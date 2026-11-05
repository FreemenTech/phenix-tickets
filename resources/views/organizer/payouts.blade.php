@php
    $money = fn ($n) => number_format($n, 0, ',', ' ').' FCFA';
@endphp
<x-dashboard-layout title="Mes reversements" area="organizer">
    <h1 class="font-display text-3xl font-bold">Mes reversements</h1>
    <p class="mt-1 text-stone-600">
        Vous êtes payé après la fin de chaque événement, plus {{ $delay }} jour(s). Votre part est de {{ 100 - $organizer->commission_rate }} % des billets vendus.
    </p>

    @if (session('status'))
        <p class="mt-6 rounded-xl border border-green-300 bg-green-50 p-4 text-sm">{{ session('status') }}</p>
    @endif

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi label="Net gagné" :value="$money($balance['net'])" :hint="'après commission de '.$organizer->commission_rate.' %'" />
        <x-kpi label="Déjà versé" :value="$money($balance['paid_out'])" />
        <x-kpi label="À recevoir bientôt" :value="$money($balance['payable'])" hint="événements terminés" />
        <x-kpi label="En attente de l'événement" :value="$money($balance['waiting'])" hint="événements à venir ou en cours" />
    </div>

    <div class="panel mt-8">
        <h2 class="font-display text-lg font-bold">Vos événements</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr><th>Événement</th><th>Votre part</th><th>Versement prévu à partir du</th><th>Statut</th></tr>
                </thead>
                <tbody>
                    @forelse ($events as $row)
                        <tr>
                            <td class="font-semibold">{{ $row['event']->title }}</td>
                            <td>{{ $money($row['net']) }}</td>
                            <td>{{ $row['release']->translatedFormat('j F Y') }}</td>
                            <td>
                                <span class="pill {{ $row['released'] ? 'pill-ok' : 'pill-warn' }}">
                                    {{ $row['released'] ? 'Disponible' : 'Après l\'événement' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-stone-600">Aucune vente pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="panel">
            <h2 class="font-display text-lg font-bold">Où recevoir vos versements</h2>
            <form method="POST" action="{{ route('organizer.payouts.profile') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="field-label" for="payout_operator">Opérateur Mobile Money</label>
                    <select class="field" id="payout_operator" name="payout_operator" required>
                        <option value="">Choisir</option>
                        <option value="MTN" @selected(old('payout_operator', $organizer->payout_operator) === 'MTN')>MTN MoMo</option>
                        <option value="Moov" @selected(old('payout_operator', $organizer->payout_operator) === 'Moov')>Moov Money</option>
                    </select>
                    @error('payout_operator') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="payout_phone">Numéro</label>
                    <input class="field" id="payout_phone" name="payout_phone" type="tel" value="{{ old('payout_phone', $organizer->payout_phone) }}" required>
                    @error('payout_phone') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn-flame">Enregistrer</button>
            </form>
        </div>

        <div class="panel">
            <h2 class="font-display text-lg font-bold">Historique des versements</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="table-clean">
                    <thead>
                        <tr><th>Date</th><th>Montant</th><th>Référence</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($payouts as $payout)
                            <tr>
                                <td>{{ ($payout->paid_at ?? $payout->created_at)->translatedFormat('j M Y') }}</td>
                                <td>{{ $money($payout->amount) }}</td>
                                <td class="font-mono text-xs">{{ $payout->reference ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-stone-600">Aucun versement pour le moment.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>
