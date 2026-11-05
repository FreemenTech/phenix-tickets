@php
    $labels = [
        'paid' => ['Payée', 'pill-ok'],
        'pending' => ['En attente de paiement', 'pill-warn'],
        'expired' => ['Expirée', 'pill-muted'],
        'refunded' => ['Remboursée', 'pill-muted'],
        'failed' => ['Échouée', 'pill-ko'],
        'refund_needed' => ['Paiement reçu, remboursement en cours', 'pill-ko'],
    ];
@endphp
<x-public-layout title="Mes billets">
    <div class="mx-auto max-w-2xl px-6 py-12">
        <h1 class="font-display text-3xl font-bold">Mes billets</h1>

        @if (session('status'))
            <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
        @endif
        @if (session('error'))
            <p class="mt-6 rounded-xl border border-red-300 bg-red-50 p-4 text-sm">{{ session('error') }}</p>
        @endif

        <div class="mt-8 space-y-4">
            @forelse ($orders as $order)
                @php [$label, $class] = $labels[$order->status] ?? [$order->status, 'pill-muted']; @endphp
                <div class="rounded-3xl bg-white p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <a href="{{ route('orders.show', $order->reference) }}" class="block">
                            <p class="font-display text-lg font-bold">{{ $order->event->title }}</p>
                            <p class="text-sm text-stone-600">{{ $order->event->starts_at->translatedFormat('l j F Y, H\hi') }}</p>
                            <p class="mt-1 font-mono text-xs text-stone-500">{{ $order->reference }}</p>
                        </a>
                        <div class="text-right text-sm">
                            <p class="font-semibold">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
                            <span class="pill {{ $class }}">{{ $label }}</span>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('orders.show', $order->reference) }}" class="btn-outline btn-sm">
                            {{ $order->isPaid() ? 'Voir mes billets' : 'Ouvrir' }}
                        </a>
                        @if (in_array($order->status, ['pending', 'expired', 'failed'], true))
                            <form method="POST" action="{{ route('orders.destroy', $order->reference) }}"
                                  onsubmit="return confirm('Supprimer cette commande ? Les places réservées seront libérées.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-outline btn-sm">Supprimer</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-stone-600">Vous n'avez pas encore de commande. <a href="/#evenements" class="underline">Voir les événements</a></p>
            @endforelse
        </div>
    </div>
</x-public-layout>
