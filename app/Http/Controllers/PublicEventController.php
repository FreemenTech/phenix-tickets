<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class PublicEventController extends Controller
{
    public function show(Event $event): View
    {
        abort_unless($event->status === 'published', 404);

        $event->load('organizer', 'ticketTypes');

        return view('events.show', compact('event'));
    }
}
