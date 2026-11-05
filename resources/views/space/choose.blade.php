<x-public-layout title="Choisir un espace">
    <div class="mx-auto max-w-xl px-6 py-14">
        <h1 class="font-display text-3xl font-bold">Comment souhaitez-vous continuer ?</h1>
        <p class="mt-3 text-stone-600">Votre adresse email est liée à un espace client et à un espace organisateur.</p>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <a href="{{ url('/') }}" class="block rounded-3xl bg-white p-6">
                <p class="font-display text-xl font-bold">Espace client</p>
                <p class="mt-2 text-sm text-stone-600">Découvrir les événements et acheter des billets.</p>
            </a>
            <a href="{{ route('organizer.dashboard') }}" class="block rounded-3xl bg-white p-6">
                <p class="font-display text-xl font-bold">Espace organisateur</p>
                <p class="mt-2 text-sm text-stone-600">Gérer vos événements et vos ventes.</p>
            </a>
        </div>
    </div>
</x-public-layout>
