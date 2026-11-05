@php
    $money = fn ($n) => number_format($n, 0, ',', ' ').' FCFA';
    $labels = [
        'pending' => ['En attente', 'pill-warn'],
        'approved' => ['Approuvé', 'pill-ok'],
        'suspended' => ['Suspendu', 'pill-ko'],
    ];
@endphp
<x-dashboard-layout title="Organisateurs" area="admin">
    <h1 class="font-display text-3xl font-bold">Organisateurs</h1>
    <p class="mt-1 text-stone-600">Approuver un organisateur publie ses événements en brouillon. Le suspendre les retire du site.</p>

    @if (session('status'))
        <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
    @endif

    <div class="panel mt-8">
        <div class="overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr><th>Structure</th><th>Contact</th><th>Statut</th><th>Événements</th><th>Reste à verser</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($organizers as $organizer)
                        @php
                            [$label, $class] = $labels[$organizer->status] ?? [$organizer->status, 'pill-muted'];
                            $due = $balances[$organizer->id]['due'] ?? 0;
                        @endphp
                        <tr>
                            <td class="font-semibold">{{ $organizer->name }}</td>
                            <td>
                                {{ $organizer->user->name }}<br>
                                <span class="text-xs text-stone-500">{{ $organizer->user->email }} / {{ $organizer->phone }}</span>
                            </td>
                            <td><span class="pill {{ $class }}">{{ $label }}</span></td>
                            <td>{{ $organizer->events_count }}</td>
                            <td>{{ $money($due) }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    @if ($organizer->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.organizers.approve', $organizer->id) }}">
                                            @csrf
                                            <button type="submit" class="btn-flame btn-sm">Approuver</button>
                                        </form>
                                    @endif
                                    @if ($organizer->status === 'approved')
                                        <form method="POST" action="{{ route('admin.organizers.suspend', $organizer->id) }}"
                                              onsubmit="return confirm('Suspendre cet organisateur ? Ses événements seront retirés du site.')">
                                            @csrf
                                            <button type="submit" class="btn-outline btn-sm">Suspendre</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-stone-600">Aucun organisateur pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-layout>
