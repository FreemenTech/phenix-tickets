<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\Stats;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        abort_unless($organizer, 403);

        $events = $organizer->events()->with('ticketTypes')->orderBy('starts_at')->get();
        $sales = Stats::eventSales($events);
        $eventIds = $events->pluck('id')->all();

        $paidOrders = Order::whereIn('event_id', $eventIds)->where('status', 'paid');

        $chart = [
            'labels' => $sales->map(fn ($s) => Str::limit($s['event']->title, 24))->values(),
            'paid' => $sales->pluck('paid')->values(),
            'reserved' => $sales->pluck('reserved')->values(),
            'remaining' => $sales->pluck('remaining')->values(),
        ];

        return view('organizer.dashboard', [
            'organizer' => $organizer,
            'events' => $events,
            'sales' => $sales,
            'chart' => $chart,
            'daily' => Stats::daily($eventIds),
            'ticketsSold' => (int) $sales->sum('paid'),
            'capacity' => (int) $sales->sum('capacity'),
            'revenue' => (int) (clone $paidOrders)->sum('total'),
            'net' => (int) (clone $paidOrders)->sum('organizer_amount'),
        ]);
    }
}
