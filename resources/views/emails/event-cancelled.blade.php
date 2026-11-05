@php
    $event = $order->event;
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
            <h1 style="margin:0 0 14px;font-size:24px;line-height:1.2;">Événement annulé</h1>
            <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">Bonjour {{ $order->buyer_name }},</p>
            <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
                Nous sommes au regret de vous informer que l'événement <strong>{{ $event->title }}</strong>,
                prévu le {{ $event->starts_at->translatedFormat('l j F Y, H\hi') }} ({{ $event->venue }}, {{ $event->city }}), est annulé.
            </p>
            <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
                Les billets de votre commande <strong>{{ $order->reference }}</strong> ne sont plus valables.
                Le prix payé, <strong>{{ number_format($order->total, 0, ',', ' ') }} FCFA</strong>, vous sera remboursé.
                Nous vous contacterons si nous avons besoin d'informations de votre part.
            </p>
            <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#57534e;">
                Une question ? Écrivez-nous avec la référence de votre commande.
            </p>
            <a href="{{ route('contact') }}" style="display:inline-block;background:#e4361b;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:999px;font-weight:bold;font-size:15px;">Contactez-nous</a>
            <p style="margin:16px 0 0;font-size:13px;color:#78716c;">Ou par WhatsApp : <a href="https://wa.me/{{ $contact['whatsapp_number'] }}" style="color:#78716c;">{{ $contact['whatsapp_display'] }}</a></p>
        </td>
    </tr>
</table>
</td></tr>
</table>
</body>
</html>
