@php
    $items = $event->ticketTypes->map(function ($t) {
        $open = (! $t->sales_start || $t->sales_start->isPast()) && (! $t->sales_end || $t->sales_end->isFuture());

        return [
            'id' => $t->id,
            'name' => $t->name,
            'price' => $t->price,
            'max' => $open ? min(10, $t->remaining) : 0,
            'qty' => 0,
        ];
    })->values();
    $user = auth()->user();
@endphp
<x-public-layout :title="$event->title">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <div class="grid items-start gap-8 md:grid-cols-2">
            <div class="md:sticky md:top-6">
                @if ($event->cover_image)
                    <img src="{{ asset('storage/'.$event->cover_image) }}" alt="Affiche de {{ $event->title }}" class="mx-auto max-h-[85vh] w-auto max-w-full rounded-3xl">
                @else
                    <div class="hero flex aspect-[4/5] items-end rounded-3xl p-6">
                        <span class="font-display text-3xl font-bold">{{ $event->title }}</span>
                    </div>
                @endif
            </div>

            <div>
                <h1 class="font-display text-3xl font-extrabold leading-tight md:text-4xl">{{ $event->title }}</h1>
                <p class="mt-3 text-lg text-stone-600">{{ $event->starts_at->translatedFormat('l j F Y, H\hi') }}</p>
                <p class="text-stone-600">{{ $event->venue }}, {{ $event->city }}</p>
                <p class="mt-1 text-sm text-stone-500">Organisé par {{ $event->organizer->name }}</p>

                <div class="mt-6 rounded-3xl bg-white p-6">
                    <h2 class="font-display text-xl font-bold">Vos billets</h2>

                    @if ($event->starts_at->isPast())
                        <p class="mt-4 text-stone-600">Cet événement est terminé.</p>
                    @else
                        @guest
                            <div class="mt-4 divide-y divide-stone-200">
                                @foreach ($event->ticketTypes as $type)
                                    <div class="flex justify-between py-3">
                                        <span class="font-semibold">{{ $type->name }}</span>
                                        <span class="text-stone-600">{{ number_format($type->price, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-4 text-sm text-stone-600">Connectez-vous ou créez un compte pour acheter vos billets.</p>
                            <div class="mt-4 flex flex-wrap gap-3">
                                <a href="{{ route('events.login', $event->slug) }}" class="btn-flame">Se connecter</a>
                                <a href="{{ route('events.register', $event->slug) }}" class="btn-outline">Créer un compte</a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('orders.store', $event->slug) }}"
                                  x-data="{
                                      items: @js($items),
                                      get total() { return this.items.reduce((s, i) => s + i.price * i.qty, 0) },
                                      get count() { return this.items.reduce((s, i) => s + i.qty, 0) },
                                      money(v) { return new Intl.NumberFormat('fr-FR').format(v) + ' FCFA' }
                                  }">
                                @csrf

                                <div class="mt-4 divide-y divide-stone-200">
                                    <template x-for="item in items" :key="item.id">
                                        <div class="flex items-center justify-between gap-4 py-3">
                                            <div>
                                                <p class="font-semibold" x-text="item.name"></p>
                                                <p class="text-sm text-stone-600" x-text="money(item.price)"></p>
                                            </div>
                                            <div>
                                                <span class="text-sm text-stone-500" x-show="item.max === 0">Indisponible</span>
                                                <div class="flex items-center gap-3" x-show="item.max > 0">
                                                    <button type="button" class="btn-outline btn-sm" aria-label="Retirer un billet" :disabled="item.qty === 0" @click="item.qty = Math.max(0, item.qty - 1)">−</button>
                                                    <span class="w-6 text-center font-semibold" x-text="item.qty"></span>
                                                    <button type="button" class="btn-outline btn-sm" aria-label="Ajouter un billet" :disabled="item.qty >= item.max" @click="item.qty = Math.min(item.max, item.qty + 1)">+</button>
                                                </div>
                                                <input type="hidden" :name="'quantities[' + item.id + ']'" :value="item.qty">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                @error('quantities') <p class="field-error">{{ $message }}</p> @enderror

                                <div class="mt-4 flex justify-between border-t border-stone-200 pt-4 font-bold">
                                    <span>Total</span>
                                    <span x-text="money(total)"></span>
                                </div>

                                <div class="mt-5 space-y-3">
                                    <div>
                                        <label class="field-label" for="buyer_name">Votre nom</label>
                                        <input class="field" id="buyer_name" name="buyer_name" value="{{ old('buyer_name', $user->name) }}" autocomplete="name" required>
                                        @error('buyer_name') <p class="field-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="buyer_email">Email (pour recevoir vos billets)</label>
                                        <input class="field" id="buyer_email" name="buyer_email" type="email" value="{{ old('buyer_email', $user->email) }}" autocomplete="email" required>
                                        @error('buyer_email') <p class="field-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="buyer_phone">Téléphone</label>
                                        <input class="field" id="buyer_phone" name="buyer_phone" type="tel" value="{{ old('buyer_phone', $user->phone) }}" autocomplete="tel" required>
                                        @error('buyer_phone') <p class="field-error">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn-flame mt-5 w-full" :disabled="count === 0">Continuer vers le paiement</button>
                            </form>
                        @endguest
                    @endif
                </div>
            </div>
        </div>

        @if ($event->description)
            <section class="mt-12 max-w-prose">
                <h2 class="font-display text-xl font-bold">À propos</h2>
                <p class="mt-3 whitespace-pre-line">{{ $event->description }}</p>
            </section>
        @endif
    </div>
</x-public-layout>
