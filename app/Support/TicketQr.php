<?php

namespace App\Support;

use App\Models\Ticket;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class TicketQr
{
    private static function build(Ticket $ticket)
    {
        return (new Builder(
            writer: new PngWriter(),
            data: route('tickets.show', $ticket->code),
            size: 300,
            margin: 10,
        ))->build();
    }

    /** QR code en data URI (pour le site et le PDF). */
    public static function dataUri(Ticket $ticket): string
    {
        return self::build($ticket)->getDataUri();
    }

    /** QR code en image PNG brute (pour l'intégrer à un email). */
    public static function png(Ticket $ticket): string
    {
        return self::build($ticket)->getString();
    }
}
