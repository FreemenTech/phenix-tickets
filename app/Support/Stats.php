<?php

namespace App\Support;

use App\Models\Event;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Ticket;
use Illuminate\Support\Collection;

class Stats
{
    /** Pour chaque événement : places, billets payés, réservés (non payés) et restants. */
    public static function eventSales(Collection $events): Collection
    {
        $typeIds = $events->flatMap(fn ($event) => $event->ticketTypes->pluck('id'));

        $paidByType = Ticket::query()
            ->join('orders', 'orders.id', '=', 'tickets.order_id')
            ->where('orders.status', 'paid')
            ->whereIn('tickets.ticket_type_id', $typeIds)
            ->selectRaw('tickets.ticket_type_id as type_id, count(*) as n')
            ->groupBy('tickets.ticket_type_id')
            ->pluck('n', 'type_id');

        return $events->map(function ($event) use ($paidByType) {
            $capacity = (int) $event->ticketTypes->sum('quantity');
            $paid = (int) $event->ticketTypes->sum(fn ($type) => (int) ($paidByType[$type->id] ?? 0));
            $reserved = max(0, (int) $event->ticketTypes->sum('sold') - $paid);

            return [
                'event' => $event,
                'capacity' => $capacity,
                'paid' => $paid,
                'reserved' => $reserved,
                'remaining' => max(0, $capacity - $paid - $reserved),
            ];
        })->values();
    }

    /** Détail par catégorie de billet d'un événement. */
    public static function typeBreakdown(Event $event): Collection
    {
        $event->loadMissing('ticketTypes');

        $rows = Ticket::query()
            ->join('orders', 'orders.id', '=', 'tickets.order_id')
            ->where('orders.status', 'paid')
            ->whereIn('tickets.ticket_type_id', $event->ticketTypes->pluck('id'))
            ->selectRaw("tickets.ticket_type_id as type_id, count(*) as paid, sum(case when tickets.status = 'used' then 1 else 0 end) as used")
            ->groupBy('tickets.ticket_type_id')
            ->get()
            ->keyBy('type_id');

        return $event->ticketTypes->map(function ($type) use ($rows) {
            $row = $rows->get($type->id);
            $paid = (int) ($row->paid ?? 0);

            return [
                'type' => $type,
                'paid' => $paid,
                'used' => (int) ($row->used ?? 0),
                'reserved' => max(0, $type->sold - $paid),
                'remaining' => max(0, $type->quantity - $type->sold),
            ];
        })->values();
    }

    /** Ventes par jour sur les N derniers jours ($eventIds = null : tous les événements). */
    public static function daily(?array $eventIds, int $days = 14): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $orders = Order::query()
            ->when($eventIds !== null, fn ($query) => $query->whereIn('event_id', $eventIds))
            ->where('status', 'paid')
            ->where('paid_at', '>=', $start)
            ->withCount('tickets')
            ->get();

        $byDay = $orders->groupBy(fn ($order) => $order->paid_at->format('Y-m-d'));

        $labels = [];
        $tickets = [];
        $revenue = [];
        $commission = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $set = $byDay->get($day->format('Y-m-d'), collect());

            $labels[] = $day->translatedFormat('j M');
            $tickets[] = (int) $set->sum('tickets_count');
            $revenue[] = (int) $set->sum('total');
            $commission[] = (int) $set->sum('commission_amount');
        }

        return compact('labels', 'tickets', 'revenue', 'commission');
    }

    /** Pour chaque organisateur : net gagné, déjà versé, reste à verser. */
    public static function organizerBalances(): array
    {
        $net = Order::query()
            ->join('events', 'events.id', '=', 'orders.event_id')
            ->where('orders.status', 'paid')
            ->selectRaw('events.organizer_id as oid, sum(orders.organizer_amount) as total')
            ->groupBy('events.organizer_id')
            ->pluck('total', 'oid');

        $out = Payout::query()
            ->where('status', 'paid')
            ->selectRaw('organizer_id as oid, sum(amount) as total')
            ->groupBy('organizer_id')
            ->pluck('total', 'oid');

        $result = [];

        foreach ($net->keys()->merge($out->keys())->unique() as $id) {
            $n = (int) ($net[$id] ?? 0);
            $o = (int) ($out[$id] ?? 0);
            $result[$id] = ['net' => $n, 'paid_out' => $o, 'due' => $n - $o];
        }

        return $result;
    }

    public static function organizerBalance(int $organizerId): array
    {
        return self::organizerBalances()[$organizerId] ?? ['net' => 0, 'paid_out' => 0, 'due' => 0];
    }
}
