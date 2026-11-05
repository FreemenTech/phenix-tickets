<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizer extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'description', 'logo', 'phone',
        'payout_operator', 'payout_phone', 'commission_rate', 'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}