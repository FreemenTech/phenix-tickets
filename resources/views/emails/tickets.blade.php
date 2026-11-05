@php
    $event = $order->event;
    $banner = \App\Support\TicketPoster::banner($event->cover_image, focusX: (int) $event->cover_focus_x, focusY: (int) $event->cover_focus_y);
    $bannerCid = $banner ? $message->embedData($banner, 'affiche.jpg', 'image/jpeg') : null;
    $logoCid = $message->embed(public_path('images/phenix-emblem.png'));
    $contact = config('billetterie.contact');
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f3efec;font-family:Arial,Helvetica,sans-serif;color:#1f1418;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3efec;">
<tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;">

    <tr>
        <td style="background:#2b0a1f;border-radius:20px 20px 0 0;padding:18px 28px;">
            <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="48"><img src="{{ $logoCid }}" width="44" height="44" alt="" style="display:block;border:0;"></td>
                    <td style="padding-left:12px;color:#ffffff;font-size:20px;font-weight:bold;">{{ config('app.name') }}</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="background:#ffffff;border-radius:0 0 20px 20px;padding:28px;">
            <h1 style="margin:0 0 12px;font-size:26px;line-height:1.2;">Vos billets sont prêts</h1>
            <p style="margin:0 0 10px;font-size:15px;line-height:1.6;">Bonjour {{ $order->buyer_name }}, merci pour votre commande <strong>{{ $order->reference }}</strong>.</p>
            <p style="margin:0;font-size:15px;line-height:1.6;color:#57534e;">
                À l'entrée, présentez le QR code de chaque billet, sur votre téléphone (écran au maximum de luminosité) ou imprimé.
                Un billet ne peut être utilisé qu'une seule fois.
            </p>
        </td>
    </tr>

    <tr><td style="height:16px;line-height:16px;font-size:0;">&nbsp;</td></tr>

    @foreach ($order->tickets as $ticket)
        <tr>
            <td style="padding-bottom:16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;">
                    @if ($bannerCid)
                        <tr>
                            <td><img src="{{ $bannerCid }}" width="600" alt="{{ $event->title }}" style="display:block;width:100%;height:auto;border:0;"></td>
                        </tr>
                    @endif
                    <tr>
                        <td style="background:#2b0a1f;padding:18px 24px;">
                            <div style="color:#fdba74;font-size:12px;letter-spacing:1px;">{{ strtoupper($ticket->ticketType->name) }}</div>
                            <div style="color:#ffffff;font-size:22px;font-weight:bold;line-height:1.25;margin-top:4px;">{{ $event->title }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td valign="top" style="font-size:14px;line-height:1.6;">
                                        <div style="color:#78716c;font-size:11px;text-transform:uppercase;">Date</div>
                                        <div style="margin-bottom:10px;">{{ $event->starts_at->translatedFormat('l j F Y, H\hi') }}</div>
                                        <div style="color:#78716c;font-size:11px;text-transform:uppercase;">Lieu</div>
                                        <div style="margin-bottom:10px;">{{ $event->venue }}, {{ $event->city }}</div>
                                        <div style="color:#78716c;font-size:11px;text-transform:uppercase;">Titulaire</div>
                                        <div>{{ $ticket->holder_name }}</div>
                                    </td>
                                    <td valign="top" align="right" width="170">
                                        <img src="{{ $message->embedData(\App\Support\TicketQr::png($ticket), 'qr-'.$ticket->code.'.png', 'image/png') }}" width="150" height="150" alt="QR code du billet {{ $ticket->code }}" style="display:block;border:0;">
                                        <div style="width:150px;text-align:center;font-family:Courier New,monospace;font-size:12px;margin-top:6px;">{{ $ticket->code }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top:2px dashed #e4361b;padding:12px 24px;font-size:12px;color:#78716c;">
                            Billet {{ $loop->iteration }} sur {{ $loop->count }}, commande {{ $order->reference }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @endforeach

    <tr>
        <td align="center" style="padding:8px 0 20px;">
            <a href="{{ route('orders.show', $order->reference) }}" style="display:inline-block;background:#e4361b;color:#ffffff;text-decoration:none;padding:14px 28px;border-radius:999px;font-weight:bold;font-size:15px;">Voir mes billets en ligne</a>
            <p style="margin:12px 0 0;font-size:13px;color:#78716c;">Vos billets sont aussi en pièce jointe au format PDF.</p>
        </td>
    </tr>

    <tr>
        <td align="center" style="padding:0 12px 8px;font-size:12px;line-height:1.7;color:#78716c;">
            Une question ?
            <a href="{{ route('help') }}" style="color:#78716c;">Aide</a> |
            <a href="{{ route('contact') }}" style="color:#78716c;">Contactez-nous</a> |
            <a href="https://wa.me/{{ $contact['whatsapp_number'] }}" style="color:#78716c;">WhatsApp {{ $contact['whatsapp_display'] }}</a>
            <br>{{ config('app.name') }}, billetterie en ligne au Bénin.
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>
