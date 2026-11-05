<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use App\Support\Stats;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizerController extends Controller
{
    public function index(): View
    {
        return view('admin.organizers', [
            'organizers' => Organizer::with('user')->withCount('events')->latest()->get(),
            'balances' => Stats::organizerBalances(),
        ]);
    }

    public function approve(Organizer $organizer): RedirectResponse
    {
        $organizer->update(['status' => 'approved']);
        $organizer->events()->where('status', 'draft')->update(['status' => 'published']);

        return back()->with('status', "« {$organizer->name} » est approuvé : ses événements en brouillon sont publiés.");
    }

    public function suspend(Organizer $organizer): RedirectResponse
    {
        $organizer->update(['status' => 'suspended']);
        $organizer->events()->where('status', 'published')->update(['status' => 'draft']);

        return back()->with('status', "« {$organizer->name} » est suspendu : ses événements sont retirés du site.");
    }
}
