<?php

namespace App\Support;

use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\Payout;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Payouts
{
    public static function delayDays(): int
    {
        return max(0, (int) config('billetterie.payout_delay_days', 3));
    }

    /** À partir de cette date, les ventes d'un événement peuvent être reversées. */
    public static function releaseDate(Event $event): Carbon
    {
        return ($event->ends_at ?? $event->starts_at)->copy()->addDays(self::delayDays());
    }

    /**
     * Pour chaque organisateur :
     * net (sa part des ventes payées), paid_out (déjà versé), due (reste dû),
     * payable (versable maintenant, événements terminés) et waiting (en attente de la fin des événements).
     */
    public static function balances(): array
    {
        $threshold = now()->subDays(self::delayDays())->toDateTimeString();

        $paidOrders = fn () => Order::query()
            ->join('events', 'events.id', '=', 'orders.event_id')
            ->where('orders.status', 'paid')
            ->selectRaw('events.organizer_id as oid, sum(orders.organizer_amount) as total')
            ->groupBy('events.organizer_id');

        $net = $paidOrders()->pluck('total', 'oid');

        $released = $paidOrders()
            ->whereRaw('COALESCE(events.ends_at, events.starts_at) <= ?', [$threshold])
            ->pluck('total', 'oid');

        $out = Payout::query()
            ->where('status', 'paid')
            ->selectRaw('organizer_id as oid, sum(amount) as total')
            ->groupBy('organizer_id')
            ->pluck('total', 'oid');

        $result = [];

        foreach ($net->keys()->merge($out->keys())->unique() as $id) {
            $n = (int) ($net[$id] ?? 0);
            $r = (int) ($released[$id] ?? 0);
            $o = (int) ($out[$id] ?? 0);
            $payable = max(0, $r - $o);

            $result[$id] = [
                'net' => $n,
                'paid_out' => $o,
                'due' => $n - $o,
                'payable' => $payable,
                'waiting' => max(0, ($n - $o) - $payable),
                'recover' => max(0, $o - $n),
            ];
        }

        return $result;
    }

    public static function balance(int $organizerId): array
    {
        return self::balances()[$organizerId]
            ?? ['net' => 0, 'paid_out' => 0, 'due' => 0, 'payable' => 0, 'waiting' => 0];
    }

    /** Pour un organisateur : sa part par événement et la date de versement prévue. */
    public static function byEvent(Organizer $organizer): Collection
    {
        return $organizer->events()
            ->orderByDesc('starts_at')
            ->withSum(['orders as net' => fn ($q) => $q->where('status', 'paid')], 'organizer_amount')
            ->get()
            ->filter(fn ($event) => (int) $event->net > 0)
            ->map(fn ($event) => [
                'event' => $event,
                'net' => (int) $event->net,
                'release' => self::releaseDate($event),
                'released' => self::releaseDate($event)->isPast(),
            ])
            ->values();
    }
}
