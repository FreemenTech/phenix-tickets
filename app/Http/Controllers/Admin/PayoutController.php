<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use App\Models\Payout;
use App\Support\Payouts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(): View
    {
        return view('admin.payouts', [
            'organizers' => Organizer::orderBy('name')->get(),
            'balances' => Payouts::balances(),
            'payouts' => Payout::with('organizer')->latest()->limit(50)->get(),
            'delay' => Payouts::delayDays(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'organizer_id' => ['required', 'exists:organizers,id'],
            'amount' => ['required', 'integer', 'min:1'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        $organizer = Organizer::findOrFail($data['organizer_id']);
        $payable = Payouts::balance($organizer->id)['payable'];

        if ($data['amount'] > $payable) {
            return back()->withInput()->withErrors([
                'payout' => 'Le montant dépasse ce qui peut être versé à « '.$organizer->name.' » maintenant ('
                    .number_format($payable, 0, ',', ' ').' FCFA). Le reste sera disponible après la fin de ses événements.',
            ]);
        }

        Payout::create([
            'organizer_id' => $organizer->id,
            'amount' => $data['amount'],
            'reference' => $data['reference'] ?? null,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('status', 'Versement enregistré.');
    }
}
