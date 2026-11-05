<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpaceController extends Controller
{
    /** Aiguillage après connexion ou inscription. */
    public function home(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.home');
        }

        if ($user->isOrganizer()) {
            // Compte créé comme organisateur : direction son espace
            if ($user->role === 'organizer') {
                return redirect()->route('organizer.dashboard');
            }

            // Client qui a aussi un espace organisateur : on lui demande
            return redirect()->route('space.choose');
        }

        return redirect()->intended('/');
    }

    public function choose(Request $request): View
    {
        abort_unless($request->user()->isOrganizer(), 404);

        return view('space.choose');
    }
}
