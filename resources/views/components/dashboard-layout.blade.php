@props(['title' => null, 'area' => 'organizer'])
@php
    $user = auth()->user();

    $icons = [
        'grid' => 'M4 4h7v7H4zM13 4h7v4h-7zM13 10h7v10h-7zM4 13h7v7H4z',
        'calendar' => 'M4 6h16v14H4zM4 10h16M8 3v4M16 3v4',
        'plus' => 'M12 5v14M5 12h14',
        'scan' => 'M4 8V4h4M16 4h4v4M20 16v4h-4M8 20H4v-4M4 12h16',
        'wallet' => 'M3 7h18v12H3zM3 7l3-3h12l3 3M16 13h2',
        'users' => 'M16 8a4 4 0 1 0-8 0 4 4 0 0 0 8 0zM4 20c0-4 4-6 8-6s8 2 8 6',
        'receipt' => 'M5 4h14v16l-3-2-2 2-2-2-2 2-2-2-3 2zM9 9h6M9 13h6',
        'mail' => 'M4 6h16v12H4zM4 7l8 6 8-6',
    ];

    $nav = $area === 'admin'
        ? [
            ['admin.home', 'Tableau de bord', 'grid', ['admin.home']],
            ['admin.organizers', 'Organisateurs', 'users', ['admin.organizers*']],
            ['admin.events', 'Événements', 'calendar', ['admin.events*']],
            ['admin.sales', 'Ventes', 'receipt', ['admin.sales']],
            ['admin.messages', 'Messages', 'mail', ['admin.messages*']],
            ['admin.payouts', 'Reversements', 'wallet', ['admin.payouts*']],
            ['scanner.index', 'Scanner', 'scan', ['scanner.*']],
        ]
        : [
            ['organizer.dashboard', 'Tableau de bord', 'grid', ['organizer.dashboard']],
            ['organizer.events.index', 'Mes événements', 'calendar', ['organizer.events.index', 'organizer.events.show', 'organizer.events.edit']],
            ['organizer.events.create', 'Créer un événement', 'plus', ['organizer.events.create']],
            ['scanner.index', 'Scanner', 'scan', ['scanner.*']],
            ['organizer.payouts', 'Mes reversements', 'wallet', ['organizer.payouts*']],
        ];

    $pendingOrganizers = $area === 'admin' ? \App\Models\Organizer::where('status', 'pending')->count() : 0;
    $unreadMessages = $area === 'admin' ? \App\Models\ContactMessage::whereNull('read_at')->count() : 0;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' | ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:600,700,800|figtree:400,500,600" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="dash">
    <aside class="dash-side">
        <a href="/" class="font-display flex items-center gap-3 text-lg font-bold">
            <img src="{{ asset('images/phenix-emblem.png') }}" alt="" class="h-10 w-10 object-contain">
            <span>{{ config('app.name') }}</span>
        </a>
        <p class="dash-area">{{ $area === 'admin' ? 'Administration' : 'Espace organisateur' }}</p>

        <nav class="dash-nav">
            @foreach ($nav as [$route, $label, $icon, $patterns])
                <a href="{{ route($route) }}" class="dash-link {{ request()->routeIs(...$patterns) ? 'dash-link--active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 shrink-0" aria-hidden="true"><path d="{{ $icons[$icon] }}"/></svg>
                    <span>{{ $label }}</span>
                    @if ($route === 'admin.messages' && $unreadMessages > 0)
                        <span class="dash-badge">{{ $unreadMessages }}</span>
                    @endif
                    @if ($route === 'admin.organizers' && $pendingOrganizers > 0)
                        <span class="dash-badge">{{ $pendingOrganizers }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="dash-foot">
            <p class="font-semibold text-white">{{ $user->name }}</p>
            <a href="/">Voir le site</a>
            @if ($area === 'organizer' && $user->isAdmin())
                <a href="{{ route('admin.home') }}">Espace admin</a>
            @endif
            @if ($area === 'admin' && $user->isOrganizer())
                <a href="{{ route('organizer.dashboard') }}">Espace organisateur</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-left">Déconnexion</button>
            </form>
        </div>
    </aside>

    <main class="dash-main">
        {{ $slot }}
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    if (window.Chart) {
        Chart.defaults.font.family = "'Figtree', sans-serif";
        Chart.defaults.color = '#57534e';
    }
</script>
@stack('scripts')
</body>
</html>
