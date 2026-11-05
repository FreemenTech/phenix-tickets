<x-public-layout :title="'Commande '.$order->reference">
    <div class="mx-auto max-w-2xl px-6 py-12">
        <h1 class="font-display text-3xl font-bold">Commande {{ $order->reference }}</h1>
        <p class="mt-2 text-stone-600">
            {{ $order->event->title }}, {{ $order->event->starts_at->translatedFormat('l j F Y, H\hi') }}
        </p>

        @if (session('status'))
            <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
        @endif
        @if (session('error'))
            <p class="mt-6 rounded-xl border border-red-300 bg-red-50 p-4 text-sm">{{ session('error') }}</p>
        @endif

        <div class="mt-8 rounded-3xl bg-white p-6">
            @foreach ($order->tickets->groupBy('ticket_type_id') as $tickets)
                @php $type = $tickets->first()->ticketType; @endphp
                <div class="flex justify-between py-2">
                    <span>{{ $tickets->count() }} × {{ $type->name }}</span>
                    <span>{{ number_format($type->price * $tickets->count(), 0, ',', ' ') }} FCFA</span>
                </div>
            @endforeach
            <div class="mt-3 flex justify-between border-t border-stone-200 pt-3 font-bold">
                <span>Total</span>
                <span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        @if ($order->isPaid())
            @php $order->tickets->each(fn ($t) => $t->setRelation('order', $order)); @endphp
            <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
                <h2 class="font-display text-xl font-bold">Vos billets</h2>
                <a href="{{ route('orders.pdf', $order->reference) }}" class="btn-flame btn-sm">Télécharger en PDF</a>
            </div>
            <div class="mt-4 space-y-4">
                @foreach ($order->tickets as $ticket)
                    <x-ticket-card :ticket="$ticket" />
                @endforeach
            </div>
            <p class="mt-4 text-sm text-stone-600">Une copie est envoyée à {{ $order->buyer_email }}.</p>
        @elseif ($order->status === 'expired')
            <p class="mt-6 rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm">
                Cette réservation a expiré faute de paiement et les places ont été libérées. Vous pouvez refaire une commande.
            </p>
            <a href="/#evenements" class="btn-flame mt-4">Voir les événements</a>
        @elseif ($order->status === 'refunded')
            <p class="mt-6 rounded-xl border border-stone-300 bg-stone-100 p-4 text-sm">
                Cette commande a été remboursée.
                @if ($order->refund_reference)
                    Référence du remboursement : {{ $order->refund_reference }}.
                @endif
            </p>
        @elseif ($order->status === 'cancelled')
            <p class="mt-6 rounded-xl border border-stone-300 bg-stone-100 p-4 text-sm">
                Cette commande a été supprimée.
            </p>
        @elseif ($order->status === 'refund_needed')
            <p class="mt-6 rounded-xl border border-red-300 bg-red-50 p-4 text-sm">
                @if ($order->event->status === 'cancelled')
                    L'événement a été annulé. Votre paiement sera remboursé : nous vous tiendrons informé.
                @else
                    Votre paiement a bien été reçu, mais les places ne sont plus disponibles. Contactez-nous avec la référence {{ $order->reference }} pour un remboursement.
                @endif
            </p>
        @else
            <form method="POST" action="{{ route('orders.pay', $order->reference) }}" class="mt-6">
                @csrf
                <button type="submit" class="btn-flame w-full">
                    Payer {{ number_format($order->total, 0, ',', ' ') }} FCFA
                </button>
            </form>
            <p class="mt-3 text-center text-sm text-stone-600">Paiement sécurisé par FedaPay : MTN MoMo, Moov Money ou carte. Vos places sont réservées 15 minutes.</p>
        @endif
    </div>
</x-public-layout>
