{{-- =============================================================
     Altyapı Sorgulama — Türkiye il seçicisi
     Başlık: padded; harita: mobilde px-0 (tam sütun), sm+ ile max-w-7xl hizası
     Alpine: addressLookup (resources/js/address-lookup.js)
============================================================= --}}
<section class="mt-10 sm:mt-20 bg-[rgb(248,248,248)] pt-8 pb-0 sm:py-14 overflow-x-hidden" id="altyapi-sorgulama">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 flex-wrap">
                <h2 class="ns-section-title mt-2">Şehrine Özel İnternet Tarifelerini Bul</h2><br>
                <div>
                <div class="ns-section-eyebrow">
                Bulunduğun ili seç, altyapına uygun ucuz ve en hızlı internet paketlerini saniyeler içinde karşılaştır.
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 w-full">
        <div class="mx-auto w-full max-w-7xl px-0 sm:px-6 lg:px-8">
            <div class="overflow-hidden sm:rounded-xl border-0 shadow-none bg-transparent"
                 x-data="addressLookup"
                 x-cloak>

                <template x-if="error">
                    <div class="px-4 py-3 border-b border-base-300 text-sm text-warning-content bg-warning/10">
                        <span x-text="error"></span>
                    </div>
                </template>

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

                <div class="relative w-full min-w-0 bg-[rgb(248,248,248)] sm:rounded-xl overflow-hidden">
                    <div x-ref="map"
                         class="ns-turkey-map block h-[min(78dvh,720px)] min-h-[min(44dvh,420px)] w-full min-w-0 sm:min-h-[520px] sm:h-[min(70vh,720px)] lg:h-[min(78vh,820px)]"
                         role="application"
                         aria-label="Türkiye il haritası — bir ile tıklayın"></div>

                    <template x-if="loading">
                        <div class="absolute inset-0 grid place-items-center bg-[rgb(248,248,248)]/90">
                            <div class="flex items-center gap-2 text-sm text-base-content/70">
                                <span class="loading loading-spinner loading-sm"></span>
                                Harita yükleniyor…
                            </div>
                        </div>
                    </template>

                    <template x-if="hoverProvince">
                        <div class="pointer-events-none absolute left-3 top-3 sm:left-4 sm:top-4 rounded-md bg-base-100/95 border border-base-300 px-3 py-1.5 text-xs shadow-sm max-w-[calc(100%-1.5rem)]">
                            <span class="text-base-content/60">İl:</span>
                            <span class="font-semibold ml-1" x-text="hoverProvince"></span>
                            <span class="ml-2 text-primary">tıkla →</span>
                        </div>
                    </template>
                </div>

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
        </div>
    </div>
</section>
