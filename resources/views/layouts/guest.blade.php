@php
    $wide = $attributes->get('wide', false);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:600,700,800|figtree:400,500,600" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="hero hero-photo flex min-h-screen flex-col items-center justify-center px-6 py-10" style="--hero-photo: url('{{ asset('images/hero-phenix.jpeg') }}')">
        <a href="/" class="font-display mb-6 flex items-center gap-3 text-2xl font-bold">
            <img src="{{ asset('images/phenix-emblem.png') }}" alt="" class="h-14 w-14 object-contain">
            {{ config('app.name') }}
        </a>

        <div class="w-full {{ $wide ? 'max-w-xl' : 'max-w-md' }} rounded-3xl bg-white p-6 text-stone-900 shadow-xl sm:p-8">
            {{ $slot }}
        </div>

        <p class="mt-6 flex flex-wrap justify-center gap-4 text-sm text-white/70">
            <a href="/" class="underline">Retour au site</a>
            <a href="{{ route('help') }}" class="underline">Aide</a>
            <a href="{{ route('contact') }}" class="underline">Contactez-nous</a>
        </p>
    </div>
</body>
</html>
