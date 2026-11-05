@php
    $delay = config('billetterie.payout_delay_days');
@endphp
<x-public-layout title="Aide">
    <div class="mx-auto max-w-3xl px-6 py-12">
        <h1 class="font-display text-3xl font-bold">Aide</h1>
        <p class="mt-2 text-stone-600">Comment fonctionne {{ config('app.name') }}, pour acheter vos billets ou pour vendre les vôtres.</p>

        <section class="panel mt-8">
            <h2 class="font-display text-xl font-bold">Acheter des billets</h2>
            <ol class="mt-4 list-decimal space-y-3 pl-5">
                <li><strong>Créez un compte</strong> (ou connectez-vous). Il est nécessaire pour acheter et retrouver vos billets.</li>
                <li><strong>Choisissez un événement</strong> sur la page d'accueil, puis le nombre de billets de chaque catégorie.</li>
                <li><strong>Vérifiez votre commande</strong> : vos places sont réservées pendant environ 15 minutes, le temps de payer. Passé ce délai sans paiement, elles sont libérées.</li>
                <li><strong>Payez</strong> en Mobile Money (MTN MoMo, Moov Money) ou par carte, sur la page de paiement sécurisée FedaPay. Des frais de paiement peuvent s'afficher sur cette page.</li>
                <li><strong>Recevez vos billets</strong> : ils s'affichent avec leur QR code, vous les téléchargez en PDF, et une copie est envoyée par email.</li>
                <li><strong>À l'entrée</strong>, présentez le QR code (sur téléphone ou imprimé). Chaque billet ne peut être utilisé qu'une seule fois.</li>
            </ol>
            <p class="mt-4 text-sm text-stone-600">Retrouvez à tout moment vos commandes et vos billets dans <strong>Mes billets</strong> (menu en haut de la page).</p>
        </section>

        <section class="panel mt-6">
            <h2 class="font-display text-xl font-bold">Vendre vos billets (organisateurs)</h2>
            <ol class="mt-4 list-decimal space-y-3 pl-5">
                <li><strong>Créez votre espace organisateur</strong> avec le lien « Vendre mes billets ». Si vous avez déjà un compte, vous pouvez ouvrir un espace organisateur depuis ce même compte.</li>
                <li><strong>Attendez la validation</strong> de votre compte. Tant qu'il n'est pas validé, vos événements restent en brouillon. Une fois validé, ils sont publiés.</li>
                <li><strong>Créez votre événement</strong> : nom, date, lieu, description, une affiche (elle sert de fond à vos billets) et vos catégories de billets avec leur prix et leur nombre de places.</li>
                <li><strong>Suivez vos ventes</strong> dans le tableau de bord : billets vendus et restants, chiffre d'affaires, statistiques par événement.</li>
                <li><strong>Contrôlez les entrées</strong> avec la page <strong>Scanner</strong> : scannez le QR code de chaque billet. Un billet déjà scanné est refusé.</li>
                <li><strong>Recevez votre argent</strong> : renseignez votre numéro Mobile Money dans <strong>Mes reversements</strong>.</li>
            </ol>
            <h3 class="font-display mt-6 text-lg font-bold">Commission et reversements</h3>
            <p class="mt-2">Une commission de 10 % est prélevée sur chaque billet vendu. Le reste vous est reversé <strong>après la fin de votre événement</strong>, plus {{ $delay }} jour(s). Les ventes d'un événement à venir apparaissent « en attente » dans votre espace jusqu'à cette date.</p>
        </section>

        <section class="panel mt-6">
            <h2 class="font-display text-xl font-bold">Questions fréquentes</h2>
            <div class="mt-4 divide-y divide-stone-200">
                <details class="py-3">
                    <summary class="cursor-pointer font-semibold">J'ai payé mais je n'ai pas reçu mes billets.</summary>
                    <p class="mt-2 text-stone-600">Ouvrez <strong>Mes billets</strong> : la commande y apparaît dès que le paiement est confirmé, parfois après quelques minutes. Si elle reste « en attente » alors que vous avez été débité, <a href="{{ route('contact') }}" class="underline">contactez-nous</a> avec la référence de commande (PX-...).</p>
                </details>
                <details class="py-3">
                    <summary class="cursor-pointer font-semibold">Ma réservation a expiré. Que faire ?</summary>
                    <p class="mt-2 text-stone-600">Les places sont libérées si le paiement n'arrive pas à temps. Refaites simplement une commande, si des places sont encore disponibles.</p>
                </details>
                <details class="py-3">
                    <summary class="cursor-pointer font-semibold">J'ai perdu mon billet.</summary>
                    <p class="mt-2 text-stone-600">Connectez-vous, ouvrez <strong>Mes billets</strong>, puis la commande concernée : vous pouvez revoir les QR codes et retélécharger le PDF.</p>
                </details>
                <details class="py-3">
                    <summary class="cursor-pointer font-semibold">Peut-on se faire rembourser ?</summary>
                    <p class="mt-2 text-stone-600">En cas d'événement annulé ou de problème, <a href="{{ route('contact') }}" class="underline">contactez-nous</a> avec votre référence de commande : chaque situation est étudiée.</p>
                </details>
                <details class="py-3">
                    <summary class="cursor-pointer font-semibold">Mon billet est refusé à l'entrée.</summary>
                    <p class="mt-2 text-stone-600">Un billet ne peut être scanné qu'une seule fois. Si le contrôle indique « déjà utilisé » alors que vous ne l'avez pas présenté, montrez votre commande à l'organisateur et écrivez-nous.</p>
                </details>
            </div>
        </section>

        <p class="mt-8 text-center text-stone-600">Une autre question ? <a href="{{ route('contact') }}" class="underline">Contactez-nous</a>.</p>
    </div>
</x-public-layout>
