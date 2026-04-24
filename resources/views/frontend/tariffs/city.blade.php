@extends('frontend.layouts.app')

@php
    use App\Models\TariffSeoContent;

    $h1      = $seo?->resolvedH1()  ?? ($cityName . ' Ev İnternet Kampanyaları ve Fiyat Karşılaştırma');
    $intro   = $seo?->resolvedIntro() ?? ($cityName . '\'daki ev interneti kullanıcıları için hazırladığımız güncel tarife listesidir.');
    $metaTitle = $seo?->meta_title ?? ($h1 . ' — Neustar');
    $metaDesc  = $seo?->meta_description ?? $intro;
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDesc)

@section('content')

{{-- ===== Hero / Header ===== --}}
<section class="border-b border-base-300 bg-base-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-xs text-base-content/55" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-base-content transition">Ana sayfa</a>
            <span>/</span>
            <a href="{{ route('packages.index') }}" class="hover:text-base-content transition">İnternet Tarifeleri</a>
            <span>/</span>
            <span class="text-base-content">{{ $cityName }}</span>
        </nav>

        <div class="mt-4 max-w-3xl">
            <div class="ns-section-eyebrow">{{ $cityName }} Tarifeleri</div>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight">{{ $h1 }}</h1>
            <p class="mt-3 text-sm sm:text-base text-base-content/65 leading-relaxed">
                {{ $intro }}
            </p>
        </div>

        {{-- İlçe hızlı erişim --}}
        @if(count($districts) > 0)
        <div class="mt-8">
            <p class="text-xs font-semibold uppercase tracking-wider text-base-content/50 mb-3">
                {{ $cityName }} İlçeleri
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach($districts as $dSlug => $dName)
                    @php
                        $dUrl = route('tariffs.district', [
                            'citySlug' => $citySlug,
                            'urlSlug'  => TariffSeoContent::districtUrlSlug($dSlug),
                        ]);
                    @endphp
                    <a href="{{ $dUrl }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium
                              bg-base-200 hover:bg-primary hover:text-primary-content transition border border-base-300">
                        {{ $dName }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

{{-- ===== Tarife Kartları ===== --}}
<section class="py-10 sm:py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Sonuç sayısı --}}
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-base-content/60">
                <span class="font-semibold text-base-content">{{ $packages->count() }}</span>
                aktif tarife listeleniyor
            </p>
        </div>

        @if($packages->isEmpty())
            <div class="rounded-xl border border-dashed border-base-300 bg-base-100 p-16 text-center">
                <p class="text-sm text-base-content/60">{{ $cityName }} için henüz tarife eklenmemiş.</p>
                <a href="{{ route('packages.index') }}" class="btn btn-sm btn-outline mt-4">
                    Tüm tarifeleri gör
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($packages as $package)
                    @include('frontend.tariffs._package-card', ['package' => $package])
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ===== SEO Alt Metin ===== --}}
@if($seo?->seo_footer_text)
<section class="border-t border-base-300 bg-base-100">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="prose prose-sm max-w-none text-base-content/70 leading-relaxed">
            {!! nl2br(e($seo->seo_footer_text)) !!}
        </div>
    </div>
</section>
@endif

@endsection
