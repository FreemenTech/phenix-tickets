<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ScannerController extends Controller
{
    public function index(): View
    {
        return view('scanner.index');
    }

    public function validateTicket(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string', 'max:500']]);

        // Le QR contient l'adresse du billet : on en garde le dernier morceau (le code)
        $raw = trim($request->input('code'));
        $path = parse_url($raw, PHP_URL_PATH) ?: $raw;
        $code = strtoupper(Str::afterLast($path, '/'));

        $ticket = Ticket::where('code', $code)
            ->with('ticketType', 'order.event.organizer')
            ->first();

        if (! $ticket) {
            return $this->result('invalid', 'Billet introuvable', ["Code : {$code}"]);
        }

        $order = $ticket->order;
        $event = $order->event;
        $user = $request->user();

        if (! $user->isAdmin() && $event->organizer->user_id !== $user->id) {
            return $this->result('forbidden', 'Billet d\'un autre organisateur', [$event->title]);
        }

        $lines = [
            $event->title,
            $ticket->ticketType->name.' : '.$ticket->holder_name,
            "Code : {$ticket->code}",
        ];

        // 1. Billet annulé : événement annulé, commande remboursée, commande supprimée...
        $voided = $ticket->status === 'cancelled'
            || in_array($order->status, ['refunded', 'refund_needed', 'cancelled'], true);

        if ($voided) {
            $title = match (true) {
                $event->status === 'cancelled' => 'Événement annulé',
                $order->status === 'refunded' => 'Billet remboursé',
                default => 'Billet annulé',
            };

            return $this->result('cancelled', $title, $lines);
        }

        // 2. Billet jamais payé
        if (! $order->isPaid() || $ticket->status === 'pending') {
            return $this->result('unpaid', 'Billet non payé', $lines);
        }

        // 3. Billet déjà scanné
        if ($ticket->status === 'used') {
            $lines[] = 'Scanné le '.$ticket->used_at?->translatedFormat('j F Y à H\hi');

            return $this->result('used', 'Billet déjà utilisé', $lines);
        }

        // 4. Validation atomique : si deux scans arrivent en même temps, un seul passe
        $updated = Ticket::where('id', $ticket->id)
            ->where('status', 'valid')
            ->update(['status' => 'used', 'used_at' => now()]);

        if (! $updated) {
            return $this->result('used', 'Billet déjà utilisé', $lines);
        }

        return $this->result('ok', 'Billet valide', $lines);
    }

    private function result(string $result, string $title, array $lines = []): JsonResponse
    {
        return response()->json(['result' => $result, 'title' => $title, 'lines' => $lines]);
    }
}
