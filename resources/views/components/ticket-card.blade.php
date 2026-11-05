@props(['ticket'])
@php
    $order = $ticket->order;
    $event = $order->event;
    $cover = $event->cover_image ? asset('storage/'.$event->cover_image) : null;
    $valid = $order->isPaid() && $ticket->status === 'valid';
    $label = match (true) {
        $event->status === 'cancelled' => 'Événement annulé',
        $ticket->status === 'used' => 'Billet déjà utilisé',
        $valid => 'Billet valide',
        default => 'Billet non valide',
    };
@endphp
<div class="poster-card" @if ($cover) style="background-image: url('{{ $cover }}'); background-position: {{ (int) $event->cover_focus_x }}% {{ (int) $event->cover_focus_y }}%" @endif>
    <div class="poster-card__overlay">
        <p class="poster-pill {{ $valid ? 'poster-pill--ok' : 'poster-pill--ko' }}">{{ $label }}</p>
        <h3 class="font-display mt-3 text-2xl font-bold leading-tight">{{ $event->title }}</h3>
        <p class="mt-1 text-sm opacity-90">{{ $event->starts_at->translatedFormat('l j F Y, H\hi') }}</p>
        <p class="text-sm opacity-90">{{ $event->venue }}, {{ $event->city }}</p>

        <div class="mt-5 flex items-end justify-between gap-4">
            <div>
                <p class="font-semibold">{{ $ticket->ticketType->name }}</p>
                <p class="text-sm opacity-90">{{ $ticket->holder_name }}</p>
                <p class="mt-2 font-mono text-sm">{{ $ticket->code }}</p>
            </div>
            @if ($order->isPaid())
                <img src="{{ \App\Support\TicketQr::dataUri($ticket) }}" alt="QR code du billet" class="h-28 w-28 shrink-0 rounded-xl bg-white p-1.5">
            @endif
        </div>
    </div>
</div>
