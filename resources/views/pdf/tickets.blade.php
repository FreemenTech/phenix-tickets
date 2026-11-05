<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body { font-family: DejaVu Sans, sans-serif; margin: 0; color: #1f1418; }
        .page { padding: 40px; }
        .card {
            width: 100%;
            height: 430px;
            background-color: #2b0a1f;
            background-image: url('{{ $card }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            color: #ffffff;
        }
        .top { height: 240px; }
        .top-inner { padding: 32px 36px 0 36px; }
        .brand { font-size: 11px; letter-spacing: 2px; color: #fdba74; }
        .title { font-size: 30px; font-weight: bold; line-height: 1.15; margin-top: 10px; }
        .meta { font-size: 14px; line-height: 1.5; margin-top: 14px; color: #f5f0ee; }
        table.bottom { width: 100%; border-collapse: collapse; }
        td.left { vertical-align: bottom; padding: 0 0 32px 36px; color: #ffffff; }
        td.right { width: 190px; vertical-align: bottom; text-align: right; padding: 0 36px 32px 0; }
        .type { font-size: 19px; font-weight: bold; }
        .holder { font-size: 14px; margin-top: 4px; }
        .code { font-family: DejaVu Sans Mono, monospace; font-size: 13px; margin-top: 10px; }
        .ref { font-size: 10px; color: #d6d3d1; margin-top: 6px; }
        .note { font-size: 11px; color: #78716c; margin: 14px 6px 0; }
    </style>
</head>
<body>
@foreach ($items as $item)
    @php $ticket = $item['ticket']; @endphp
    <div class="page" @if (! $loop->last) style="page-break-after: always;" @endif>
        <div class="card">
            <div class="top">
                <div class="top-inner">
                    <div class="brand">{{ strtoupper(config('app.name')) }}</div>
                    <div class="title">{{ $order->event->title }}</div>
                    <div class="meta">
                        {{ $order->event->starts_at->translatedFormat('l j F Y, H\hi') }}<br>
                        {{ $order->event->venue }}, {{ $order->event->city }}
                    </div>
                </div>
            </div>
            <table class="bottom">
                <tr>
                    <td class="left">
                        <div class="type">{{ $ticket->ticketType->name }}</div>
                        <div class="holder">{{ $ticket->holder_name }}</div>
                        <div class="code">{{ $ticket->code }}</div>
                        <div class="ref">Commande {{ $order->reference }}</div>
                    </td>
                    <td class="right">
                        <img src="{{ $item['qr'] }}" width="150" height="150" alt="QR code">
                    </td>
                </tr>
            </table>
        </div>
        <p class="note">Présentez ce billet à l'entrée (sur téléphone ou imprimé). Un billet ne peut être utilisé qu'une seule fois.</p>
    </div>
@endforeach
</body>
</html>
