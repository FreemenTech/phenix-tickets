<x-public-layout>
    <section class="hero hero-photo" style="--hero-photo: url('{{ asset('images/hero-phenix.jpeg') }}')">
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-6 py-16 md:grid-cols-2 md:py-24">
            <div>
                <h1 class="font-display text-4xl font-extrabold leading-[1.05] md:text-6xl">
                    Vos billets d'événements au Bénin, payés en Mobile Money.
                </h1>
                <p class="mt-6 max-w-md text-lg text-white/80">
                    Concerts, festivals, soirées : choisissez votre événement, payez avec MTN MoMo ou Moov Money, et recevez votre billet avec QR code.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#evenements" class="btn-flame">Voir les événements</a>
                    <a href="{{ auth()->check() ? route('organizer.become') : route('organizer.register') }}" class="btn-ghost">Vendre mes billets</a>
                </div>
            </div>
            @if ($featured)
                <div class="md:rotate-2">
                    <x-event-ticket :event="$featured" />
                </div>
            @endif
        </div>
    </section>

    <section id="evenements" class="mx-auto max-w-6xl px-6 py-16">
        <h2 class="font-display text-3xl font-bold">Événements à venir</h2>
        <div class="mt-8 grid gap-8 md:grid-cols-2">
            @forelse ($events as $event)
                <x-event-ticket :event="$event" />
            @empty
                <p class="text-stone-600">Aucun événement publié pour le moment. Les prochains billets arrivent bientôt.</p>
            @endforelse
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-20">
        <div class="rounded-3xl bg-white p-8 md:p-12">
            <h2 class="font-display text-3xl font-bold">Vous organisez un événement ?</h2>
            <p class="mt-4 max-w-xl text-stone-600">
                Créez votre page, fixez vos prix et vendez vos billets en ligne. Une commission de 10 % est prélevée sur chaque billet vendu, le reste vous est reversé après l'événement.
            </p>
            <a href="{{ auth()->check() ? route('organizer.become') : route('organizer.register') }}" class="btn-outline mt-6">Créer mon espace organisateur</a>
        </div>
    </section>
</x-public-layout>
