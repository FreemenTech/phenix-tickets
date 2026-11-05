<?php

namespace App\Models;

use App\Services\OrderFulfillment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $fillable = [
        'reference', 'event_id', 'user_id', 'buyer_name', 'buyer_email', 'buyer_phone',
        'total', 'commission_amount', 'organizer_amount', 'status',
        'payment_provider', 'payment_reference', 'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Dès qu'une commande passe en « payée », on envoie les billets (après validation de la transaction en base)
        static::updated(function (Order $order) {
            if ($order->wasChanged('status') && $order->status === 'paid') {
                DB::afterCommit(fn () => app(OrderFulfillment::class)->sendTickets($order));
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
