@extends('frontend.layouts.app')

@section('title', 'Sayfa Bulunamadı — Neustar')
@section('meta_description', 'Aradığınız sayfa bulunamadı.')

@section('content')
<section class="min-h-[70vh] flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-lg text-center">

        {{-- 404 büyük yazı --}}
        <div class="relative inline-block">
            <span class="text-[100px] sm:text-[160px] font-black leading-none select-none"
                  style="color: transparent; -webkit-text-stroke: 2px #1bb6ad; opacity: 0.15;">
                404
            </span>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-primary" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Başlık --}}
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-center">
            Sayfa bulunamadı
        </h1>
        <p class="mt-3 text-sm sm:text-base text-base-content/60 leading-relaxed max-w-sm mx-auto text-center">
            Aradığınız sayfa taşınmış, silinmiş ya da hiç var olmamış olabilir.
            Aşağıdaki bağlantılardan devam edebilirsiniz.
        </p>

        {{-- Hızlı linkler --}}
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-primary">
                Ana Sayfaya Dön
            </a>
            <a href="{{ route('packages.index') }}" class="btn btn-outline">
                İnternet Paketleri
            </a>
            <a href="{{ route('blog.index') }}" class="btn btn-ghost btn-sm">
                Blog
            </a>
        </div>

        {{-- Arama kutusu --}}
        <div class="mt-8 max-w-sm mx-auto">
            <p class="text-xs text-base-content/45 mb-2 text-center">Veya paket arayın:</p>
            <form action="{{ route('packages.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="q"
                       placeholder="Operatör veya paket adı…"
                       class="input input-bordered flex-1 rounded-md text-sm">
                <button type="submit" class="btn btn-primary btn-sm">Ara</button>
            </form>
        </div>

        {{-- Popüler sayfalar --}}
        <div class="mt-8 pt-6 border-t border-base-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-base-content/40 mb-3 text-center">
                Popüler sayfalar
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                <a href="{{ route('packages.index') }}"
                   class="ns-surface ns-surface--hover rounded-lg px-3 py-2.5 text-base-content/70 hover:text-primary transition no-underline text-left">
                    📦 İnternet Paketleri
                </a>
                <a href="{{ route('operators.index') }}"
                   class="ns-surface ns-surface--hover rounded-lg px-3 py-2.5 text-base-content/70 hover:text-primary transition no-underline text-left">
                    🏢 Markalar
                </a>
                <a href="{{ route('compare') }}"
                   class="ns-surface ns-surface--hover rounded-lg px-3 py-2.5 text-base-content/70 hover:text-primary transition no-underline text-left">
                    ⚖️ Karşılaştır
                </a>
                <a href="{{ route('speed-test') }}"
                   class="ns-surface ns-surface--hover rounded-lg px-3 py-2.5 text-base-content/70 hover:text-primary transition no-underline text-left">
                    ⚡ Hız Testi
                </a>
                <a href="{{ route('commitment-counter') }}"
                   class="ns-surface ns-surface--hover rounded-lg px-3 py-2.5 text-base-content/70 hover:text-primary transition no-underline text-left">
                    ⏱️ Taahhüt Sayacı
                </a>
                <a href="{{ route('blog.index') }}"
                   class="ns-surface ns-surface--hover rounded-lg px-3 py-2.5 text-base-content/70 hover:text-primary transition no-underline text-left">
                    📝 Blog
                </a>
            </div>
        </div>

    </div>
</section>
@endsection
