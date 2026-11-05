<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function refund(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate(['reference' => ['nullable', 'string', 'max:100']]);

        if (! in_array($order->status, ['paid', 'refund_needed'], true)) {
            return back()->with('error', 'Seule une commande payée ou à rembourser peut être marquée comme remboursée.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $data) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if (! in_array($order->status, ['paid', 'refund_needed'], true)) {
                return;
            }

            // Commande payée : les places redeviennent disponibles
            if ($order->status === 'paid') {
                $counts = $order->tickets()
                    ->selectRaw('ticket_type_id, count(*) as n')
                    ->groupBy('ticket_type_id')
                    ->pluck('n', 'ticket_type_id');

                foreach ($counts as $typeId => $n) {
                    \App\Models\TicketType::whereKey($typeId)->where('sold', '>=', $n)->decrement('sold', $n);
                }
            }

            $order->tickets()->update(['status' => 'cancelled']);
            $order->forceFill([
                'status' => 'refunded',
                'refunded_at' => now(),
                'refund_reference' => $data['reference'] ?? null,
            ])->save();
        });

        return back()->with('status', 'Commande marquée comme remboursée.');
    }

    public function index(Request $request): View
    {
        $status = $request->query('status');

        $orders = Order::with('event.organizer')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->limit(100)
            ->get();

        return view('admin.sales', [
            'orders' => $orders,
            'status' => $status,
            'totals' => Order::selectRaw('status, count(*) as n, sum(total) as total')->groupBy('status')->get()->keyBy('status'),
        ]);
    }
}
