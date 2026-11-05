@php
    $money = fn ($n) => number_format($n, 0, ',', ' ').' FCFA';
@endphp
<x-dashboard-layout title="Reversements" area="admin">
    <h1 class="font-display text-3xl font-bold">Reversements</h1>
    <p class="mt-1 text-stone-600">
        Règle : un organisateur est payé après la fin de son événement, plus {{ $delay }} jour(s) (le temps que FedaPay libère les fonds).
        Les ventes des événements en cours restent « en attente ».
    </p>

    @if (session('status'))
        <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
    @endif
    @error('payout')
        <p class="mt-6 rounded-xl border border-red-300 bg-red-50 p-4 text-sm">{{ $message }}</p>
    @enderror

    <div class="panel mt-8">
        <h2 class="font-display text-lg font-bold">Soldes par organisateur</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr><th>Structure</th><th>Où verser</th><th>Net gagné</th><th>Déjà versé</th><th>À verser maintenant</th><th>En attente de l'événement</th><th>Enregistrer un versement</th></tr>
                </thead>
                <tbody>
                    @forelse ($organizers as $organizer)
                        @php $b = $balances[$organizer->id] ?? ['net' => 0, 'paid_out' => 0, 'due' => 0, 'payable' => 0, 'waiting' => 0]; @endphp
                        <tr>
                            <td class="font-semibold">{{ $organizer->name }}</td>
                            <td>
                                @if ($organizer->payout_phone)
                                    {{ $organizer->payout_operator }} {{ $organizer->payout_phone }}
                                @else
                                    <span class="text-stone-500">Non renseigné</span>
                                @endif
                            </td>
                            <td>{{ $money($b['net']) }}</td>
                            <td>
                                {{ $money($b['paid_out']) }}
                                @if (($b['recover'] ?? 0) > 0)
                                    <br><span class="text-xs text-red-700">À récupérer : {{ $money($b['recover']) }}</span>
                                @endif
                            </td>
                            <td class="font-semibold">{{ $money($b['payable']) }}</td>
                            <td class="text-stone-600">{{ $money($b['waiting']) }}</td>
                            <td>
                                @if ($b['payable'] > 0)
                                    <form method="POST" action="{{ route('admin.payouts.store') }}" class="flex flex-wrap gap-2">
                                        @csrf
                                        <input type="hidden" name="organizer_id" value="{{ $organizer->id }}">
                                        <input class="field" style="width: 8rem" type="number" name="amount" min="1" max="{{ $b['payable'] }}" value="{{ $b['payable'] }}" required>
                                        <input class="field" style="width: 9rem" name="reference" placeholder="Référence MoMo">
                                        <button type="submit" class="btn-flame btn-sm">Enregistrer</button>
                                    </form>
                                @else
                                    <span class="text-stone-500">Rien à verser maintenant</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-stone-600">Aucun organisateur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel mt-6">
        <h2 class="font-display text-lg font-bold">Derniers versements</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr><th>Date</th><th>Organisateur</th><th>Montant</th><th>Référence</th></tr>
                </thead>
                <tbody>
                    @forelse ($payouts as $payout)
                        <tr>
                            <td>{{ ($payout->paid_at ?? $payout->created_at)->translatedFormat('j M Y, H\hi') }}</td>
                            <td>{{ $payout->organizer->name }}</td>
                            <td>{{ $money($payout->amount) }}</td>
                            <td class="font-mono text-xs">{{ $payout->reference ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-stone-600">Aucun versement enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-layout>
