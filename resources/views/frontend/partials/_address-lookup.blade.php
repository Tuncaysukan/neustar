{{-- =============================================================
     Altyapı Sorgulama — Türkiye il seçicisi
     Davranış: Haritadan / arama kutusundan bir il seçildiğinde
     kullanıcı ilgili il sayfasına yönlendirilir.
     Alpine: addressLookup (resources/js/address-lookup.js)
============================================================= --}}
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-20" id="altyapi-sorgulama">
    <div class="flex items-end justify-between gap-4 flex-wrap">
        <div>
            <div class="ns-section-eyebrow">Altyapı Sorgulama</div>
            <h2 class="ns-section-title mt-2">Haritadan ilini seç</h2>
            {{-- Açıklama metni gizlendi --}}
        </div>
        {{-- "81 il tıklanabilir" rozeti gizlendi --}}
    </div>

    <div class="mt-8 ns-surface overflow-hidden" x-data="addressLookup" x-cloak>

        {{-- Hata şeridi --}}
        <template x-if="error">
            <div class="px-4 py-3 border-b border-base-300 text-sm text-warning-content bg-warning/10">
                <span x-text="error"></span>
            </div>
        </template>

        {{-- Arama kutusu — gizlendi --}}
        <div class="hidden">
            <form @submit.prevent="submitSearch" class="flex items-center gap-3 flex-wrap">
                <label class="ns-meta-label shrink-0">İl ara</label>
                <div class="relative flex-1 min-w-[200px]">
                    <input type="text"
                           list="ns-province-list"
                           class="input input-bordered w-full rounded-md pr-10"
                           placeholder="Örn. İstanbul, Ankara, İzmir…"
                           x-model="query"
                           @change="goToProvince(query)"
                           :disabled="loading">
                    <datalist id="ns-province-list">
                        <template x-for="p in provinces" :key="p.id">
                            <option :value="p.name"></option>
                        </template>
                    </datalist>
                    <span class="absolute inset-y-0 right-3 grid place-items-center text-base-content/40 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                    </span>
                </div>
                <button type="submit"
                        class="btn btn-primary rounded-md"
                        :disabled="loading || filteredProvinces.length === 0">
                    Aç →
                </button>
            </form>
        </div>

        {{-- Harita — arka plan beyaz --}}
        <div class="relative" style="background-color: #ffffff;">
            <div x-ref="map"
                 class="h-[420px] sm:h-[540px] lg:h-[620px] w-full"
                 role="application"
                 aria-label="Türkiye il haritası — bir ile tıklayın"></div>

            {{-- Yüklenme overlay'i --}}
            <template x-if="loading">
                <div class="absolute inset-0 grid place-items-center" style="background-color: rgba(255,255,255,0.8);">
                    <div class="flex items-center gap-2 text-sm text-base-content/70">
                        <span class="loading loading-spinner loading-sm"></span>
                        Harita yükleniyor…
                    </div>
                </div>
            </template>

            {{-- Hover rozeti --}}
            <template x-if="hoverProvince">
                <div class="pointer-events-none absolute left-4 top-4 rounded-md bg-base-100/95 border border-base-300 px-3 py-1.5 text-xs shadow-sm">
                    <span class="text-base-content/60">İl:</span>
                    <span class="font-semibold ml-1" x-text="hoverProvince"></span>
                    <span class="ml-2 text-primary">tıkla →</span>
                </div>
            </template>
        </div>

        {{-- Alt kaynaklar şeridi — gizlendi --}}
        <div class="hidden">
            <span>Harita:
                <a href="https://github.com/alpers/Turkey-Maps-GeoJSON" target="_blank" rel="noopener"
                   class="underline underline-offset-2 hover:text-primary">Turkey-Maps-GeoJSON</a>
            </span>
            <span>Adres verisi:
                <a href="https://turkiyeapi.dev" target="_blank" rel="noopener"
                   class="underline underline-offset-2 hover:text-primary">turkiyeapi.dev</a>
            </span>
        </div>
    </div>
</section>
