@php
    $labels = [
        'published' => ['Publié', 'pill-ok'],
        'draft' => ['Brouillon', 'pill-muted'],
        'cancelled' => ['Annulé', 'pill-ko'],
    ];
@endphp
<x-dashboard-layout title="Événements" area="admin">
    <h1 class="font-display text-3xl font-bold">Événements</h1>
    <p class="mt-1 text-stone-600">Annuler un événement le retire du site, invalide ses billets, prévient les acheteurs par email et place leurs commandes « À rembourser ».</p>

    @if (session('status'))
        <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p class="mt-6 rounded-xl border border-red-300 bg-red-50 p-4 text-sm">{{ session('error') }}</p>
    @endif

    <div class="panel mt-8">
        <div class="overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr><th>Événement</th><th>Organisateur</th><th>Statut</th><th>Billets vendus</th><th>À rembourser</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($sales as $s)
                        @php
                            $event = $s['event'];
                            [$label, $class] = $labels[$event->status] ?? [$event->status, 'pill-muted'];
                        @endphp
                        <tr>
                            <td class="font-semibold">
                                {{ $event->title }}<br>
                                <span class="text-xs font-normal text-stone-500">{{ $event->starts_at->translatedFormat('j F Y, H\hi') }}</span>
                            </td>
                            <td>{{ $event->organizer->name }}</td>
                            <td><span class="pill {{ $class }}">{{ $label }}</span></td>
                            <td>{{ $s['paid'] }} / {{ $s['capacity'] }}</td>
                            <td>{{ $event->to_refund }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    @if ($event->status === 'published')
                                        <a href="{{ route('events.show', $event->slug) }}" class="btn-outline btn-sm">Voir</a>
                                    @endif
                                    @if ($event->status === 'cancelled')
                                        @if ($event->to_refund > 0)
                                            <a href="{{ route('admin.sales', ['status' => 'refund_needed']) }}" class="btn-flame btn-sm">Voir les remboursements</a>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('admin.events.cancel', $event->id) }}"
                                              onsubmit="return confirm('Annuler cet événement ? Il sera retiré du site, tous les billets seront invalidés et les acheteurs seront prévenus par email. Cette action est définitive.')">
                                            @csrf
                                            <button type="submit" class="btn-outline btn-sm">Annuler l'événement</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-stone-600">Aucun événement.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-layout>
