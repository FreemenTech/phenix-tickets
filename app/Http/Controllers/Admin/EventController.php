<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventCancellation;
use App\Support\Stats;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::with(['organizer', 'ticketTypes'])
            ->withCount(['orders as to_refund' => fn ($q) => $q->where('status', 'refund_needed')])
            ->orderByDesc('starts_at')
            ->get();

        return view('admin.events', ['sales' => Stats::eventSales($events)]);
    }

    public function cancel(Event $event, EventCancellation $cancellation): RedirectResponse
    {
        if ($event->status === 'cancelled') {
            return back()->with('error', 'Cet événement est déjà annulé.');
        }

        $result = $cancellation->cancel($event);

        return back()->with(
            'status',
            "Événement annulé : {$result['refunds']} commande(s) à rembourser, {$result['expired']} réservation(s) non payée(s) fermée(s). Les acheteurs ont été prévenus par email."
        );
    }
}
