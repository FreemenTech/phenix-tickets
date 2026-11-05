<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Support\TicketPdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function show(string $code): View
    {
        $ticket = Ticket::where('code', $code)
            ->with('ticketType', 'order.event')
            ->firstOrFail();

        return view('tickets.show', compact('ticket'));
    }

    public function orderPdf(Order $order): Response
    {
        abort_unless($order->isPaid(), 404);

        return response(TicketPdf::render($order), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="billets-'.$order->reference.'.pdf"',
        ]);
    }
}
