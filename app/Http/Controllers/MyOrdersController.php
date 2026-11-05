<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\TicketType;
use App\Services\PaymentConfirmation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MyOrdersController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->where('status', '!=', 'cancelled')
            ->with('event')
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function destroy(Order $order, PaymentConfirmation $confirmation): RedirectResponse
    {
        if ($order->isPaid() || $order->status === 'refund_needed') {
            return redirect()->route('orders.index')->with('error', 'Une commande payée ne peut pas être supprimée.');
        }

        // Un paiement a peut-être abouti sans que le site le sache : on vérifie d'abord auprès de FedaPay
        if ($order->payment_reference && $confirmation->confirm($order) === 'paid') {
            return redirect()->route('orders.show', $order->reference)
                ->with('status', 'Votre paiement a bien été reçu : cette commande ne peut pas être supprimée.');
        }

        DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if (in_array($order->status, ['paid', 'refund_needed'], true)) {
                return;
            }

            // Réservation encore active : on libère les places (une réservation expirée l'a déjà fait)
            if ($order->status === 'pending') {
                $counts = $order->tickets()
                    ->selectRaw('ticket_type_id, count(*) as n')
                    ->groupBy('ticket_type_id')
                    ->pluck('n', 'ticket_type_id');

                foreach ($counts as $typeId => $n) {
                    TicketType::whereKey($typeId)->where('sold', '>=', $n)->decrement('sold', $n);
                }

                $order->tickets()->update(['status' => 'cancelled']);
            }

            $order->update(['status' => 'cancelled']);
        });

        return redirect()->route('orders.index')->with('status', 'Commande supprimée.');
    }
}
