<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        // Anti-spam : champ caché que seuls les robots remplissent
        if ($request->filled('website')) {
            return redirect()->route('contact')->with('status', 'Merci ! Votre message a bien été envoyé.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', Rule::in(ContactMessage::SUBJECTS)],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        ContactMessage::create($data + ['user_id' => $request->user()?->id]);

        return redirect()->route('contact')->with('status', 'Merci ! Votre message a bien été envoyé, nous vous répondons rapidement.');
    }
}
