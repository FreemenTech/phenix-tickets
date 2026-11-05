@php
    $money = fn ($n) => number_format($n, 0, ',', ' ').' FCFA';
    $pct = fn ($n, $d) => $d > 0 ? round($n / $d * 100, 1) : 0;
@endphp
<x-dashboard-layout title="Tableau de bord" area="organizer">
    <h1 class="font-display text-3xl font-bold">Bonjour, {{ $organizer->name }}</h1>
    <p class="mt-1 text-stone-600">Voici l'activité de vos événements.</p>

    @if (session('status'))
        <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p class="mt-6 rounded-xl border border-red-300 bg-red-50 p-4 text-sm">{{ session('error') }}</p>
    @endif
    @unless ($organizer->isApproved())
        <p class="mt-6 rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm">
            Votre compte est en cours de validation. Vos événements restent en brouillon jusqu'à son approbation.
        </p>
    @endunless

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi label="Billets vendus" :value="number_format($ticketsSold, 0, ',', ' ')" :hint="'sur '.number_format($capacity, 0, ',', ' ').' places'" />
        <x-kpi label="Chiffre d'affaires" :value="$money($revenue)" />
        <x-kpi label="Net à recevoir" :value="$money($net)" :hint="'après commission de '.$organizer->commission_rate.' %'" />
        <x-kpi label="Événements publiés" :value="$events->where('status', 'published')->count()" :hint="$events->count().' au total'" />
    </div>

    @if ($sales->isEmpty())
        <div class="panel mt-8 text-center">
            <p class="text-stone-600">Vous n'avez pas encore d'événement. Créez le premier pour commencer à vendre.</p>
            <a href="{{ route('organizer.events.create') }}" class="btn-flame mt-4">Créer un événement</a>
        </div>
    @else
        <div class="mt-8 grid gap-6 xl:grid-cols-2">
            <div class="panel">
                <h2 class="font-display text-lg font-bold">Billets vendus et restants par événement</h2>
                <div class="mt-4" style="height: {{ max(220, $sales->count() * 56 + 90) }}px">
                    <canvas id="eventsChart"></canvas>
                </div>
            </div>
            <div class="panel">
                <h2 class="font-display text-lg font-bold">Billets vendus, 14 derniers jours</h2>
                <div class="mt-4" style="height: 260px">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="panel mt-6">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-display text-lg font-bold">Vos événements</h2>
                <a href="{{ route('organizer.events.index') }}" class="text-sm underline">Tout voir</a>
            </div>
            <div class="mt-4 space-y-5">
                @foreach ($sales->take(5) as $s)
                    <div>
                        <div class="flex flex-wrap items-baseline justify-between gap-2">
                            <a href="{{ route('organizer.events.show', $s['event']->id) }}" class="font-semibold underline">{{ $s['event']->title }}</a>
                            <span class="text-sm text-stone-600">{{ $s['paid'] }} vendus, {{ $s['remaining'] }} restants</span>
                        </div>
                        <div class="bar mt-2">
                            <span class="bar-paid" style="width: {{ $pct($s['paid'], $s['capacity']) }}%"></span>
                            <span class="bar-reserved" style="width: {{ $pct($s['reserved'], $s['capacity']) }}%"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @push('scripts')
        <script>
            const events = @js($chart);
            const daily = @js($daily);

            new Chart(document.getElementById('eventsChart'), {
                type: 'bar',
                data: {
                    labels: events.labels,
                    datasets: [
                        { label: 'Vendus', data: events.paid, backgroundColor: '#e4361b', borderRadius: 6 },
                        { label: 'Réservés (non payés)', data: events.reserved, backgroundColor: '#f5b53c' },
                        { label: 'Restants', data: events.remaining, backgroundColor: '#e7e1dc' }
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
    @endif
</x-dashboard-layout>
