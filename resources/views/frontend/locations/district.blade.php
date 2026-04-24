@extends('frontend.layouts.app')

@section('title', $cityName . ' ' . $districtName . ' İnternet Altyapı Sorgulama - Neustar')
@section('meta_description', $cityName . ' ' . $districtName . ' için fiber, VDSL ve ADSL altyapı sorgulaması, port durumu ve güncel internet paketleri.')

@section('content')
    {{-- Header --}}
    <section class="border-b border-base-300 bg-base-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <nav class="ns-section-eyebrow flex items-center gap-1.5" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-base-content transition">Ana sayfa</a>
                <span class="opacity-50">/</span>
                <a href="{{ url('/internet-altyapi/' . $citySlug) }}"
                   class="hover:text-base-content transition">{{ $cityName }}</a>
                <span class="opacity-50">/</span>
                <span class="text-base-content">{{ $districtName }}</span>
            </nav>

            <h1 class="mt-4 text-3xl sm:text-4xl font-bold tracking-tight">
                {{ $cityName }} {{ $districtName }} altyapı sorgulama
            </h1>
            <p class="mt-3 text-sm sm:text-base text-base-content/70 leading-relaxed max-w-2xl">
                Mahalle, sokak, bina ve daireni seç; altyapı durumunu ve uygun fiber / VDSL / ADSL paketleri anında göstereyim.
            </p>
        </div>
    </section>

    @php
        $lookupConfig = [
            'provinceName' => $cityName,
            'districtName' => $districtName,
            'provinceSlug' => $citySlug,
            'districtSlug' => $districtSlug,
        ];
    @endphp

    <section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12"
             x-data="districtLookup({{ \Illuminate\Support\Js::from($lookupConfig) }})"
             x-cloak
             id="ns-wizard">

        {{-- Breadcrumb chip'leri --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <span class="ns-step-chip ns-step-chip--static">{{ Str::upper($cityName) }}</span>
            <span class="ns-step-chip ns-step-chip--static">{{ Str::upper($districtName) }}</span>

            <template x-if="selection.neighborhood">
                <button type="button" class="ns-step-chip ns-step-chip--active" @click="goToStep('neighborhood')">
                    <span class="ns-step-chip__label">Mahalle</span>
                    <span class="ns-step-chip__value" x-text="selection.neighborhood.name.toLocaleUpperCase('tr')"></span>
                </button>
            </template>
            <template x-if="selection.street">
                <button type="button" class="ns-step-chip ns-step-chip--active" @click="goToStep('street')">
                    <span class="ns-step-chip__label">Sokak</span>
                    <span class="ns-step-chip__value" x-text="selection.street.name.toLocaleUpperCase('tr')"></span>
                </button>
            </template>
            <template x-if="selection.building">
                <button type="button" class="ns-step-chip ns-step-chip--active" @click="goToStep('building')">
                    <span class="ns-step-chip__label">Bina</span>
                    <span class="ns-step-chip__value" x-text="selection.building.name"></span>
                </button>
            </template>
            <template x-if="selection.door">
                <button type="button" class="ns-step-chip ns-step-chip--active" @click="goToStep('door')">
                    <span class="ns-step-chip__label">Daire</span>
                    <span class="ns-step-chip__value" x-text="selection.door.name"></span>
                </button>
            </template>

            <button type="button"
                    class="ml-auto text-xs text-base-content/50 hover:text-base-content transition"
                    x-show="selection.neighborhood || result"
                    @click="resetAll()">
                Sıfırla
            </button>
        </div>

        {{-- Progress --}}
        <div class="mb-6 h-1 rounded-full bg-base-200 overflow-hidden">
            <div class="h-full bg-primary transition-all duration-300"
                 :style="`width: ${
                    step === 'neighborhood' ? 20 :
                    step === 'street'       ? 40 :
                    step === 'building'     ? 60 :
                    step === 'door'         ? 80 : 100
                 }%`"></div>
        </div>

        {{-- ================================================================
             STEP 1 — Mahalle
        ================================================================ --}}
        <template x-if="step === 'neighborhood'">
            <div>
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h2 class="text-lg font-semibold">Mahalle seç</h2>
                        <p class="text-sm text-base-content/60">{{ $districtName }} mahallelerinden birini seç.</p>
                    </div>
                    <div class="relative w-full sm:w-64">
                        @include('frontend.partials._wizard-search-icon')
                        <input type="search" class="input input-bordered w-full pl-9"
                               placeholder="Mahalle Ara..." x-model="nSearch">
                    </div>
                </div>

                <template x-if="nLoading">
                    @include('frontend.partials._wizard-loading', ['label' => 'Mahalleler yükleniyor…'])
                </template>

                <template x-if="!nLoading && filteredNeighborhoods.length > 0">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        <template x-for="item in filteredNeighborhoods" :key="item.id">
                            <button type="button" class="ns-pick-card" @click="selectNeighborhood(item)">
                                <span class="ns-pick-card__label" x-text="item.name.toLocaleUpperCase('tr')"></span>
                                <span class="ns-pick-card__chev" aria-hidden="true">›</span>
                            </button>
                        </template>
                    </div>
                </template>

                <template x-if="!nLoading && filteredNeighborhoods.length === 0">
                    @include('frontend.partials._wizard-empty', ['errorVar' => 'nError'])
                </template>
            </div>
        </template>

        {{-- ================================================================
             STEP 2 — Sokak
        ================================================================ --}}
        <template x-if="step === 'street'">
            <div>
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h2 class="text-lg font-semibold">Sokak / Cadde seç</h2>
                        <p class="text-sm text-base-content/60">
                            <span x-text="selection.neighborhood?.name"></span> mahallesindeki yollar.
                        </p>
                    </div>
                    <div class="relative w-full sm:w-64">
                        @include('frontend.partials._wizard-search-icon')
                        <input type="search" class="input input-bordered w-full pl-9"
                               placeholder="Sokak Ara..." x-model="sSearch">
                    </div>
                </div>

                <template x-if="sLoading">
                    @include('frontend.partials._wizard-loading', ['label' => 'Sokaklar yükleniyor…'])
                </template>

                <template x-if="!sLoading && filteredStreets.length > 0">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        <template x-for="item in filteredStreets" :key="item.id">
                            <button type="button" class="ns-pick-card" @click="selectStreet(item)">
                                <span class="ns-pick-card__label" x-text="item.name.toLocaleUpperCase('tr')"></span>
                                <span class="ns-pick-card__chev" aria-hidden="true">›</span>
                            </button>
                        </template>
                    </div>
                </template>

                <template x-if="!sLoading && filteredStreets.length === 0">
                    @include('frontend.partials._wizard-empty', ['errorVar' => 'sError'])
                </template>

                <div class="mt-4 flex items-center gap-3">
                    <button type="button" class="btn btn-ghost btn-sm" @click="goToStep('neighborhood')">
                        ← Mahalleyi değiştir
                    </button>
                </div>
            </div>
        </template>

        {{-- ================================================================
             STEP 3 — Bina
        ================================================================ --}}
        <template x-if="step === 'building'">
            <div>
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h2 class="text-lg font-semibold">Bina numarasını seç</h2>
                        <p class="text-sm text-base-content/60">
                            <span x-text="selection.street?.name"></span> üzerindeki binalar.
                        </p>
                    </div>
                    <div class="relative w-full sm:w-64">
                        @include('frontend.partials._wizard-search-icon')
                        <input type="search" class="input input-bordered w-full pl-9"
                               placeholder="Bina No Ara..." x-model="bSearch">
                    </div>
                </div>

                <template x-if="bLoading">
                    @include('frontend.partials._wizard-loading', ['label' => 'Binalar yükleniyor…'])
                </template>

                <template x-if="!bLoading && filteredBuildings.length > 0">
                    <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-6 gap-3">
                        <template x-for="item in filteredBuildings" :key="item.id">
                            <button type="button" class="ns-pick-card ns-pick-card--compact" @click="selectBuilding(item)">
                                <span class="ns-pick-card__label" x-text="item.name"></span>
                                <span class="ns-pick-card__chev" aria-hidden="true">›</span>
                            </button>
                        </template>
                    </div>
                </template>

                <template x-if="!bLoading && filteredBuildings.length === 0">
                    @include('frontend.partials._wizard-empty', ['errorVar' => 'bError'])
                </template>

                <div class="mt-4 flex items-center gap-3">
                    <button type="button" class="btn btn-ghost btn-sm" @click="goToStep('street')">
                        ← Sokağı değiştir
                    </button>
                </div>
            </div>
        </template>

        {{-- ================================================================
             STEP 4 — Daire
        ================================================================ --}}
        <template x-if="step === 'door'">
            <div>
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h2 class="text-lg font-semibold">Daire seç</h2>
                        <p class="text-sm text-base-content/60">
                            Bina <span class="font-medium" x-text="selection.building?.name"></span>
                            içindeki daireler — port kontrolü için gerekli.
                        </p>
                    </div>
                    <div class="relative w-full sm:w-64">
                        @include('frontend.partials._wizard-search-icon')
                        <input type="search" class="input input-bordered w-full pl-9"
                               placeholder="Daire Ara..." x-model="dSearch">
                    </div>
                </div>

                <template x-if="dLoading">
                    @include('frontend.partials._wizard-loading', ['label' => 'Daireler yükleniyor…'])
                </template>

                <template x-if="!dLoading && filteredDoors.length > 0">
                    <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-8 gap-3">
                        <template x-for="item in filteredDoors" :key="item.id">
                            <button type="button" class="ns-pick-card ns-pick-card--compact" @click="selectDoor(item)">
                                <span class="ns-pick-card__label" x-text="item.name"></span>
                                <span class="ns-pick-card__chev" aria-hidden="true">›</span>
                            </button>
                        </template>
                    </div>
                </template>

                <template x-if="!dLoading && filteredDoors.length === 0">
                    <div class="border border-dashed border-base-300 rounded-lg p-6 text-center">
                        <p class="text-sm text-base-content/60" x-text="dError || 'Bu bina için daire listesi bulunamadı.'"></p>
                    </div>
                </template>

                <div class="mt-5 flex items-center gap-3 flex-wrap">
                    <button type="button" class="btn btn-ghost btn-sm" @click="goToStep('building')">
                        ← Binayı değiştir
                    </button>
                    <button type="button"
                            class="btn btn-primary btn-sm"
                            :disabled="submitting"
                            @click="skipDoor()">
                        <span x-show="!submitting">Daire atla, altyapıyı sorgula</span>
                        <span x-show="submitting" class="flex items-center gap-2" x-cloak>
                            <span class="loading loading-spinner loading-xs"></span>
                            Sorgulanıyor…
                        </span>
                    </button>
                </div>

                <template x-if="submitError">
                    <p class="mt-3 text-xs text-error" x-text="submitError"></p>
                </template>
            </div>
        </template>

        {{-- ================================================================
             STEP 5 — Sonuç + Lead formu
        ================================================================ --}}
        <template x-if="step === 'result' && result">
            <div>
                <div class="flex items-center justify-between gap-3 flex-wrap mb-5">
                    <div>
                        <h2 class="text-lg font-semibold">Altyapı sorgu sonucu</h2>
                        <p class="text-sm text-base-content/60 mt-1">
                            <span x-text="result.scope.city"></span>
                            <span x-show="result.scope.district"> · <span x-text="result.scope.district"></span></span>
                            <span x-show="result.scope.neighborhood"> · <span x-text="result.scope.neighborhood"></span></span>
                        </p>
                    </div>
                    <span class="badge"
                          :class="result.source === 'managed' ? 'badge-success' : 'badge-ghost'"
                          x-text="result.source === 'managed' ? 'Doğrulanmış' : 'Tahmini'"></span>
                </div>

                {{-- Teknoloji kartları --}}
                <div class="grid gap-3 sm:grid-cols-3 mb-5">
                    <template x-for="tech in result.technologies" :key="tech.key">
                        <div class="ns-surface ns-surface--soft p-4">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-semibold" x-text="tech.label"></span>
                                <span class="badge" :class="statusBadge(tech.status)" x-text="statusLabel(tech.status)"></span>
                            </div>
                            <div class="mt-3">
                                <div class="h-1.5 w-full rounded-full bg-base-300 overflow-hidden">
                                    <div class="h-full bg-primary rounded-full transition-[width]"
                                         :style="`width:${tech.coverage}%`"></div>
                                </div>
                                <div class="mt-1.5 flex items-center justify-between text-[11px] text-base-content/55">
                                    <span>Kapsama</span>
                                    <span x-text="tech.coverage + '%'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="grid gap-3 sm:grid-cols-[1fr_auto] items-center mb-6">
                    <div class="ns-surface px-4 py-3 flex items-center gap-6 flex-wrap">
                        <div>
                            <div class="ns-meta-label">Maksimum indirme</div>
                            <div class="text-lg font-semibold">
                                <span x-text="result.max_down_mbps ?? '—'"></span>
                                <span class="text-xs font-normal text-base-content/55 ml-1">Mbps</span>
                            </div>
                        </div>
                        <div>
                            <div class="ns-meta-label">Yükleme</div>
                            <div class="text-lg font-semibold">
                                <span x-text="result.max_up_mbps ?? '—'"></span>
                                <span class="text-xs font-normal text-base-content/55 ml-1">Mbps</span>
                            </div>
                        </div>
                        <template x-if="result.primary">
                            <div>
                                <div class="ns-meta-label">Öncelikli</div>
                                <div class="text-sm font-semibold" x-text="result.primary.label + ' · ' + result.primary.coverage + '%'"></div>
                            </div>
                        </template>
                    </div>
                    <a href="{{ url('/internet-paketleri') }}?city={{ $citySlug }}&district={{ $districtSlug }}"
                       class="btn btn-outline">Uygun paketleri gör →</a>
                </div>

                {{-- ── Tarife Kartları ── --}}
                @if($packages->isNotEmpty())
                <div class="mb-8">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <h3 class="text-base font-semibold">
                            {{ $districtName }} için uygun tarifeler
                        </h3>
                        <a href="{{ $tariffDistrictUrl }}"
                           class="text-xs text-primary hover:underline whitespace-nowrap">
                            Tüm tarifeleri gör →
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($packages->take(6) as $package)
                            @include('frontend.tariffs._package-card', ['package' => $package])
                        @endforeach
                    </div>

                    @if($packages->count() > 6)
                        <div class="mt-4 text-center">
                            <a href="{{ $tariffDistrictUrl }}"
                               class="btn btn-outline btn-sm">
                                {{ $packages->count() - 6 }} tarife daha gör
                            </a>
                        </div>
                    @endif
                </div>
                @endif

                {{-- Lead formu --}}
                <template x-if="!leadDone">
                    <div class="ns-surface ns-surface--soft p-5 sm:p-6">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div>
                                <h3 class="text-base font-semibold">Uzman seni arasın</h3>
                                <p class="mt-1 text-xs text-base-content/60 max-w-md">
                                    Kesin port durumunu ve sana özel kampanyayı telefonda ileteyim —
                                    <span class="font-medium">ücretsiz, taahhütsüz</span>.
                                </p>
                            </div>
                            <template x-if="result.source === 'estimated'">
                                <span class="text-[11px] text-base-content/55 max-w-xs">
                                    Bu sonuç tahmini; arayıp portu kesinleştirelim.
                                </span>
                            </template>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="ns-meta-label block mb-1.5">Ad Soyad</label>
                                <input type="text" class="input input-bordered w-full"
                                       placeholder="Ahmet Yılmaz" autocomplete="name"
                                       x-model="leadForm.fullName">
                            </div>
                            <div>
                                <label class="ns-meta-label block mb-1.5">Telefon</label>
                                <input type="tel" class="input input-bordered w-full"
                                       placeholder="05XX XXX XX XX" inputmode="tel" autocomplete="tel"
                                       x-model="leadForm.phone">
                            </div>
                        </div>

                        <div class="hidden" aria-hidden="true">
                            <label>Website <input type="text" x-model="leadForm.hp" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-4 flex-wrap">
                            <p class="text-[11px] text-base-content/50 leading-relaxed max-w-md">
                                Gönderdiğinde
                                <a href="{{ url('/gizlilik-politikasi') }}" class="underline underline-offset-2">gizlilik politikası</a>
                                kabul edilmiş sayılır.
                            </p>
                            <button type="button" class="btn btn-primary"
                                    :disabled="!canSubmitLead || leadSubmitting"
                                    @click="submitLead()">
                                <span x-show="!leadSubmitting">Uzman beni arasın</span>
                                <span x-show="leadSubmitting" class="flex items-center gap-2" x-cloak>
                                    <span class="loading loading-spinner loading-xs"></span>
                                    Gönderiliyor…
                                </span>
                            </button>
                        </div>

                        <template x-if="leadError">
                            <p class="mt-3 text-xs text-error" x-text="leadError"></p>
                        </template>
                    </div>
                </template>

                <template x-if="leadDone">
                    <div class="ns-surface ns-surface--soft p-6 text-center">
                        <div class="mx-auto w-12 h-12 rounded-full bg-success/15 text-success flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                 class="w-6 h-6">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <h4 class="mt-3 text-base font-semibold">Başvurun alındı</h4>
                        <p class="mt-1 text-sm text-base-content/60 max-w-md mx-auto" x-text="leadDoneMessage"></p>
                        <div class="mt-4 flex items-center justify-center gap-3 flex-wrap">
                            <a href="{{ $tariffDistrictUrl }}"
                               class="btn btn-primary btn-sm">Paketleri gör</a>
                            <button type="button" class="btn btn-ghost btn-sm" @click="resetAll()">Yeni sorgu</button>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <div class="ns-prose mt-16 max-w-3xl">
            <h2>{{ $districtName }} internet altyapısı</h2>
            <p>
                {{ $cityName }} - {{ $districtName }} bölgesinde fiber kapsama sürekli genişliyor.
                Tam fiber (FTTH) desteklenmeyen sokaklarda VDSL veya ADSL ile 16–100 Mbps arasında
                hizmet alabilirsiniz. Adresine uygun altyapıyı ve güncel port durumunu kontrol
                etmek için yukarıdaki adımları doldurabilirsin.
            </p>
        </div>
    </section>
@endsection
