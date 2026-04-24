@extends('frontend.layouts.app')

@section('title', $cityName . ' İnternet Altyapı Sorgulama ve Başvuru - Neustar')
@section('meta_description', $cityName . ' ilindeki ilçe bazlı internet altyapı bilgilerine ulaş: fiber, VDSL, ADSL seçenekleri ve güncel kampanyalar.')

@section('content')
    {{-- Header --}}
    <section class="border-b border-base-300 bg-base-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <nav class="ns-section-eyebrow flex items-center gap-1.5" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-base-content transition">Ana sayfa</a>
                <span class="opacity-50">/</span>
                <a href="{{ url('/') }}#altyapi-sorgulama" class="hover:text-base-content transition">İller</a>
                <span class="opacity-50">/</span>
                <span class="text-base-content">{{ $cityName }}</span>
            </nav>

            <div class="mt-4 flex items-end justify-between gap-4 flex-wrap">
                <div class="max-w-3xl">
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">
                        {{ $cityName }} internet tarifeleri
                    </h1>
                    <p class="mt-3 text-sm sm:text-base text-base-content/70 leading-relaxed">
                        Haritadan veya listeden ilçeni seç;
                        {{ $cityName }} ilindeki güncel internet tarifelerini,
                        fiyatları ve operatör kampanyalarını hemen gör.
                    </p>
                    <div class="mt-5">
                        <a href="{{ $tariffCityUrl }}"
                           class="btn btn-primary btn-sm">
                            {{ $cityName }} internet tarifelerini gör →
                        </a>
                    </div>
                </div>

                @if(!empty($districts))
                    <div class="text-sm text-base-content/60">
                        {{ count($districts) }} ilçe
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Harita --}}
    @if($districtsGeoJsonUrl)
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-8">
            <div class="ns-surface overflow-hidden"
                 x-data='cityMap(@json([
                     "provinceSlug" => $citySlug,
                     "districtsGeoJsonUrl" => $districtsGeoJsonUrl,
                 ]))'
                 x-cloak>

                {{-- hata şeridi --}}
                <template x-if="error">
                    <div class="px-4 py-3 border-b border-base-300 text-sm text-warning-content bg-warning/10">
                        <span x-text="error"></span>
                    </div>
                </template>

                <div class="relative" style="background-color: #ffffff;">
                    <div x-ref="map"
                         class="w-full h-[280px] sm:h-[340px] lg:h-[400px] max-h-[55vh]"
                         role="application"
                         aria-label="{{ $cityName }} ilçe haritası — bir ilçeye tıklayın"></div>

                    {{-- Yüklenme overlay'i --}}
                    <template x-if="loading">
                        <div class="absolute inset-0 grid place-items-center pointer-events-none" style="background-color: rgba(255,255,255,0.8);">
                            <div class="flex items-center gap-2 text-sm text-base-content/70">
                                <span class="loading loading-spinner loading-sm"></span>
                                Harita yükleniyor…
                            </div>
                        </div>
                    </template>

                    {{-- Hover rozeti --}}
                    <template x-if="hoverDistrict">
                        <div class="pointer-events-none absolute left-4 top-4 rounded-md bg-base-100/95 border border-base-300 px-3 py-1.5 text-xs shadow-sm">
                            <span class="text-base-content/60">İlçe:</span>
                            <span class="font-semibold ml-1" x-text="hoverDistrict.name"></span>
                            <span class="ml-2 text-primary">tıkla →</span>
                        </div>
                    </template>
                </div>
            </div>
        </section>
    @endif

    {{-- İlçe grid --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
        @if(empty($districts))
            <div class="ns-surface p-10 text-center">
                <p class="text-sm text-base-content/60">
                    Bu il için ilçe listesi bulunamadı.
                </p>
            </div>
        @else
            <div class="ns-surface px-4 py-3" x-data="{ q: '' }">
                <div class="flex items-center gap-3 flex-wrap">
                    <label class="ns-meta-label shrink-0">İlçe ara</label>
                    <div class="relative flex-1 min-w-[200px]">
                        <input type="text"
                               class="input input-bordered w-full rounded-md pr-10"
                               placeholder="Örn. Kadıköy, Çankaya, Konak…"
                               x-model="q">
                        <span class="absolute inset-y-0 right-3 grid place-items-center text-base-content/40 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                        </span>
                    </div>
                    <span class="text-xs text-base-content/50" x-show="q.trim()" x-cloak>
                        filtrelendi
                    </span>
                </div>

                <div class="mt-5 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2.5">
                    @foreach($districts as $district)
                        @php
                            $tariffUrl = route('tariffs.district', [
                                'citySlug' => $citySlug,
                                'urlSlug'  => 'ucuz-' . $district['slug'] . '-ev-interneti-fiyatlari',
                            ]);
                        @endphp
                        <a href="{{ $tariffUrl }}"
                           class="ns-surface ns-surface--hover no-underline px-3 py-3 flex flex-col gap-1"
                           x-show="q.trim() === '' || '{{ mb_strtolower($district['name'], 'UTF-8') }}'.startsWith(q.trim().toLowerCase())"
                           x-transition.opacity>
                            <span class="text-sm font-medium text-base-content hover:text-primary transition flex items-center justify-between gap-2">
                                <span class="truncate">{{ $district['name'] }}</span>
                                <span aria-hidden="true" class="text-base-content/30">→</span>
                            </span>
                            <span class="text-[11px] text-primary/70">Tarifeleri gör</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- SEO content --}}
        <div class="ns-prose mt-12 max-w-3xl">
            <h2>{{ $cityName }}'de internet hizmeti</h2>
            <p>
                {{ $cityName }} ilinde fiber, VDSL ve ADSL altyapıları aktif olarak
                hizmet veriyor. Türk Telekom, TurkNet, Vodafone ve Superonline gibi
                sağlayıcıların ilçe bazlı kampanyalarını karşılaştırarak bütçenize
                en uygun tarifeyi seçebilirsiniz.
            </p>
            <p>
                Haritadan veya listeden ilçenizi seçin; o ilçeye ait güncel internet
                tarifelerini, fiyatları ve operatör kampanyalarını hemen görün.
            </p>
        </div>
    </section>
@endsection
