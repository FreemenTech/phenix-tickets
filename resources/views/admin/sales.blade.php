@php
    $money = fn ($n) => number_format($n, 0, ',', ' ').' FCFA';
    $labels = [
        'paid' => ['Payée', 'pill-ok'],
        'pending' => ['En attente', 'pill-warn'],
        'expired' => ['Expirée', 'pill-muted'],
        'refund_needed' => ['À rembourser', 'pill-ko'],
        'refunded' => ['Remboursée', 'pill-muted'],
        'failed' => ['Échouée', 'pill-ko'],
        'cancelled' => ['Supprimée par le client', 'pill-muted'],
    ];
    $filters = ['' => 'Toutes'] + collect($labels)->map(fn ($l) => $l[0])->all();
@endphp
<x-dashboard-layout title="Ventes" area="admin">
    <h1 class="font-display text-3xl font-bold">Ventes</h1>
    <p class="mt-1 text-stone-600">Les 100 dernières commandes.</p>

    @if (session('status'))
        <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p class="mt-6 rounded-xl border border-red-300 bg-red-50 p-4 text-sm">{{ session('error') }}</p>
    @endif

    <div class="mt-6 flex flex-wrap gap-2">
        @foreach ($filters as $key => $text)
            <a href="{{ $key === '' ? route('admin.sales') : route('admin.sales', ['status' => $key]) }}"
               class="{{ ($status ?? '') === (string) $key ? 'btn-flame' : 'btn-outline' }} btn-sm">
                {{ $text }}@if ($key !== '' && isset($totals[$key])) ({{ $totals[$key]->n }})@endif
            </a>
        @endforeach
    </div>

    <div class="panel mt-6">
        <div class="overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr><th>Date</th><th>Référence</th><th>Événement</th><th>Acheteur</th><th>Total</th><th>Commission</th><th>Statut</th><th>Remboursement</th></tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php [$label, $class] = $labels[$order->status] ?? [$order->status, 'pill-muted']; @endphp
                        <tr>
                            <td>{{ $order->created_at->translatedFormat('j M, H\hi') }}</td>
                            <td>
                                <span class="font-mono text-xs">{{ $order->reference }}</span>
                                @if ($order->payment_reference)
                                    <br><span class="text-xs text-stone-500">FedaPay n° {{ $order->payment_reference }}</span>
                                @endif
                            </td>
                            <td>{{ $order->event->title }}<br><span class="text-xs text-stone-500">{{ $order->event->organizer->name }}</span></td>
                            <td>{{ $order->buyer_name }}<br><span class="text-xs text-stone-500">{{ $order->buyer_phone }}</span></td>
                            <td>{{ $money($order->total) }}</td>
                            <td>{{ $money($order->commission_amount) }}</td>
                            <td><span class="pill {{ $class }}">{{ $label }}</span></td>
                            <td>
                                @if (in_array($order->status, ['paid', 'refund_needed'], true))
                                    <form method="POST" action="{{ route('admin.orders.refund', $order->id) }}" class="flex flex-wrap gap-2"
                                          onsubmit="return confirm('Confirmer que cette commande a bien été remboursée au client dans FedaPay ?')">
                                        @csrf
                                        <input class="field" style="width: 9rem" name="reference" placeholder="Réf. remboursement">
                                        <button type="submit" class="btn-outline btn-sm">Marquer remboursée</button>
                                    </form>
                                @elseif ($order->status === 'refunded')
                                    <span class="font-mono text-xs text-stone-500">{{ $order->refund_reference }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-stone-600">Aucune commande.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-layout>
