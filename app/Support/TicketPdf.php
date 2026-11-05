<?php

namespace App\Support;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketPdf
{
    /** Génère le PDF (une page par billet) et retourne son contenu binaire. */
    public static function render(Order $order): string
    {
        $order->loadMissing('event.organizer', 'tickets.ticketType');

        $items = $order->tickets->map(fn ($ticket) => [
            'ticket' => $ticket,
            'qr' => TicketQr::dataUri($ticket),
        ]);

        return Pdf::loadView('pdf.tickets', [
            'order' => $order,
            'items' => $items,
            'card' => TicketPoster::card($order->event->cover_image, focusX: (int) $order->event->cover_focus_x, focusY: (int) $order->event->cover_focus_y),
        ])->setPaper('a4')->output();
    }
}
