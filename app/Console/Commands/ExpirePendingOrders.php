<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\TicketType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Libère les billets réservés dont la commande n\'a pas été payée à temps';

    public function handle(): int
    {
        $count = 0;

        // 15 min sans tentative de paiement, 60 min si le client a ouvert la page de paiement
        $ids = Order::where('status', 'pending')
            ->where(function ($query) {
                $query->where(fn ($q) => $q->whereNull('payment_reference')->where('created_at', '<', now()->subMinutes(15)))
                    ->orWhere(fn ($q) => $q->whereNotNull('payment_reference')->where('created_at', '<', now()->subMinutes(60)));
            })
            ->pluck('id');

        foreach ($ids as $id) {
            DB::transaction(function () use ($id, &$count) {
                $order = Order::whereKey($id)->lockForUpdate()->first();

                if (! $order || $order->status !== 'pending') {
                    return;
                }

                $counts = $order->tickets()
                    ->selectRaw('ticket_type_id, count(*) as n')
                    ->groupBy('ticket_type_id')
                    ->pluck('n', 'ticket_type_id');

                foreach ($counts as $typeId => $n) {
                    TicketType::whereKey($typeId)->where('sold', '>=', $n)->decrement('sold', $n);
                }

                $order->tickets()->update(['status' => 'cancelled']);
                $order->update(['status' => 'expired']);

                $count++;
            });
        }

        $this->info("{$count} commande(s) expirée(s).");

        return self::SUCCESS;
    }
}
