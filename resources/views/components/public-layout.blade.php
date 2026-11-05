@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' | ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:600,700,800|figtree:400,500,600" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col antialiased">
    <header class="site-header">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-6 py-3">
            <a href="/" class="font-display flex items-center gap-3 text-xl font-bold">
                <img src="{{ asset('images/phenix-emblem.png') }}" alt="" class="h-11 w-11 object-contain">
                {{ config('app.name') }}
            </a>
            <nav class="flex flex-wrap items-center gap-4 text-sm font-medium">
                <a href="/#evenements" class="hidden hover:underline sm:inline">Événements</a>
                @auth
                    @php $navUser = auth()->user(); @endphp
                    @if ($navUser->isAdmin())
                        <a href="{{ route('admin.home') }}" class="hover:underline">Espace admin</a>
                    @endif
                    @if ($navUser->isOrganizer())
                        <a href="{{ route('organizer.dashboard') }}" class="hover:underline">Espace organisateur</a>
                    @endif
                    @if ($navUser->isAdmin() || $navUser->isOrganizer())
                        <a href="{{ route('scanner.index') }}" class="hover:underline">Scanner</a>
                    @endif
                    <a href="{{ route('orders.index') }}" class="hover:underline">Mes billets</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:underline">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Connexion</a>
                    <a href="{{ route('register') }}" class="btn-flame btn-sm">Créer un compte</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-site-footer />
</body>
</html>
