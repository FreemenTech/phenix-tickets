<x-guest-layout :wide="true">
    <h1 class="font-display text-2xl font-bold">Vendez vos billets en ligne</h1>
    <p class="mt-1 text-sm text-stone-600">Créez votre espace organisateur. Une commission de 10 % est prélevée sur chaque billet vendu, le reste vous est reversé après l'événement.</p>

    <form method="POST" action="{{ route('organizer.register.store') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="field-label" for="structure_name">Nom de votre structure</label>
            <input class="field" id="structure_name" name="structure_name" value="{{ old('structure_name') }}" required autofocus>
            @error('structure_name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="field-label" for="name">Votre nom</label>
                <input class="field" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
                @error('name') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label" for="phone">Téléphone (WhatsApp ou Mobile Money)</label>
                <input class="field" id="phone" name="phone" type="tel" value="{{ old('phone') }}" required>
                @error('phone') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>
        <div>
            <label class="field-label" for="email">Email</label>
            <input class="field" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="field-label" for="password">Mot de passe</label>
                <input class="field" id="password" name="password" type="password" autocomplete="new-password" required>
                @error('password') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label" for="password_confirmation">Confirmer le mot de passe</label>
                <input class="field" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            </div>
        </div>

        <label class="flex items-start gap-3 text-sm">
            <input type="checkbox" name="conditions" value="1" class="mt-1" required @checked(old('conditions'))>
            <span>J'ai lu et j'accepte les <a href="{{ route('legal.terms') }}" target="_blank" class="underline">conditions d'utilisation</a>, dont la commission de 10 % et les reversements après l'événement, et la <a href="{{ route('legal.privacy') }}" target="_blank" class="underline">politique de confidentialité</a>.</span>
        </label>
        @error('conditions') <p class="field-error">{{ $message }}</p> @enderror

        <button type="submit" class="btn-flame w-full">Créer mon espace organisateur</button>
    </form>

    <p class="mt-6 text-center text-sm text-stone-600">
        Déjà un compte ? <a href="{{ route('login') }}" class="underline">Se connecter</a>
    </p>
</x-guest-layout>
