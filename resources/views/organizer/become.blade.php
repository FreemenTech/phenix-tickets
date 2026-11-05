<x-public-layout title="Ouvrir un espace organisateur">
    <div class="mx-auto max-w-xl px-6 py-12">
        <h1 class="font-display text-3xl font-bold">Ouvrir un espace organisateur</h1>
        <p class="mt-3 text-stone-600">Vous êtes déjà connecté : il suffit de nous donner le nom de votre structure. Une commission de 10 % est prélevée sur chaque billet vendu.</p>

        <form method="POST" action="{{ route('organizer.become.store') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label class="field-label" for="structure_name">Nom de votre structure</label>
                <input class="field" id="structure_name" name="structure_name" value="{{ old('structure_name') }}" required>
                @error('structure_name') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label" for="phone">Téléphone (WhatsApp ou Mobile Money)</label>
                <input class="field" id="phone" name="phone" type="tel" value="{{ old('phone', auth()->user()->phone) }}" required>
                @error('phone') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-start gap-3 text-sm">
                <input type="checkbox" name="conditions" value="1" class="mt-1" required @checked(old('conditions'))>
                <span>J'ai lu et j'accepte les <a href="{{ route('legal.terms') }}" target="_blank" class="underline">conditions d'utilisation</a>, dont la commission de 10 % et les reversements après l'événement.</span>
            </label>
            @error('conditions') <p class="field-error">{{ $message }}</p> @enderror

            <button type="submit" class="btn-flame">Créer mon espace organisateur</button>
        </form>
    </div>
</x-public-layout>
