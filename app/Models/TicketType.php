<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    protected $fillable = [
        'event_id', 'name', 'price', 'quantity', 'sold', 'sales_start', 'sales_end',
    ];

    protected $casts = [
        'sales_start' => 'datetime',
        'sales_end' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function getRemainingAttribute(): int
    {
        return max(0, $this->quantity - $this->sold);
    }
}