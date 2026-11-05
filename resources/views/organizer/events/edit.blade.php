@php
    $typesData = old('types', $event->ticketTypes->map(fn ($t) => [
        'id' => $t->id,
        'name' => $t->name,
        'price' => $t->price,
        'quantity' => $t->quantity,
        'sold' => $t->sold,
    ])->values()->all());
@endphp
<x-dashboard-layout area="organizer" title="Modifier l'événement">
    <div class="mx-auto max-w-2xl px-6 py-12">
        <h1 class="font-display text-3xl font-bold">Modifier l'événement</h1>

        <form method="POST" action="{{ route('organizer.events.update', $event->id) }}" enctype="multipart/form-data" class="mt-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="field-label" for="title">Nom de l'événement</label>
                <input class="field" id="title" name="title" value="{{ old('title', $event->title) }}" required>
                @error('title') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label" for="description">Description</label>
                <textarea class="field" id="description" name="description" rows="4">{{ old('description', $event->description) }}</textarea>
                @error('description') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="venue">Lieu</label>
                    <input class="field" id="venue" name="venue" value="{{ old('venue', $event->venue) }}" required>
                    @error('venue') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="city">Ville</label>
                    <input class="field" id="city" name="city" value="{{ old('city', $event->city) }}" required>
                    @error('city') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="starts_at">Début</label>
                    <input class="field" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', $event->starts_at->format('Y-m-d\TH:i')) }}" required>
                    @error('starts_at') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="ends_at">Fin (facultatif)</label>
                    <input class="field" id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}">
                    @error('ends_at') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <x-poster-picker :event="$event ?? null" />

            <div>
                <label class="field-label" for="status">Visibilité</label>
                @if ($organizer->isApproved())
                    <select class="field" id="status" name="status">
                        <option value="published" @selected(old('status', $event->status) === 'published')>Publié (visible sur le site)</option>
                        <option value="draft" @selected(old('status', $event->status) === 'draft')>Brouillon (masqué)</option>
                    </select>
                @else
                    <input type="hidden" name="status" value="draft">
                    <p class="text-sm text-stone-600">Votre compte est en cours de validation : l'événement reste en brouillon.</p>
                @endif
                @error('status') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div x-data="{ types: @js($typesData) }">
                <h2 class="font-display text-xl font-bold">Catégories de billets</h2>
                <div class="mt-4 space-y-4">
                    <template x-for="(type, i) in types" :key="i">
                        <div>
                            <div class="grid gap-3 sm:grid-cols-[2fr_1fr_1fr_auto] sm:items-end">
                                <input type="hidden" :name="'types[' + i + '][id]'" :value="type.id || ''">
                                <div>
                                    <label class="field-label">Catégorie</label>
                                    <input class="field" :name="'types[' + i + '][name]'" x-model="type.name" required>
                                </div>
                                <div>
                                    <label class="field-label">Prix (FCFA)</label>
                                    <input class="field" type="number" min="0" step="1" :name="'types[' + i + '][price]'" x-model="type.price" required>
                                </div>
                                <div>
                                    <label class="field-label">Places</label>
                                    <input class="field" type="number" step="1" :min="type.sold || 1" :name="'types[' + i + '][quantity]'" x-model="type.quantity" required>
                                </div>
                                <button type="button" class="btn-outline btn-sm" x-show="types.length > 1" :disabled="type.sold > 0" @click="types.splice(i, 1)">Retirer</button>
                            </div>
                            <p class="mt-1 text-xs text-stone-500" x-show="type.sold > 0" x-text="type.sold + ' billet(s) vendu(s) ou réservé(s) : cette catégorie ne peut pas être retirée.'"></p>
                        </div>
                    </template>
                </div>
                <button type="button" class="btn-outline btn-sm mt-4" @click="types.push({ id: '', name: '', price: '', quantity: '', sold: 0 })">Ajouter une catégorie</button>
                @error('types') <p class="field-error">{{ $message }}</p> @enderror
                @if ($errors->has('types.*'))
                    <p class="field-error">Vérifiez les catégories : le nom, le prix et le nombre de places sont obligatoires.</p>
                @endif
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="btn-flame">Enregistrer les modifications</button>
                <a href="{{ route('organizer.dashboard') }}" class="btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</x-dashboard-layout>
