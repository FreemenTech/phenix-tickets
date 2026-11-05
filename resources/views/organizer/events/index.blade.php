@php
    $pct = fn ($n, $d) => $d > 0 ? round($n / $d * 100, 1) : 0;
    $labels = [
        'published' => ['Publié', 'pill-ok'],
        'draft' => ['Brouillon', 'pill-muted'],
        'cancelled' => ['Annulé', 'pill-ko'],
    ];
@endphp
<x-dashboard-layout title="Mes événements" area="organizer">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-display text-3xl font-bold">Mes événements</h1>
        <a href="{{ route('organizer.events.create') }}" class="btn-flame">Créer un événement</a>
    </div>

    @if (session('status'))
        <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p class="mt-6 rounded-xl border border-red-300 bg-red-50 p-4 text-sm">{{ session('error') }}</p>
    @endif

    <div class="mt-8 space-y-4">
        @forelse ($sales as $s)
            @php
                $event = $s['event'];
                [$label, $class] = $labels[$event->status] ?? [$event->status, 'pill-muted'];
            @endphp
            <div class="panel">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="font-display text-lg font-bold">{{ $event->title }}</h2>
                        <p class="mt-1 text-sm text-stone-600">{{ $event->starts_at->translatedFormat('l j F Y, H\hi') }}, {{ $event->city }}</p>
                    </div>
                    <span class="pill {{ $class }}">{{ $label }}</span>
                </div>

                @if ($event->status === 'cancelled')
                    <p class="mt-4 text-sm text-stone-600">Cet événement est annulé. Ses acheteurs ont été prévenus et seront remboursés.</p>
                @else
                    <div class="bar mt-4">
                        <span class="bar-paid" style="width: {{ $pct($s['paid'], $s['capacity']) }}%"></span>
                        <span class="bar-reserved" style="width: {{ $pct($s['reserved'], $s['capacity']) }}%"></span>
                    </div>
                    <p class="mt-2 text-sm text-stone-600">{{ $s['paid'] }} vendus, {{ $s['reserved'] }} réservés, {{ $s['remaining'] }} restants sur {{ $s['capacity'] }} places</p>
                @endif

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('organizer.events.show', $event->id) }}" class="btn-flame btn-sm">Statistiques</a>
                    @if ($event->status === 'published')
                        <a href="{{ route('events.show', $event->slug) }}" class="btn-outline btn-sm">Voir</a>
                    @endif
                    @if ($event->status !== 'cancelled')
                        <a href="{{ route('organizer.events.edit', $event->id) }}" class="btn-outline btn-sm">Modifier</a>

                        @if ($s['paid'] > 0)
                            <form method="POST" action="{{ route('organizer.events.cancel', $event->id) }}"
                                  onsubmit="return confirm('Annuler cet événement ? Il sera retiré du site, tous les billets vendus seront invalidés, et les acheteurs seront prévenus par email puis remboursés. Cette action est définitive.')">
                                @csrf
                                <button type="submit" class="btn-outline btn-sm">Annuler l'événement</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('organizer.events.destroy', $event->id) }}"
                                  onsubmit="return confirm('Supprimer cet événement ? Cette action est définitive.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-outline btn-sm">Supprimer</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <p class="text-stone-600">Vous n'avez pas encore d'événement.</p>
        @endforelse
    </div>
</x-dashboard-layout>
