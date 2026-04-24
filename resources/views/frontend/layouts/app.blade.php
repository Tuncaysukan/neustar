<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="neustar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Neustar'))</title>
    <meta name="description" content="@yield('meta_description', 'İnternet paketlerini karşılaştır, en uygununu seç.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="min-h-screen font-sans antialiased bg-base-200 text-base-content"
    x-data="{
        theme: localStorage.getItem('theme') || 'neustar',
        init() {
            const allowed = new Set(['neustar', 'neustar-dark']);
            if (!allowed.has(this.theme)) this.theme = 'neustar';
            document.documentElement.setAttribute('data-theme', this.theme);
        },
        toggleTheme() {
            this.theme = this.theme === 'neustar-dark' ? 'neustar' : 'neustar-dark';
            document.documentElement.setAttribute('data-theme', this.theme);
            localStorage.setItem('theme', this.theme);
        }
    }"
>
    @if(session('status'))
        <div class="fixed top-4 left-0 right-0 z-[60] px-4">
            <div class="mx-auto max-w-3xl">
                <div class="alert alert-success rounded-lg">
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            </div>
        </div>
    @endif

    <div class="drawer drawer-end">
        <input id="neustar-nav" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content">
            {{-- ============ Header ============ --}}
            <header class="sticky top-0 z-50 bg-base-100/90 backdrop-blur border-b border-base-300">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">

                        <div class="flex items-center gap-3">
                            <label for="neustar-nav" class="btn btn-ghost btn-sm btn-square lg:hidden" aria-label="Menüyü aç">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </label>

                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                                <span class="grid place-items-center h-8 w-8 rounded-md bg-neutral text-neutral-content font-bold">N</span>
                                <span class="text-base font-semibold tracking-tight">Neustar</span>
                            </a>
                        </div>

                        <nav class="hidden lg:flex items-center gap-1">
                            <a href="{{ route('home') }}" class="ns-navlink {{ request()->routeIs('home') ? 'ns-navlink-active' : '' }}">Ana Sayfa</a>
                            <a href="{{ route('packages.index') }}" class="ns-navlink {{ request()->routeIs('packages.*') ? 'ns-navlink-active' : '' }}">Paketler</a>
                            <a href="{{ route('operators.index') }}" class="ns-navlink {{ request()->routeIs('operators.*') ? 'ns-navlink-active' : '' }}">Markalar</a>
                            <a href="{{ route('compare') }}" class="ns-navlink {{ request()->routeIs('compare') ? 'ns-navlink-active' : '' }}">Karşılaştır</a>
                            <a href="{{ route('speed-test') }}" class="ns-navlink {{ request()->routeIs('speed-test') ? 'ns-navlink-active' : '' }}">Hız Testi</a>
                            <a href="{{ route('commitment-counter') }}" class="ns-navlink {{ request()->routeIs('commitment-counter') ? 'ns-navlink-active' : '' }}">Taahhüt</a>
                            <a href="{{ route('blog.index') }}" class="ns-navlink {{ request()->routeIs('blog.*') ? 'ns-navlink-active' : '' }}">Blog</a>
                        </nav>

                        <div class="flex items-center gap-2">
                            <button type="button" class="btn btn-ghost btn-sm btn-square" @click="toggleTheme()" aria-label="Tema değiştir">
                                <svg x-show="theme !== 'neustar-dark'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414m12.728 0l-1.414-1.414M7.05 7.05 5.636 5.636" />
                                </svg>
                                <svg x-show="theme === 'neustar-dark'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                                </svg>
                            </button>

                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">Hesabım</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            {{-- ============ Main ============ --}}
            <main class="min-h-[calc(100vh-64px)]">
                @yield('content')
            </main>

            {{-- ============ Footer ============ --}}
            <footer class="mt-16 ns-footer">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                        <div class="md:col-span-5">
                            <div class="flex items-center gap-2.5">
                                <span class="grid place-items-center h-8 w-8 rounded-md bg-primary text-primary-content font-bold">N</span>
                                <span class="text-base font-semibold">Neustar</span>
                            </div>
                            <p class="mt-4 text-sm leading-relaxed text-neutral-content/70 max-w-md">
                                İnternet paketlerini tek ekranda karşılaştır. Fiyat, hız ve taahhüt süresini yan yana gör.
                            </p>
                        </div>

                        <div class="md:col-span-7 grid grid-cols-2 sm:grid-cols-3 gap-8">
                            <div>
                                <div class="text-sm font-semibold text-neutral-content">Hızlı Erişim</div>
                                <ul class="mt-4 space-y-2.5 text-sm text-neutral-content/70">
                                    <li><a class="hover:text-neutral-content" href="{{ route('packages.index') }}">Paketler</a></li>
                                    <li><a class="hover:text-neutral-content" href="{{ route('operators.index') }}">Markalar</a></li>
                                    <li><a class="hover:text-neutral-content" href="{{ route('compare') }}">Karşılaştır</a></li>
                                </ul>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral-content">Araçlar</div>
                                <ul class="mt-4 space-y-2.5 text-sm text-neutral-content/70">
                                    <li><a class="hover:text-neutral-content" href="{{ route('speed-test') }}">Hız Testi</a></li>
                                    <li><a class="hover:text-neutral-content" href="{{ route('commitment-counter') }}">Taahhüt Sayacı</a></li>
                                    <li><a class="hover:text-neutral-content" href="{{ route('blog.index') }}">Blog Yazıları</a></li>
                                </ul>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral-content">Yasal</div>
                                <ul class="mt-4 space-y-2.5 text-sm text-neutral-content/70">
                                    <li><a class="hover:text-neutral-content" href="#">KVKK</a></li>
                                    <li><a class="hover:text-neutral-content" href="#">Çerezler</a></li>
                                    <li><a class="hover:text-neutral-content" href="#">İletişim</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- ── Disclaimer ── --}}
                    @php
                        $disclaimer = \App\Models\SeoContent::forKey('footer_disclaimer');
                    @endphp
                    @if($disclaimer && $disclaimer->content)
                    <div class="mt-10 rounded-xl border border-white/10 bg-white/5 p-5 sm:p-6 flex gap-4">
                        {{-- Info ikonu --}}
                        <div class="shrink-0 mt-0.5">
                            <div class="h-8 w-8 rounded-full border border-primary/40 bg-primary/10 text-primary grid place-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4M12 8h.01"/>
                                </svg>
                            </div>
                        </div>
                        <div class="min-w-0">
                            @if($disclaimer->title)
                            <p class="text-xs font-bold uppercase tracking-wider text-neutral-content/90 mb-2">
                                {{ $disclaimer->title }}
                            </p>
                            @endif
                            <p class="text-xs text-neutral-content/60 leading-relaxed">
                                {{ $disclaimer->content }}
                            </p>
                        </div>
                    </div>
                    @endif

                    <div class="mt-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between text-xs text-neutral-content/55">
                        <p>&copy; {{ date('Y') }} Neustar. Tüm hakları saklıdır.</p>
                        <p>Karşılaştırma platformudur. Kesin fiyatlar için operatörün sitesini ziyaret edin.</p>
                    </div>
                </div>
            </footer>

            {{-- ============ Compare bar ============ --}}
            <div x-data x-show="$store.compare.count > 0" x-transition
                 class="fixed bottom-4 left-0 right-0 z-50 px-4">
                <div class="mx-auto max-w-7xl">
                    <div class="rounded-xl border border-base-300 bg-base-100 shadow-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4">
                            <div class="flex items-center justify-between gap-3 flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-md bg-primary/10 text-primary grid place-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3v18M16 3v18M3 8h5M16 16h5"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-base-content/60">Karşılaştırma</div>
                                        <div class="text-sm font-semibold">
                                            <span x-text="$store.compare.count"></span> / <span x-text="$store.compare.max"></span> paket seçildi
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-ghost btn-sm" type="button" @click="$store.compare.clear()">Temizle</button>
                            </div>

                            <div class="flex items-center gap-2 justify-end">
                                <a class="btn btn-primary btn-sm" :href="$store.compare.url()">
                                    Karşılaştır
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Mobile drawer ============ --}}
        <div class="drawer-side z-[60]">
            <label for="neustar-nav" class="drawer-overlay" aria-label="Menüyü kapat"></label>
            <aside class="ns-drawer min-h-full w-[86vw] max-w-[340px] bg-base-100 flex flex-col">
                <div class="px-5 py-4 border-b border-base-300 flex items-center justify-between">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                        <span class="grid place-items-center h-8 w-8 rounded-md bg-neutral text-neutral-content font-bold">N</span>
                        <span class="text-base font-semibold">Neustar</span>
                    </a>
                    <label for="neustar-nav" class="btn btn-ghost btn-sm btn-square" aria-label="Kapat">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </label>
                </div>

                <nav class="flex-1 px-3 py-4 overflow-y-auto">
                    @php
                        $navItems = [
                            ['route' => 'home',               'label' => 'Ana Sayfa',         'match' => 'home'],
                            ['route' => 'packages.index',     'label' => 'Paketler',          'match' => 'packages.*'],
                            ['route' => 'operators.index',    'label' => 'Markalar',          'match' => 'operators.*'],
                            ['route' => 'compare',            'label' => 'Karşılaştır',       'match' => 'compare'],
                            ['route' => 'speed-test',         'label' => 'Hız Testi',         'match' => 'speed-test'],
                            ['route' => 'commitment-counter', 'label' => 'Taahhüt Sayacı',    'match' => 'commitment-counter'],
                            ['route' => 'blog.index',         'label' => 'Blog',              'match' => 'blog.*'],
                        ];
                    @endphp

                    <ul class="list-none p-0 m-0 space-y-1">
                        @foreach($navItems as $item)
                            @php $active = request()->routeIs($item['match']); @endphp
                            <li class="list-none">
                                <a href="{{ route($item['route']) }}"
                                   class="ns-drawer-link {{ $active ? 'ns-drawer-link--active' : '' }}">
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                    @if(!$active)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                             class="h-4 w-4 text-base-content/30">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <div class="mt-auto p-4 border-t border-base-300 space-y-2.5 bg-base-100">
                    <button type="button"
                            class="w-full inline-flex items-center justify-between rounded-md border border-base-300 bg-base-100 px-3 py-2.5 text-sm font-medium hover:bg-base-200 transition"
                            @click="toggleTheme()">
                        <span class="inline-flex items-center gap-2.5">
                            <svg x-show="theme !== 'neustar-dark'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-primary">
                                <circle cx="12" cy="12" r="4"/>
                                <path d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414m12.728 0l-1.414-1.414M7.05 7.05 5.636 5.636"/>
                            </svg>
                            <svg x-show="theme === 'neustar-dark'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-primary">
                                <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
                            </svg>
                            <span x-text="theme === 'neustar-dark' ? 'Koyu tema' : 'Açık tema'"></span>
                        </span>
                        <span class="text-xs text-base-content/50">Değiştir</span>
                    </button>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm w-full">Hesabım</a>
                    @endauth
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
