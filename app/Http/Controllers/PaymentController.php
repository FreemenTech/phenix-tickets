<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentConfirmation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /** Crée la transaction côté serveur puis envoie le client sur la page de paiement FedaPay. */
    public function pay(Order $order): RedirectResponse
    {
        $back = redirect()->route('orders.show', $order->reference);

        if ($order->isPaid()) {
            return $back;
        }

        abort_unless($order->status === 'pending' && $order->total > 0, 404);

        if (! config('fedapay.secret_key')) {
            return $back->with('error', 'Le paiement n\'est pas encore configuré.');
        }

        \FedaPay\FedaPay::setApiKey(config('fedapay.secret_key'));
        \FedaPay\FedaPay::setEnvironment(config('fedapay.environment'));

        try {
            $name = Str::of($order->buyer_name)->squish();
            $firstname = (string) $name->before(' ');
            $lastname = $name->contains(' ') ? (string) $name->after(' ') : $firstname;

            $transaction = \FedaPay\Transaction::create([
                'description' => "Billets {$order->event->title} ({$order->reference})",
                'amount' => $order->total,
                'currency' => ['iso' => 'XOF'],
                'callback_url' => route('payment.callback'),
                'customer' => [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $order->buyer_email,
                    'phone_number' => [
                        'number' => $this->normalizePhone($order->buyer_phone),
                        'country' => 'bj',
                    ],
                ],
            ]);

            $token = $transaction->generateToken();

            $order->update([
                'payment_provider' => 'fedapay',
                'payment_reference' => (string) $transaction->id,
            ]);

            return redirect()->away($token->url);
        } catch (\Throwable $e) {
            report($e);

            return $back->with('error', 'Impossible d\'ouvrir le paiement pour le moment. Réessayez dans un instant.');
        }
    }

    /** Retour du client depuis FedaPay. */
    public function callback(Request $request, PaymentConfirmation $confirmation): RedirectResponse
    {
        $transactionId = (string) $request->query('id');

        $order = Order::where('payment_provider', 'fedapay')
            ->where('payment_reference', $transactionId)
            ->first();

        abort_unless($order, 404);

        $back = redirect()->route('orders.show', $order->reference);

        return match ($confirmation->confirm($order)) {
            'paid' => $back->with('status', 'Paiement confirmé. Vos billets sont prêts.'),
            'refund_needed' => $back->with('error', "Votre paiement a bien été reçu, mais les places ne sont plus disponibles. Contactez-nous avec la référence {$order->reference} pour un remboursement."),
            'failed' => $back->with('error', 'Le paiement a été annulé ou refusé. Vous pouvez réessayer.'),
            'error' => $back->with('error', 'Impossible de vérifier le paiement pour le moment. Rechargez la page dans un instant.'),
            default => $back->with('error', 'Paiement en cours de confirmation. Rechargez cette page dans un instant.'),
        };
    }

    /** Confirmation de secours : FedaPay prévient notre serveur même si le client a fermé la page. */
    public function webhook(Request $request, PaymentConfirmation $confirmation): Response
    {
        $raw = $request->getContent();
        $secret = config('fedapay.webhook_secret');

        if ($secret) {
            try {
                \FedaPay\Webhook::constructEvent($raw, (string) $request->header('X-FEDAPAY-SIGNATURE'), $secret);
            } catch (\Throwable $e) {
                report($e);

                return response('Signature invalide', 400);
            }
        }

        $payload = json_decode($raw, true) ?: [];
        $transactionId = (string) (data_get($payload, 'entity.id') ?? data_get($payload, 'object.id') ?? '');

        if ($transactionId === '') {
            return response('OK', 200);
        }

        $order = Order::where('payment_provider', 'fedapay')
            ->where('payment_reference', $transactionId)
            ->first();

        if ($order && $confirmation->confirm($order) === 'error') {
            // FedaPay réessaiera plus tard
            return response('Réessayez', 500);
        }

        return response('OK', 200);
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (! str_starts_with($digits, '229')) {
            $digits = '229'.$digits;
        }

        return '+'.$digits;
    }
}
