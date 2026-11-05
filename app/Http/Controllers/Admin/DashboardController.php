<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\Ticket;
use App\Support\Stats;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $paid = Order::where('status', 'paid');

        $top = Event::with('organizer')
            ->withSum(['orders as revenue' => fn ($q) => $q->where('status', 'paid')], 'total')
            ->withCount(['orders as paid_orders' => fn ($q) => $q->where('status', 'paid')])
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'revenue' => (int) (clone $paid)->sum('total'),
            'commission' => (int) (clone $paid)->sum('commission_amount'),
            'ticketsSold' => Ticket::whereHas('order', fn ($q) => $q->where('status', 'paid'))->count(),
            'pendingOrganizers' => Organizer::where('status', 'pending')->count(),
            'flagged' => Order::where('status', 'refund_needed')->count(),
            'daily' => Stats::daily(null),
            'top' => $top,
        ]);
    }
}
