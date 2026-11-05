@php
    $company = config('billetterie.company');
    $delay = config('billetterie.payout_delay_days');
    $app = config('app.name');
@endphp
<x-public-layout title="Conditions d'utilisation">
    <div class="mx-auto max-w-3xl px-6 py-12">
        <h1 class="font-display text-3xl font-bold">Conditions d'utilisation</h1>
        <p class="mt-2 text-sm text-stone-500">Dernière mise à jour : 26 octobre 2026</p>

        <h2 class="font-display mt-8 text-xl font-bold">1. Présentation</h2>
        <p class="mt-3">{{ $app }} est une plateforme de billetterie en ligne éditée par {{ $company['name'] }} (« nous »). Elle permet à des organisateurs de vendre des billets pour leurs événements, et à des acheteurs de les acheter. Nous agissons comme intermédiaire technique : chaque événement est organisé sous la seule responsabilité de son organisateur.</p>

        <h2 class="font-display mt-8 text-xl font-bold">2. Compte</h2>
        <p class="mt-3">Un compte est nécessaire pour acheter des billets. Vous vous engagez à fournir des informations exactes, à garder votre mot de passe confidentiel et à nous prévenir en cas d'utilisation frauduleuse de votre compte. Un compte acheteur peut ouvrir un espace organisateur, qui est soumis à notre validation.</p>

        <h2 class="font-display mt-8 text-xl font-bold">3. Achat de billets</h2>
        <ul class="mt-3 list-disc space-y-2 pl-5">
            <li>Les prix sont indiqués en francs CFA (FCFA) et fixés par l'organisateur. Les éventuels frais du prestataire de paiement sont affichés avant le paiement.</li>
            <li>Les places sont réservées pendant environ 15 minutes à la création de la commande. Sans paiement dans ce délai, elles sont libérées.</li>
            <li>Le paiement s'effectue sur la page sécurisée de notre prestataire FedaPay (MTN MoMo, Moov Money, carte bancaire).</li>
            <li>Une fois le paiement confirmé, vos billets sont disponibles dans « Mes billets », téléchargeables en PDF et envoyés par email.</li>
            <li>Chaque billet porte un QR code unique et ne peut être utilisé qu'une seule fois. Ne le partagez pas et ne le publiez pas : toute personne qui le présente en premier peut être admise. La revente à des fins commerciales est interdite.</li>
        </ul>

        <h2 class="font-display mt-8 text-xl font-bold">4. Annulation, report et remboursement</h2>
        <p class="mt-3">Sauf mention contraire de l'organisateur, les billets ne sont ni échangeables ni remboursables. En cas d'annulation de l'événement, le prix des billets est remboursé selon les modalités que nous vous communiquons. Pour toute demande, utilisez la page <a href="{{ route('contact') }}" class="underline">Contactez-nous</a> en indiquant la référence de votre commande.</p>

        <h2 class="font-display mt-8 text-xl font-bold">5. Conditions applicables aux organisateurs</h2>
        <ul class="mt-3 list-disc space-y-2 pl-5">
            <li><strong>Validation.</strong> Votre compte organisateur doit être validé par nos soins avant la publication de vos événements. Nous pouvons refuser ou suspendre un compte en cas d'informations inexactes, de contenu illicite, de fraude ou de manquement à ces conditions.</li>
            <li><strong>Vos obligations.</strong> Vous garantissez l'exactitude des informations de l'événement, que vous disposez des autorisations nécessaires, et que vous détenez les droits sur l'affiche et les images que vous publiez, que vous nous autorisez à afficher sur le site, dans les billets et dans les emails.</li>
            <li><strong>Commission.</strong> Nous prélevons une commission de 10 % sur le prix de chaque billet vendu (le taux applicable est indiqué dans votre espace).</li>
            <li><strong>Reversements.</strong> Les sommes qui vous sont dues sont versées après la fin de l'événement, au plus tôt {{ $delay }} jour(s) après sa fin (ou après sa date de début si aucune fin n'est indiquée), par Mobile Money au numéro que vous avez renseigné. Le montant versé correspond au prix des billets vendus, moins la commission et les remboursements éventuels.</li>
            <li><strong>Retenue.</strong> Nous pouvons différer ou retenir un versement en cas de litige, de suspicion de fraude ou d'annulation de l'événement.</li>
            <li><strong>Annulation par l'organisateur.</strong> Vous devez nous prévenir sans délai. Le remboursement des acheteurs est à votre charge et est déduit de vos reversements.</li>
            <li><strong>Contrôle des entrées.</strong> Vous contrôlez les billets avec le scanner du site. Un billet déjà scanné est refusé.</li>
        </ul>

        <h2 class="font-display mt-8 text-xl font-bold">6. Responsabilité</h2>
        <p class="mt-3">Nous ne sommes pas responsables du déroulement, du contenu, de la qualité, du report ou de l'annulation d'un événement, qui relèvent de l'organisateur. Nous mettons tout en œuvre pour que le site soit disponible mais ne pouvons garantir l'absence d'interruption. Notre responsabilité ne couvre pas les dommages indirects.</p>

        <h2 class="font-display mt-8 text-xl font-bold">7. Propriété intellectuelle</h2>
        <p class="mt-3">Le site, son nom, son logo et ses contenus sont protégés. Toute reproduction sans autorisation est interdite. Les affiches et images des événements restent la propriété de leurs titulaires.</p>

        <h2 class="font-display mt-8 text-xl font-bold">8. Données personnelles</h2>
        <p class="mt-3">Le traitement de vos données est décrit dans notre <a href="{{ route('legal.privacy') }}" class="underline">politique de confidentialité</a>.</p>

        <h2 class="font-display mt-8 text-xl font-bold">9. Modification des conditions</h2>
        <p class="mt-3">Nous pouvons modifier ces conditions. La version en vigueur est celle publiée sur cette page, avec sa date de mise à jour.</p>

        <h2 class="font-display mt-8 text-xl font-bold">10. Droit applicable et litiges</h2>
        <p class="mt-3">Ces conditions sont régies par le droit béninois. En cas de litige, nous vous invitons d'abord à nous contacter pour trouver une solution amiable. À défaut, les tribunaux compétents de Cotonou seront saisis.</p>

        <p class="mt-10 text-sm text-stone-600">Une question ? <a href="{{ route('contact') }}" class="underline">Contactez-nous</a>.</p>
    </div>
</x-public-layout>
