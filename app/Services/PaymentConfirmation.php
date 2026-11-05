<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentConfirmation
{
    /**
     * Demande à FedaPay le vrai statut de la transaction liée à la commande.
     * Retourne : paid, refund_needed, failed, pending ou error.
     */
    public function confirm(Order $order): string
    {
        if ($order->isPaid()) {
            return 'paid';
        }

        if ($order->status === 'refund_needed') {
            return 'refund_needed';
        }

        if (! $order->payment_reference) {
            return 'pending';
        }

        \FedaPay\FedaPay::setApiKey(config('fedapay.secret_key'));
        \FedaPay\FedaPay::setEnvironment(config('fedapay.environment'));

        try {
            $transaction = \FedaPay\Transaction::retrieve($order->payment_reference);
        } catch (\Throwable $e) {
            report($e);

            return 'error';
        }

        Log::info('FedaPay confirmation', [
            'order' => $order->reference,
            'transaction' => $order->payment_reference,
            'status' => $transaction->status,
            'amount' => $transaction->amount,
        ]);

        if (in_array($transaction->status, ['approved', 'transferred'], true)
            && (int) $transaction->amount === $order->total) {
            $this->markPaid($order);

            return $order->fresh()->isPaid() ? 'paid' : 'refund_needed';
        }

        return in_array($transaction->status, ['declined', 'canceled'], true) ? 'failed' : 'pending';
    }

    private function markPaid(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if ($order->isPaid()) {
                return;
            }

            // Événement annulé entre-temps : le paiement reçu devra être remboursé
            if ($order->event->status === 'cancelled') {
                $order->tickets()->update(['status' => 'cancelled']);
                $order->update(['status' => 'refund_needed']);

                return;
            }

            // Réservation expirée mais paiement reçu : on re-réserve si les places existent encore
            if (in_array($order->status, ['expired', 'cancelled'], true)) {
                $counts = $order->tickets()
                    ->selectRaw('ticket_type_id, count(*) as n')
                    ->groupBy('ticket_type_id')
                    ->pluck('n', 'ticket_type_id');

                $types = TicketType::whereIn('id', $counts->keys())->lockForUpdate()->get()->keyBy('id');

                foreach ($counts as $typeId => $n) {
                    if ($types[$typeId]->remaining < $n) {
                        $order->update(['status' => 'refund_needed']);

                        return;
                    }
                }

                foreach ($counts as $typeId => $n) {
                    $types[$typeId]->increment('sold', $n);
                }
            }

            $order->update(['status' => 'paid', 'paid_at' => now()]);
            $order->tickets()->update(['status' => 'valid']);
        });
    }
}
