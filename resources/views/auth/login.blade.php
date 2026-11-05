<x-guest-layout>
    <h1 class="font-display text-2xl font-bold">Connexion</h1>
    <p class="mt-1 text-sm text-stone-600">Retrouvez vos billets et vos commandes.</p>

    @if (session('status'))
        <p class="mt-4 rounded-xl border border-green-300 bg-green-50 p-3 text-sm">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="field-label" for="email">Email</label>
            <input class="field" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="field-label" for="password">Mot de passe</label>
            <input class="field" id="password" name="password" type="password" autocomplete="current-password" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" value="1"> Rester connecté
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="underline">Mot de passe oublié ?</a>
            @endif
        </div>
        <button type="submit" class="btn-flame w-full">Se connecter</button>
    </form>

    <p class="mt-6 text-center text-sm text-stone-600">
        Pas encore de compte ? <a href="{{ route('register') }}" class="underline">Créer un compte</a><br>
        <span class="mt-1 inline-block">Vous organisez des événements ? <a href="{{ route('organizer.register') }}" class="underline">Ouvrir un espace organisateur</a></span>
    </p>
</x-guest-layout>
