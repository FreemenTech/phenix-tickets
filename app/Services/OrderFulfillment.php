<?php

namespace App\Services;

use App\Mail\TicketsMail;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class OrderFulfillment
{
    /** Envoie les billets par email. Ne lève jamais d'exception : un souci d'email ne doit pas casser un paiement. */
    public function sendTickets(Order $order): void
    {
        try {
            $order = $order->fresh(['event', 'tickets.ticketType']);

            Mail::to($order->buyer_email)->send(new TicketsMail($order));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
