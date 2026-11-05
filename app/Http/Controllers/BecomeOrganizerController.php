<?php

namespace App\Http\Controllers;

use App\Models\Organizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BecomeOrganizerController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->isOrganizer()) {
            return redirect()->route('organizer.dashboard');
        }

        return view('organizer.become');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isOrganizer()) {
            return redirect()->route('organizer.dashboard');
        }

        $data = $request->validate([
            'structure_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'conditions' => ['accepted'],
        ]);

        $slug = Str::slug($data['structure_name']) ?: 'structure';
        if (Organizer::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(4));
        }

        Organizer::create([
            'user_id' => $user->id,
            'name' => $data['structure_name'],
            'slug' => $slug,
            'phone' => $data['phone'],
            'status' => 'pending',
        ]);

        return redirect()->route('organizer.dashboard');
    }
}
