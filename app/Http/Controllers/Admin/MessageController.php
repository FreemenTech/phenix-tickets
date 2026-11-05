<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        return view('admin.messages', [
            'messages' => ContactMessage::orderByRaw('read_at is null desc')->latest()->limit(100)->get(),
        ]);
    }

    public function markRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => now()]);

        return back();
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('status', 'Message supprimé.');
    }
}
