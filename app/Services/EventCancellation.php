<?php

namespace App\Services;

use App\Mail\EventCancelledMail;
use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EventCancellation
{
    /** Annule un événement. Retourne le nombre de commandes à rembourser et de réservations fermées. */
    public function cancel(Event $event): array
    {
        $toNotify = new Collection();
        $expired = 0;

        DB::transaction(function () use ($event, $toNotify, &$expired) {
            $event = Event::whereKey($event->id)->lockForUpdate()->first();

            if ($event->status === 'cancelled') {
                return;
            }

            $event->update(['status' => 'cancelled']);

            $orders = $event->orders()->whereIn('status', ['pending', 'paid'])->lockForUpdate()->get();

            foreach ($orders as $order) {
                $order->tickets()->update(['status' => 'cancelled']);

                if ($order->status === 'pending') {
                    $order->update(['status' => 'expired']);
                    $expired++;
                } else {
                    $order->update(['status' => 'refund_needed']);
                    $toNotify->push($order);
                }
            }

            $event->ticketTypes()->update(['sold' => 0]);
        });

        foreach ($toNotify as $order) {
            try {
                Mail::to($order->buyer_email)->send(new EventCancelledMail($order->fresh('event')));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return ['refunds' => $toNotify->count(), 'expired' => $expired];
    }
}
