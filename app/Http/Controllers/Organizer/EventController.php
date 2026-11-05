<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EventController extends Controller
{
    public function create(): View
    {
        return view('organizer.events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $organizer = $request->user()->organizer;
        abort_unless($organizer, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'venue' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'poster' => ['nullable', 'image', 'max:5120'],
            'cover_focus_x' => ['nullable', 'integer', 'between:0,100'],
            'cover_focus_y' => ['nullable', 'integer', 'between:0,100'],
            'types' => ['required', 'array', 'min:1'],
            'types.*.name' => ['required', 'string', 'max:100'],
            'types.*.price' => ['required', 'integer', 'min:0'],
            'types.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($request, $data, $organizer) {
            $slug = Str::slug($data['title']) ?: 'evenement';
            if (Event::where('slug', $slug)->exists()) {
                $slug .= '-'.Str::lower(Str::random(5));
            }

            $event = Event::create([
                'organizer_id' => $organizer->id,
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'venue' => $data['venue'],
                'city' => $data['city'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'] ?? null,
                'cover_focus_x' => (int) ($data['cover_focus_x'] ?? 50),
                'cover_focus_y' => (int) ($data['cover_focus_y'] ?? 50),
                'cover_image' => $request->hasFile('poster')
                    ? $request->file('poster')->store('posters', 'public')
                    : null,
                'status' => $organizer->isApproved() ? 'published' : 'draft',
            ]);

            foreach ($data['types'] as $type) {
                $event->ticketTypes()->create([
                    'name' => $type['name'],
                    'price' => $type['price'],
                    'quantity' => $type['quantity'],
                ]);
            }
        });

        return redirect()->route('organizer.dashboard')->with(
            'status',
            $organizer->isApproved()
                ? 'Événement publié.'
                : 'Événement enregistré en brouillon, en attente de validation de votre compte.'
        );
    }

    public function edit(Request $request, Event $event): View
    {
        $this->authorizeOwner($request, $event);

        $event->load('ticketTypes');

        return view('organizer.events.edit', [
            'event' => $event,
            'organizer' => $request->user()->organizer,
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeOwner($request, $event);

        $organizer = $request->user()->organizer;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'venue' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'status' => ['required', 'in:draft,published'],
            'poster' => ['nullable', 'image', 'max:5120'],
            'cover_focus_x' => ['nullable', 'integer', 'between:0,100'],
            'cover_focus_y' => ['nullable', 'integer', 'between:0,100'],
            'types' => ['required', 'array', 'min:1'],
            'types.*.id' => ['nullable', 'integer'],
            'types.*.name' => ['required', 'string', 'max:100'],
            'types.*.price' => ['required', 'integer', 'min:0'],
            'types.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $oldCover = null;

        DB::transaction(function () use ($request, $event, $data, $organizer, &$oldCover) {
            $payload = [
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'venue' => $data['venue'],
                'city' => $data['city'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'] ?? null,
                'cover_focus_x' => (int) ($data['cover_focus_x'] ?? $event->cover_focus_x ?? 50),
                'cover_focus_y' => (int) ($data['cover_focus_y'] ?? $event->cover_focus_y ?? 50),
                // Un organisateur non validé ne peut pas publier
                'status' => $organizer->isApproved() ? $data['status'] : 'draft',
            ];

            if ($request->hasFile('poster')) {
                $oldCover = $event->cover_image;
                $payload['cover_image'] = $request->file('poster')->store('posters', 'public');
            }

            $event->update($payload);

            $existing = $event->ticketTypes()->get()->keyBy('id');
            $submittedIds = collect($data['types'])->pluck('id')->filter()->map(fn ($id) => (int) $id);

            // Catégories retirées du formulaire : supprimées seulement si aucune vente
            foreach ($existing as $id => $type) {
                if (! $submittedIds->contains($id)) {
                    if ($type->sold > 0 || $type->tickets()->exists()) {
                        throw ValidationException::withMessages([
                            'types' => "« {$type->name} » a déjà des ventes : elle ne peut pas être supprimée.",
                        ]);
                    }
                    $type->delete();
                }
            }

            foreach ($data['types'] as $row) {
                $row['id'] = $row['id'] ?? null;

                if ($row['id']) {
                    $type = $existing->get((int) $row['id']);
                    abort_unless($type, 422);

                    if ((int) $row['quantity'] < $type->sold) {
                        throw ValidationException::withMessages([
                            'types' => "« {$type->name} » : le nombre de places ne peut pas être inférieur aux {$type->sold} billets déjà vendus ou réservés.",
                        ]);
                    }

                    $type->update([
                        'name' => $row['name'],
                        'price' => $row['price'],
                        'quantity' => $row['quantity'],
                    ]);
                } else {
                    $event->ticketTypes()->create([
                        'name' => $row['name'],
                        'price' => $row['price'],
                        'quantity' => $row['quantity'],
                    ]);
                }
            }
        });

        if ($oldCover) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()->route('organizer.dashboard')->with('status', 'Événement mis à jour.');
    }

    public function destroy(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeOwner($request, $event);

        if ($event->orders()->whereIn('status', ['paid', 'refund_needed', 'refunded'])->exists()) {
            return redirect()->route('organizer.dashboard')->with(
                'error',
                'Cet événement a déjà des billets vendus : il ne peut pas être supprimé. Utilisez « Annuler l\'événement » pour le retirer et rembourser les acheteurs.'
            );
        }

        $cover = $event->cover_image;
        $event->delete();

        if ($cover) {
            Storage::disk('public')->delete($cover);
        }

        return redirect()->route('organizer.dashboard')->with('status', 'Événement supprimé.');
    }

    public function cancel(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeOwner($request, $event);

        if ($event->status === 'cancelled') {
            return redirect()->route('organizer.events.index')->with('error', 'Cet événement est déjà annulé.');
        }

        $result = app(\App\Services\EventCancellation::class)->cancel($event);

        return redirect()->route('organizer.events.index')->with(
            'status',
            "Événement annulé. {$result['refunds']} commande(s) à rembourser : les acheteurs ont été prévenus par email et notre équipe traite les remboursements."
        );
    }

    private function authorizeOwner(Request $request, Event $event): void
    {
        abort_unless($event->organizer->user_id === $request->user()->id, 404);

        abort_if(
            $event->status === 'cancelled' && $request->routeIs('organizer.events.edit', 'organizer.events.update'),
            403,
            'Cet événement est annulé et ne peut plus être modifié.'
        );
    }
}
