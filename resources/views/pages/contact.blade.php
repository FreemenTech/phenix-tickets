@php
    $contact = config('billetterie.contact');
    $links = config('billetterie.links');
    $user = auth()->user();
@endphp
<x-public-layout title="Contactez-nous">
    <div class="mx-auto grid max-w-5xl gap-10 px-6 py-12 md:grid-cols-[1.4fr_1fr]">
        <div>
            <h1 class="font-display text-3xl font-bold">Contactez-nous</h1>
            <p class="mt-2 text-stone-600">Une question sur un billet, un paiement ou la vente de vos billets ? Écrivez-nous, nous répondons rapidement.</p>

            @if (session('status'))
                <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="mt-8 space-y-5">
                @csrf
                <div style="position: absolute; left: -9999px;" aria-hidden="true">
                    <label>Ne pas remplir <input name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="field-label" for="name">Votre nom</label>
                        <input class="field" id="name" name="name" value="{{ old('name', $user?->name) }}" autocomplete="name" required>
                        @error('name') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label" for="phone">Téléphone (facultatif)</label>
                        <input class="field" id="phone" name="phone" type="tel" value="{{ old('phone', $user?->phone) }}" autocomplete="tel">
                        @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="field-label" for="email">Email</label>
                    <input class="field" id="email" name="email" type="email" value="{{ old('email', $user?->email) }}" autocomplete="email" required>
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="subject">Sujet</label>
                    <select class="field" id="subject" name="subject" required>
                        @foreach (\App\Models\ContactMessage::SUBJECTS as $subject)
                            <option value="{{ $subject }}" @selected(old('subject') === $subject)>{{ $subject }}</option>
                        @endforeach
                    </select>
                    @error('subject') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="message">Votre message</label>
                    <textarea class="field" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                    <p class="mt-1 text-sm text-stone-600">Si votre message concerne une commande, indiquez sa référence (PX-...).</p>
                    @error('message') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn-flame">Envoyer le message</button>
            </form>
        </div>

        <aside class="space-y-4">
            <div class="panel">
                <h2 class="font-display text-lg font-bold">Plus rapide : WhatsApp</h2>
                <p class="mt-2 text-sm text-stone-600">Écrivez-nous directement.</p>
                <a href="https://wa.me/{{ $contact['whatsapp_number'] }}" target="_blank" rel="noopener" class="btn-flame btn-sm mt-4">{{ $contact['whatsapp_display'] }}</a>
            </div>
            <div class="panel">
                <h2 class="font-display text-lg font-bold">Suivez-nous</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ $links['instagram'] }}" target="_blank" rel="noopener" class="underline">Instagram</a></li>
                    <li><a href="{{ $links['tiktok'] }}" target="_blank" rel="noopener" class="underline">TikTok</a></li>
                </ul>
            </div>
            <p class="text-sm text-stone-600">Une question fréquente ? Consultez d'abord la <a href="{{ route('help') }}" class="underline">page Aide</a>.</p>
        </aside>
    </div>
</x-public-layout>
