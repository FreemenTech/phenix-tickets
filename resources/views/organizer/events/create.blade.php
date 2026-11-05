<x-dashboard-layout area="organizer" title="Créer un événement">
    <div class="mx-auto max-w-2xl px-6 py-12">
        <h1 class="font-display text-3xl font-bold">Créer un événement</h1>

        <form method="POST" action="{{ route('organizer.events.store') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
            @csrf
            <div>
                <label class="field-label" for="title">Nom de l'événement</label>
                <input class="field" id="title" name="title" value="{{ old('title') }}" required>
                @error('title') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label" for="description">Description</label>
                <textarea class="field" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                @error('description') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="venue">Lieu</label>
                    <input class="field" id="venue" name="venue" value="{{ old('venue') }}" required>
                    @error('venue') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="city">Ville</label>
                    <input class="field" id="city" name="city" value="{{ old('city') }}" required>
                    @error('city') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="starts_at">Début</label>
                    <input class="field" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}" required>
                    @error('starts_at') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="ends_at">Fin (facultatif)</label>
                    <input class="field" id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at') }}">
                    @error('ends_at') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <x-poster-picker :event="$event ?? null" />

            <div x-data="{ types: @js(old('types', [['name' => 'Standard', 'price' => '', 'quantity' => '']])) }">
                <h2 class="font-display text-xl font-bold">Catégories de billets</h2>
                <div class="mt-4 space-y-4">
                    <template x-for="(type, i) in types" :key="i">
                        <div class="grid gap-3 sm:grid-cols-[2fr_1fr_1fr_auto] sm:items-end">
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
                                <input class="field" type="number" min="1" step="1" :name="'types[' + i + '][quantity]'" x-model="type.quantity" required>
                            </div>
                            <button type="button" class="btn-outline btn-sm" x-show="types.length > 1" @click="types.splice(i, 1)">Retirer</button>
                        </div>
                    </template>
                </div>
                <button type="button" class="btn-outline btn-sm mt-4" @click="types.push({ name: '', price: '', quantity: '' })">Ajouter une catégorie</button>
                @if ($errors->has('types') || $errors->has('types.*'))
                    <p class="field-error">Vérifiez les catégories : le nom, le prix et le nombre de places sont obligatoires.</p>
                @endif
            </div>

            <button type="submit" class="btn-flame">Enregistrer l'événement</button>
        </form>
    </div>
</x-dashboard-layout>
