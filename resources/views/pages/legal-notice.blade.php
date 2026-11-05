@php
    $company = config('billetterie.company');
    $contact = config('billetterie.contact');
    $todo = '[à compléter]';
@endphp
<x-public-layout title="Mentions légales">
    <div class="mx-auto max-w-3xl px-6 py-12">
        <h1 class="font-display text-3xl font-bold">Mentions légales</h1>

        <h2 class="font-display mt-8 text-xl font-bold">Éditeur du site</h2>
        <ul class="mt-3 space-y-1">
            <li><strong>Nom :</strong> {{ $company['name'] }}</li>
            <li><strong>Forme juridique :</strong> {{ $company['legal_form'] ?: $todo }}</li>
            <li><strong>Adresse :</strong> {{ $company['address'] ?: $todo }}</li>
            <li><strong>RCCM :</strong> {{ $company['rccm'] ?: $todo }}</li>
            <li><strong>IFU :</strong> {{ $company['ifu'] ?: $todo }}</li>
            <li><strong>Email :</strong> {{ $company['email'] ?: $todo }}</li>
            <li><strong>Téléphone / WhatsApp :</strong> {{ $contact['whatsapp_display'] }}</li>
            <li><strong>Directeur de la publication :</strong> {{ $company['director'] ?: $todo }}</li>
        </ul>

        <h2 class="font-display mt-8 text-xl font-bold">Hébergement</h2>
        <p class="mt-3">{{ $company['host'] ?: $todo }}</p>

        <h2 class="font-display mt-8 text-xl font-bold">Paiements</h2>
        <p class="mt-3">Les paiements sont traités par FedaPay, qui agit en son nom et sous sa responsabilité.</p>

        <h2 class="font-display mt-8 text-xl font-bold">Propriété intellectuelle</h2>
        <p class="mt-3">Le site, son nom, son logo et ses contenus sont protégés. Toute reproduction sans autorisation est interdite.</p>

        <h2 class="font-display mt-8 text-xl font-bold">Contact</h2>
        <p class="mt-3">Via la page <a href="{{ route('contact') }}" class="underline">Contactez-nous</a> ou par WhatsApp au {{ $contact['whatsapp_display'] }}.</p>
    </div>
</x-public-layout>
