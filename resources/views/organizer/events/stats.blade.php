@php
    $money = fn ($n) => number_format($n, 0, ',', ' ').' FCFA';
    $paid = $breakdown->sum('paid');
    $used = $breakdown->sum('used');
    $reserved = $breakdown->sum('reserved');
    $remaining = $breakdown->sum('remaining');
    $capacity = $breakdown->sum(fn ($b) => $b['type']->quantity);
    $chart = [
        'labels' => $breakdown->map(fn ($b) => $b['type']->name)->values(),
        'paid' => $breakdown->pluck('paid')->values(),
        'reserved' => $breakdown->pluck('reserved')->values(),
        'remaining' => $breakdown->pluck('remaining')->values(),
    ];
@endphp
<x-dashboard-layout :title="'Statistiques : '.$event->title" area="organizer">
    <a href="{{ route('organizer.events.index') }}" class="text-sm underline">Retour aux événements</a>

    <div class="mt-3 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-bold">{{ $event->title }}</h1>
            <p class="mt-1 text-stone-600">{{ $event->starts_at->translatedFormat('l j F Y, H\hi') }}, {{ $event->venue }}, {{ $event->city }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($event->status === 'published')
                <a href="{{ route('events.show', $event->slug) }}" class="btn-outline btn-sm">Voir la page</a>
            @endif
            <a href="{{ route('organizer.events.edit', $event->id) }}" class="btn-outline btn-sm">Modifier</a>
            <a href="{{ route('scanner.index') }}" class="btn-flame btn-sm">Scanner</a>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-kpi label="Billets vendus" :value="$paid" :hint="'sur '.$capacity.' places'" />
        <x-kpi label="Billets restants" :value="$remaining" :hint="$reserved.' réservé(s) en attente de paiement'" />
        <x-kpi label="Entrées validées" :value="$used" :hint="'sur '.$paid.' billets vendus'" />
        <x-kpi label="Chiffre d'affaires" :value="$money($revenue)" />
        <x-kpi label="Net à recevoir" :value="$money($net)" :hint="'après commission de '.$event->organizer->commission_rate.' %'" />
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <div class="panel">
            <h2 class="font-display text-lg font-bold">Par catégorie de billet</h2>
            <div class="mt-4" style="height: {{ max(200, $breakdown->count() * 70 + 90) }}px">
                <canvas id="typesChart"></canvas>
            </div>
        </div>
        <div class="panel">
            <h2 class="font-display text-lg font-bold">Ventes des 14 derniers jours</h2>
            <div class="mt-4" style="height: 260px">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="panel mt-6">
        <h2 class="font-display text-lg font-bold">Dernières commandes payées</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr><th>Date</th><th>Acheteur</th><th>Billets</th><th>Montant</th></tr>
                </thead>
                <tbody>
                    @forelse ($recent as $order)
                        <tr>
                            <td>{{ $order->paid_at?->translatedFormat('j M, H\hi') }}</td>
                            <td>{{ $order->buyer_name }}</td>
                            <td>{{ $order->tickets_count }}</td>
                            <td>{{ $money($order->total) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-stone-600">Aucune vente pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        const types = @js($chart);
        const daily = @js($daily);

        new Chart(document.getElementById('typesChart'), {
            type: 'bar',
            data: {
                labels: types.labels,
                datasets: [
                    { label: 'Vendus', data: types.paid, backgroundColor: '#e4361b', borderRadius: 6 },
                    { label: 'Réservés (non payés)', data: types.reserved, backgroundColor: '#f5b53c' },
                    { label: 'Restants', data: types.remaining, backgroundColor: '#e7e1dc' }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true, beginAtZero: true, ticks: { precision: 0 } },
                    y: { stacked: true }
                },
                plugins: { legend: { position: 'bottom' } }
            }
        });

        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: {
                labels: daily.labels,
                datasets: [{ label: 'Billets vendus', data: daily.tickets, backgroundColor: '#e4361b', borderRadius: 6 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                plugins: { legend: { display: false } }
            }
        });
    </script>
    @endpush
</x-dashboard-layout>
