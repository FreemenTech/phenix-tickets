<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Support\Payouts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        abort_unless($organizer, 403);

        return view('organizer.payouts', [
            'organizer' => $organizer,
            'balance' => Payouts::balance($organizer->id),
            'events' => Payouts::byEvent($organizer),
            'payouts' => $organizer->payouts()->latest()->get(),
            'delay' => Payouts::delayDays(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $organizer = $request->user()->organizer;
        abort_unless($organizer, 403);

        $data = $request->validate([
            'payout_operator' => ['required', 'in:MTN,Moov'],
            'payout_phone' => ['required', 'string', 'max:30'],
        ]);

        $organizer->update($data);

        return back()->with('status', 'Informations de reversement enregistrées.');
    }
}
