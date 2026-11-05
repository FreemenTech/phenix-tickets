@php
    $money = fn ($n) => number_format($n, 0, ',', ' ').' FCFA';
@endphp
<x-dashboard-layout title="Administration" area="admin">
    <h1 class="font-display text-3xl font-bold">Vue d'ensemble</h1>
    <p class="mt-1 text-stone-600">Les ventes de toute la plateforme.</p>

    @if (session('status'))
        <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
    @endif
    @if ($flagged > 0)
        <p class="mt-6 rounded-xl border border-red-300 bg-red-50 p-4 text-sm">
            {{ $flagged }} commande(s) payée(s) sans places disponibles :
            <a href="{{ route('admin.sales', ['status' => 'refund_needed']) }}" class="underline">à rembourser</a>.
        </p>
    @endif
    @if ($pendingOrganizers > 0)
        <p class="mt-6 rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm">
            {{ $pendingOrganizers }} organisateur(s) en attente de validation :
            <a href="{{ route('admin.organizers') }}" class="underline">voir la liste</a>.
        </p>
    @endif

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi label="Chiffre d'affaires" :value="$money($revenue)" hint="billets payés, hors frais FedaPay" />
        <x-kpi label="Ta commission" :value="$money($commission)" hint="avant frais de paiement" />
        <x-kpi label="Billets vendus" :value="number_format($ticketsSold, 0, ',', ' ')" />
        <x-kpi label="Organisateurs en attente" :value="$pendingOrganizers" />
    </div>

    <div class="panel mt-8">
        <h2 class="font-display text-lg font-bold">Chiffre d'affaires et commission, 14 derniers jours</h2>
        <div class="mt-4" style="height: 280px">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="panel mt-6">
        <h2 class="font-display text-lg font-bold">Meilleurs événements</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr><th>Événement</th><th>Organisateur</th><th>Commandes payées</th><th>Chiffre d'affaires</th></tr>
                </thead>
                <tbody>
                    @forelse ($top as $event)
                        <tr>
                            <td class="font-semibold">{{ $event->title }}</td>
                            <td>{{ $event->organizer->name }}</td>
                            <td>{{ $event->paid_orders }}</td>
                            <td>{{ $money((int) $event->revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-stone-600">Aucun événement pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        const daily = @js($daily);

        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: daily.labels,
                datasets: [
                    { label: "Chiffre d'affaires (FCFA)", data: daily.revenue, backgroundColor: '#f5b53c', borderRadius: 6 },
                    { label: 'Commission (FCFA)', data: daily.commission, type: 'line', borderColor: '#e4361b', backgroundColor: '#e4361b', tension: 0.3 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
    @endpush
</x-dashboard-layout>
