<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        abort_unless($event->status === 'published' && $event->starts_at->isFuture(), 404);

        $data = $request->validate([
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_email' => ['required', 'email', 'max:255'],
            'buyer_phone' => ['required', 'string', 'max:30'],
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        $order = DB::transaction(function () use ($data, $event, $request) {
            $quantities = collect($data['quantities'])
                ->map(fn ($q) => (int) $q)
                ->filter(fn ($q) => $q > 0);

            if ($quantities->isEmpty()) {
                throw ValidationException::withMessages(['quantities' => 'Choisissez au moins un billet.']);
            }

            // Verrou sur les lignes de stock pour éviter la survente
            $types = TicketType::where('event_id', $event->id)
                ->whereIn('id', $quantities->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            foreach ($quantities as $typeId => $qty) {
                $type = $types->get($typeId);

                if (! $type) {
                    throw ValidationException::withMessages(['quantities' => 'Catégorie de billet invalide.']);
                }

                $open = (! $type->sales_start || $type->sales_start->isPast())
                    && (! $type->sales_end || $type->sales_end->isFuture());

                if (! $open || $type->remaining < $qty) {
                    throw ValidationException::withMessages([
                        'quantities' => "« {$type->name} » n'est plus disponible en quantité demandée.",
                    ]);
                }

                $total += $type->price * $qty;
            }

            $commission = (int) round($total * $event->organizer->commission_rate / 100);

            $order = Order::create([
                'reference' => 'PX-'.strtoupper(Str::random(10)),
                'event_id' => $event->id,
                'user_id' => $request->user()?->id,
                'buyer_name' => $data['buyer_name'],
                'buyer_email' => $data['buyer_email'],
                'buyer_phone' => $data['buyer_phone'],
                'total' => $total,
                'commission_amount' => $commission,
                'organizer_amount' => $total - $commission,
                'status' => 'pending',
            ]);

            foreach ($quantities as $typeId => $qty) {
                $type = $types->get($typeId);
                $type->increment('sold', $qty);

                for ($i = 0; $i < $qty; $i++) {
                    $order->tickets()->create([
                        'ticket_type_id' => $type->id,
                        'code' => strtoupper(Str::random(12)),
                        'holder_name' => $data['buyer_name'],
                        'status' => 'pending',
                    ]);
                }
            }

            // Billets gratuits : pas de paiement nécessaire
            if ($total === 0) {
                $order->update(['status' => 'paid', 'paid_at' => now()]);
                $order->tickets()->update(['status' => 'valid']);
            }

            return $order;
        });

        return redirect()->route('orders.show', $order->reference);
    }

    public function show(Order $order): View
    {
        $order->load('event', 'tickets.ticketType');

        return view('orders.show', compact('order'));
    }
}
