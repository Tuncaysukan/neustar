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

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

            {{-- Sol: Başlık + ilçe listesi --}}
            <div>
                <div class="ns-section-eyebrow">{{ $cityName }} Tarifeleri</div>
                <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight">{{ $h1 }}</h1>
                <p class="mt-3 text-sm sm:text-base text-base-content/65 leading-relaxed">
                    {{ $intro }}
                </p>

                {{-- İlçe hızlı erişim --}}
                @if(count($districts) > 0)
                <div class="mt-6">
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

            {{-- Sağ: İlçe haritası --}}
            @if($districtsGeoJsonUrl)
            <div class="ns-surface overflow-hidden rounded-xl"
                 x-data='cityMap(@json([
                     "provinceSlug" => $citySlug,
                     "districtsGeoJsonUrl" => $districtsGeoJsonUrl,
                 ]))'
                 x-cloak>

                <template x-if="error">
                    <div class="px-4 py-3 text-sm text-warning-content bg-warning/10">
                        <span x-text="error"></span>
                    </div>
                </template>

                <div class="relative rounded-xl overflow-hidden" style="background-color: #ffffff;">
                    <div x-ref="map"
                         class="w-full h-[260px] sm:h-[320px]"
                         role="application"
                         aria-label="{{ $cityName }} ilçe haritası — bir ilçeye tıklayın"></div>

                    <template x-if="loading">
                        <div class="absolute inset-0 grid place-items-center pointer-events-none"
                             style="background-color: rgba(255,255,255,0.8);">
                            <div class="flex items-center gap-2 text-sm text-base-content/70">
                                <span class="loading loading-spinner loading-sm"></span>
                                Harita yükleniyor…
                            </div>
                        </div>
                    </template>

                    <template x-if="hoverDistrict">
                        <div class="pointer-events-none absolute left-3 top-3 rounded-md bg-base-100/95 border border-base-300 px-3 py-1.5 text-xs shadow-sm">
                            <span class="text-base-content/60">İlçe:</span>
                            <span class="font-semibold ml-1" x-text="hoverDistrict.name"></span>
                            <span class="ml-2 text-primary">→</span>
                        </div>
                    </template>
                </div>
            </div>
            @endif

        </div>
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

{{-- ===== FAQ Section ===== --}}
@if(!empty($seo?->faqs))
<section class="border-t border-base-300">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        <h2 class="text-xl font-bold mb-8">{{ $cityName }} İnternet Hakkında Sıkça Sorulanlar</h2>
        
        <div class="space-y-4">
            @foreach($seo->faqs as $faq)
                @if(!empty($faq['question']) && !empty($faq['answer']))
                    <details class="collapse collapse-plus ns-surface border border-base-300 rounded-xl">
                        <summary class="collapse-title text-sm font-semibold">
                            {{ $faq['question'] }}
                        </summary>
                        <div class="collapse-content text-sm text-base-content/70"> 
                            <p>{{ $faq['answer'] }}</p>
                        </div>
                    </details>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
