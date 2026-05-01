<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="neustar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Neustar') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200 text-base-content">

    {{-- Header --}}
    <header class="bg-base-100 border-b border-base-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 no-underline">
                <img src="{{ asset('images/logo-net-karsilastir.png') }}"
                     alt="{{ config('app.name') }}"
                     class="h-8 w-auto object-contain">
            </a>
            <a href="{{ route('home') }}" class="text-sm text-base-content/60 hover:text-base-content transition">
                ← Ana sayfaya dön
            </a>
        </div>
    </header>

    {{-- Content --}}
   a <main class="min-h-[calc(100vh-64px)] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </main>

</body>
</html>
