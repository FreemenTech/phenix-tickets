<x-guest-layout>
    <h1 class="font-display text-2xl font-bold">Créer un compte</h1>
    <p class="mt-1 text-sm text-stone-600">Pour acheter vos billets et les retrouver à tout moment.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="field-label" for="name">Nom</label>
            <input class="field" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="field-label" for="email">Email</label>
            <input class="field" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="field-label" for="password">Mot de passe</label>
            <input class="field" id="password" name="password" type="password" autocomplete="new-password" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="field-label" for="password_confirmation">Confirmer le mot de passe</label>
            <input class="field" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </div>

        <label class="flex items-start gap-3 text-sm">
            <input type="checkbox" name="conditions" value="1" class="mt-1" required @checked(old('conditions'))>
            <span>J'ai lu et j'accepte les <a href="{{ route('legal.terms') }}" target="_blank" class="underline">conditions d'utilisation</a> et la <a href="{{ route('legal.privacy') }}" target="_blank" class="underline">politique de confidentialité</a>.</span>
        </label>
        @error('conditions') <p class="field-error">{{ $message }}</p> @enderror

        <button type="submit" class="btn-flame w-full">Créer mon compte</button>
    </form>

    <p class="mt-6 text-center text-sm text-stone-600">
        Déjà inscrit ? <a href="{{ route('login') }}" class="underline">Se connecter</a><br>
        <span class="mt-1 inline-block">Vous organisez des événements ? <a href="{{ route('organizer.register') }}" class="underline">Ouvrir un espace organisateur</a></span>
    </p>
</x-guest-layout>
