@props(['event'])
@php
    $minPrice = $event->ticketTypes->min('price');
    $cover = $event->cover_image ? asset('storage/'.$event->cover_image) : null;
@endphp
<div class="ticket-wrap">
    <a href="{{ route('events.show', $event->slug) }}" class="ticket {{ $cover ? 'ticket--cover' : '' }}" @if ($cover) style="background-image: url('{{ $cover }}'); background-position: {{ (int) $event->cover_focus_x }}% {{ (int) $event->cover_focus_y }}%" @endif>
        <div class="ticket-body p-5 sm:p-6">
            <h3 class="font-display text-xl font-bold leading-snug">{{ $event->title }}</h3>
            <p class="mt-2 text-sm text-stone-600">{{ $event->venue }}, {{ $event->city }}</p>
            <p class="mt-1 text-sm text-stone-600">{{ $event->starts_at->translatedFormat('l j F, H\hi') }}</p>
        </div>
        <div class="ticket-stub">
            <span class="font-display text-4xl font-extrabold leading-none">{{ $event->starts_at->format('d') }}</span>
            <span class="text-sm font-semibold">{{ $event->starts_at->translatedFormat('F') }}</span>
            @if ($minPrice !== null)
                <span class="mt-3 text-xs">dès {{ number_format($minPrice, 0, ',', ' ') }} FCFA</span>
            @endif
        </div>
    </a>
</div>
