@php
    $company = config('billetterie.company');
    $app = config('app.name');
@endphp
<x-public-layout title="Politique de confidentialité">
    <div class="mx-auto max-w-3xl px-6 py-12">
        <h1 class="font-display text-3xl font-bold">Politique de confidentialité</h1>
        <p class="mt-2 text-sm text-stone-500">Dernière mise à jour : 26 octobre 2026</p>

        <h2 class="font-display mt-8 text-xl font-bold">1. Qui est responsable de vos données ?</h2>
        <p class="mt-3">{{ $company['name'] }}, éditeur de {{ $app }} (voir les <a href="{{ route('legal.notice') }}" class="underline">mentions légales</a>).</p>

        <h2 class="font-display mt-8 text-xl font-bold">2. Données que nous collectons</h2>
        <ul class="mt-3 list-disc space-y-2 pl-5">
            <li><strong>Votre compte</strong> : nom, adresse email, téléphone, mot de passe (conservé sous forme chiffrée).</li>
            <li><strong>Vos commandes</strong> : événements, billets, montants, références et statut des paiements.</li>
            <li><strong>Les organisateurs</strong> : nom de la structure, téléphone, opérateur et numéro Mobile Money utilisés pour les reversements.</li>
            <li><strong>Vos messages</strong> envoyés depuis la page Contact.</li>
            <li><strong>Données techniques</strong> : cookies de session et de sécurité nécessaires au fonctionnement du site.</li>
        </ul>

        <h2 class="font-display mt-8 text-xl font-bold">3. Paiement</h2>
        <p class="mt-3">Le paiement se fait sur la page de notre prestataire FedaPay. Nous ne voyons ni ne conservons vos numéros de carte, codes secrets ou identifiants Mobile Money : nous recevons seulement la confirmation du paiement et sa référence.</p>

        <h2 class="font-display mt-8 text-xl font-bold">4. Pourquoi nous utilisons ces données</h2>
        <p class="mt-3">Pour gérer votre compte et vos commandes, vous envoyer vos billets, contrôler les entrées, effectuer les reversements aux organisateurs, répondre à vos messages, prévenir la fraude et respecter nos obligations légales et comptables.</p>

        <h2 class="font-display mt-8 text-xl font-bold">5. Avec qui nous les partageons</h2>
        <ul class="mt-3 list-disc space-y-2 pl-5">
            <li><strong>L'organisateur de l'événement</strong> voit le nom des acheteurs et des titulaires des billets de ses événements, pour gérer les ventes et les entrées.</li>
            <li><strong>Nos prestataires</strong> : FedaPay (paiement), notre hébergeur et notre service d'envoi d'emails.</li>
            <li>Nous ne vendons pas vos données.</li>
        </ul>

        <h2 class="font-display mt-8 text-xl font-bold">6. Cookies</h2>
        <p class="mt-3">Nous utilisons uniquement des cookies essentiels (connexion et sécurité). Nous n'utilisons pas de cookies publicitaires. Certaines pages chargent des polices ou des bibliothèques depuis des services tiers (fonts.bunny.net, jsDelivr, unpkg), qui peuvent recevoir votre adresse IP.</p>

        <h2 class="font-display mt-8 text-xl font-bold">7. Combien de temps nous les conservons</h2>
        <p class="mt-3">Votre compte est conservé tant qu'il est actif. Les données de commandes et de paiements sont conservées pendant la durée requise par les obligations légales et comptables applicables.</p>

        <h2 class="font-display mt-8 text-xl font-bold">8. Vos droits</h2>
        <p class="mt-3">Vous pouvez demander l'accès à vos données, leur rectification, leur suppression ou vous opposer à leur traitement, dans les limites de la loi, via la page <a href="{{ route('contact') }}" class="underline">Contactez-nous</a>. Vous pouvez aussi saisir l'autorité béninoise de protection des données personnelles (APDP).</p>

        <h2 class="font-display mt-8 text-xl font-bold">9. Sécurité</h2>
        <p class="mt-3">Les mots de passe sont chiffrés, les échanges passent par une connexion sécurisée (HTTPS) et l'accès aux données est limité aux personnes qui en ont besoin.</p>
    </div>
</x-public-layout>
