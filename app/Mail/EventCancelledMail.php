<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EventCancelledMail extends Mailable
{
    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Événement annulé : {$this->order->event->title}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.event-cancelled');
    }
}
