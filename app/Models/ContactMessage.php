<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const SUBJECTS = [
        'Question sur un billet',
        'Problème de paiement',
        'Devenir organisateur',
        'Autre',
    ];

    protected $fillable = ['user_id', 'name', 'email', 'phone', 'subject', 'message', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
