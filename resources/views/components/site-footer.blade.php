@php
    $links = config('billetterie.links');
    $contact = config('billetterie.contact');
    $sellUrl = auth()->check() ? route('organizer.become') : route('organizer.register');
@endphp
<footer class="site-header">
    <div class="mx-auto grid max-w-6xl gap-10 px-6 py-10 md:grid-cols-3">
        <div>
            <img src="{{ asset('images/phenix-logo.png') }}" alt="Phenix Agency" class="h-24 w-auto">
            <p class="mt-4 max-w-xs text-sm text-white/70">Billetterie en ligne au Bénin : achetez vos billets en Mobile Money, vendez les vôtres simplement.</p>
        </div>

        <div>
            <p class="font-display font-bold">Le site</p>
            <ul class="mt-3 space-y-2 text-sm text-white/80">
                <li><a href="/#evenements" class="hover:underline">Événements</a></li>
                <li><a href="{{ route('help') }}" class="hover:underline">Aide</a></li>
                <li><a href="{{ route('contact') }}" class="hover:underline">Contactez-nous</a></li>
                <li><a href="{{ $sellUrl }}" class="hover:underline">Vendre mes billets</a></li>
            </ul>
        </div>

        <div>
            <p class="font-display font-bold">Suivez-nous</p>
            <ul class="mt-3 space-y-3 text-sm text-white/80">
                <li>
                    <a href="{{ $links['instagram'] }}" target="_blank" rel="noopener" class="flex items-center gap-3 hover:underline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.8" fill="currentColor"/></svg>
                        Instagram
                    </a>
                </li>
                <li>
                    <a href="{{ $links['tiktok'] }}" target="_blank" rel="noopener" class="flex items-center gap-3 hover:underline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M15 4v9.5a4 4 0 1 1-4-4M15 4c.4 2.4 2 3.9 4.5 4.1"/></svg>
                        TikTok
                    </a>
                </li>
                <li>
                    <a href="https://wa.me/{{ $contact['whatsapp_number'] }}" target="_blank" rel="noopener" class="flex items-center gap-3 hover:underline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M4 20l1.4-4.3A8.5 8.5 0 1 1 8.4 18.7z"/></svg>
                        WhatsApp : {{ $contact['whatsapp_display'] }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-6 py-4 text-sm text-white/60">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <p class="flex flex-wrap gap-4">
                <a href="{{ route('legal.terms') }}" class="hover:underline">Conditions d'utilisation</a>
                <a href="{{ route('legal.privacy') }}" class="hover:underline">Confidentialité</a>
                <a href="{{ route('legal.notice') }}" class="hover:underline">Mentions légales</a>
            </p>
        </div>
    </div>
</footer>
