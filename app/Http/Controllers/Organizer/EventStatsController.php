<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Support\Stats;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventStatsController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        abort_unless($organizer, 403);

        $events = $organizer->events()->with('ticketTypes')->orderByDesc('starts_at')->get();

        return view('organizer.events.index', [
            'organizer' => $organizer,
            'sales' => Stats::eventSales($events),
        ]);
    }

    public function show(Request $request, Event $event): View
    {
        abort_unless($event->organizer->user_id === $request->user()->id, 404);

        $event->load('ticketTypes');

        $breakdown = Stats::typeBreakdown($event);
        $paidOrders = $event->orders()->where('status', 'paid');

        return view('organizer.events.stats', [
            'event' => $event,
            'breakdown' => $breakdown,
            'daily' => Stats::daily([$event->id]),
            'revenue' => (int) (clone $paidOrders)->sum('total'),
            'net' => (int) (clone $paidOrders)->sum('organizer_amount'),
            'recent' => $event->orders()->where('status', 'paid')->withCount('tickets')->latest('paid_at')->limit(10)->get(),
        ]);
    }
}
