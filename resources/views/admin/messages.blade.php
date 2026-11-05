@php
    $whatsapp = function (?string $phone) {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return null;
        }

        return 'https://wa.me/'.(str_starts_with($digits, '229') ? $digits : '229'.$digits);
    };
@endphp
<x-dashboard-layout title="Messages" area="admin">
    <h1 class="font-display text-3xl font-bold">Messages</h1>
    <p class="mt-1 text-stone-600">Les messages envoyés depuis la page « Contactez-nous ».</p>

    @if (session('status'))
        <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
    @endif

    <div class="mt-8 space-y-4">
        @forelse ($messages as $m)
            <div class="panel" @if (! $m->read_at) style="box-shadow: 0 0 0 2px var(--flamme);" @endif>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-display text-lg font-bold">{{ $m->subject }}</p>
                        <p class="text-sm text-stone-600">
                            {{ $m->name }}, {{ $m->created_at->translatedFormat('j F Y, H\hi') }}
                        </p>
                    </div>
                    <span class="pill {{ $m->read_at ? 'pill-muted' : 'pill-warn' }}">{{ $m->read_at ? 'Lu' : 'Nouveau' }}</span>
                </div>

                <p class="mt-4 whitespace-pre-line">{{ $m->message }}</p>

                <p class="mt-4 text-sm text-stone-600">
                    {{ $m->email }}@if ($m->phone), {{ $m->phone }}@endif
                </p>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="mailto:{{ $m->email }}?subject={{ rawurlencode('Re: '.$m->subject.' - '.config('app.name')) }}" class="btn-flame btn-sm">Répondre par email</a>
                    @if ($whatsapp($m->phone))
                        <a href="{{ $whatsapp($m->phone) }}" target="_blank" rel="noopener" class="btn-outline btn-sm">WhatsApp</a>
                    @endif
                    @unless ($m->read_at)
                        <form method="POST" action="{{ route('admin.messages.read', $m->id) }}">
                            @csrf
                            <button type="submit" class="btn-outline btn-sm">Marquer comme lu</button>
                        </form>
                    @endunless
                    <form method="POST" action="{{ route('admin.messages.destroy', $m->id) }}" onsubmit="return confirm('Supprimer ce message ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-outline btn-sm">Supprimer</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-stone-600">Aucun message pour le moment.</p>
        @endforelse
    </div>
</x-dashboard-layout>
