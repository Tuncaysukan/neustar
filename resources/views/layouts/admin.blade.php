<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white min-h-screen">
            <div class="p-4 text-2xl font-bold border-b border-gray-700">
                Neustar Admin
            </div>
            <nav class="mt-4 overflow-y-auto" style="max-height: calc(100vh - 80px)">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">Dashboard</a>

                <div class="px-4 pt-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-gray-500">İçerik</div>
                <a href="{{ route('admin.operators.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.operators.*') ? 'bg-gray-700' : '' }}">Operatörler</a>
                <a href="{{ route('admin.packages.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.packages.*') ? 'bg-gray-700' : '' }}">Paketler</a>
                <a href="{{ route('admin.blogs.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.blogs.*') ? 'bg-gray-700' : '' }}">Blog</a>
                <a href="{{ route('admin.faqs.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.faqs.*') ? 'bg-gray-700' : '' }}">SSS</a>
                @php($pendingReviews = \App\Models\PackageReview::where('is_approved', false)->count())
                <a href="{{ route('admin.reviews.index') }}" class="flex items-center justify-between px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.reviews.*') ? 'bg-gray-700' : '' }}">
                    <span>Yorumlar</span>
                    @if($pendingReviews > 0)
                        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-500 text-white">{{ $pendingReviews }}</span>
                    @endif
                </a>

                <div class="px-4 pt-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-gray-500">Başvurular</div>
                @php($pendingLeads = \Illuminate\Support\Facades\Cache::remember('admin.leads.pending', 60, fn () => \App\Models\InfrastructureLead::where('status', 'new')->count()))
                <a href="{{ route('admin.infrastructure-leads.index') }}" class="flex items-center justify-between px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.infrastructure-leads.*') ? 'bg-gray-700' : '' }}">
                    <span>Başvurular</span>
                    @if($pendingLeads > 0)
                        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-500 text-white">{{ $pendingLeads }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.commitment-reminders.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.commitment-reminders.*') ? 'bg-gray-700' : '' }}">Taahhüt Hatırlatıcı</a>
                <a href="{{ route('admin.contact-messages.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.contact-messages.*') ? 'bg-gray-700' : '' }}">İletişim Mesajları</a>

                <div class="px-4 pt-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-gray-500">SEO & Ayarlar</div>
                <a href="{{ route('admin.seo.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.seo.*') ? 'bg-gray-700' : '' }}">SEO Yönetimi</a>
                <a href="{{ route('admin.tariff-seo.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.tariff-seo.*') ? 'bg-gray-700' : '' }}">Tarife SEO</a>
                <a href="{{ route('admin.location-meta.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.location-meta.*') ? 'bg-gray-700' : '' }}">Meta Şablonları</a>
                <a href="{{ route('admin.custom-code.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.custom-code.*') ? 'bg-gray-700' : '' }}">CSS / JS Editörü</a>
                <a href="{{ route('admin.site-settings.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.site-settings.*') ? 'bg-gray-700' : '' }}">Site Ayarları</a>
                <a href="/sitemap.xml" target="_blank" class="block px-4 py-2 hover:bg-gray-700 text-gray-400">Sitemap.xml ↗</a>

                <div class="px-4 pt-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-gray-500">Hesap</div>
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : '' }}">Kullanıcılar</a>
                <div class="border-t border-gray-700 mt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-600">Çıkış Yap</button>
                    </form>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        @yield('header')
                    </h2>
                </div>
            </header>

            <main class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
