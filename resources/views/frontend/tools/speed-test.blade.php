@extends('frontend.layouts.app')

@section('title', 'İnternet Hız Testi - Neustar')

@section('meta_description', 'Cloudflare ağı üzerinden gerçek zamanlı internet hız testi. İndirme, yükleme, ping, jitter ve paket kaybını anında ölç.')

@section('content')
    <section class="py-12 sm:py-16" x-data="speedTest">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- ===== Header ===== --}}
            <div class="max-w-2xl">
                <div class="ns-section-eyebrow">Araç</div>
                <h1 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight">İnternet hız testi</h1>
                <p class="mt-3 text-sm sm:text-base text-base-content/70 leading-relaxed">
                    Cloudflare'in küresel edge ağı üzerinden gerçek trafikle ölçüm.
                    İndirme, yükleme, ping, jitter ve paket kaybı tek ekranda.
                </p>
            </div>

            {{-- ===== Main panel ===== --}}
            <div class="mt-10 ns-surface p-6 sm:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">

                    {{-- Sol: Yarım daire kadran + buton --}}
                    <div class="flex flex-col items-center">

                        {{-- Yarım daire SVG --}}
                        <div class="relative" style="width:260px;height:165px;overflow:visible;margin-bottom:8px;">
                            <svg viewBox="0 0 260 140" style="width:260px;height:140px;">
                                {{-- Arka plan yarım daire --}}
                                <path d="M 20 130 A 110 110 0 0 1 240 130"
                                      fill="none"
                                      stroke="#e5e7eb"
                                      stroke-width="18"
                                      stroke-linecap="round"/>
                                {{-- İlerleme yarım daire --}}
                                <path d="M 20 130 A 110 110 0 0 1 240 130"
                                      fill="none"
                                      stroke="#f97316"
                                      stroke-width="18"
                                      stroke-linecap="round"
                                      :stroke-dasharray="`${345 * Math.min(1, (summary.download ?? liveDown) / (100 * 1e6))} 345`"
                                      :style="status === 'idle' ? 'stroke:#e5e7eb' : 'stroke:#f97316'"
                                      style="transition: stroke-dasharray 0.5s ease;"/>
                            </svg>
                            {{-- Ortadaki değer --}}
                            <div style="position:absolute;bottom:0;left:0;right:0;text-align:center;padding-bottom:4px;">
                                <div class="text-5xl font-black tabular-nums leading-none"
                                     :class="status === 'idle' ? 'text-base-content/30' : 'text-base-content'"
                                     x-text="format(summary.download ?? liveDown)"></div>
                                <div class="text-sm font-semibold text-base-content/50 mt-1 uppercase tracking-widest">Mbps</div>
                            </div>
                            {{-- Skala etiketleri --}}
                            <div style="position:absolute;bottom:-20px;left:8px;font-size:10px;color:#9ca3af;">0</div>
                            <div style="position:absolute;bottom:-4px;left:50%;transform:translateX(-50%);font-size:10px;color:#9ca3af;">50</div>
                            <div style="position:absolute;bottom:-20px;right:8px;font-size:10px;color:#9ca3af;">100+</div>
                        </div>

                        {{-- Durum / Buton --}}
                        <div class="mt-6 w-full max-w-xs">
                            <button type="button"
                                    class="btn btn-primary btn-lg w-full"
                                    x-show="status !== 'running'"
                                    @click="status === 'done' ? restart() : start()">
                                <template x-if="status === 'done'">
                                    <span class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Yeniden test et
                                    </span>
                                </template>
                                <template x-if="status !== 'done'">
                                    <span x-text="status === 'error' ? 'Tekrar dene' : 'Testi başlat'"></span>
                                </template>
                            </button>

                            <button type="button"
                                    class="btn btn-lg w-full pointer-events-none"
                                    x-show="status === 'running'" disabled>
                                <span class="loading loading-spinner loading-xs"></span>
                                <span>Ölçülüyor…</span>
                            </button>

                            <p class="mt-2 text-xs text-center text-base-content/50" x-text="phase"></p>
                        </div>
                    </div>

                    {{-- Sağ: Metrik listesi --}}
                    <div class="space-y-3">

                        {{-- Download --}}
                        <div class="flex items-center justify-between ns-surface rounded-xl px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background:#3b82f6;"></span>
                                <span class="text-sm font-semibold text-base-content/70">Download</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black tabular-nums"
                                      x-text="format(summary.download ?? liveDown)"></span>
                                <span class="text-sm font-semibold text-base-content/50 ml-1">Mbps</span>
                            </div>
                        </div>

                        {{-- Upload --}}
                        <div class="flex items-center justify-between ns-surface rounded-xl px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background:#f97316;"></span>
                                <span class="text-sm font-semibold text-base-content/70">Upload</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black tabular-nums"
                                      x-text="format(summary.upload ?? liveUp)"></span>
                                <span class="text-sm font-semibold text-base-content/50 ml-1">Mbps</span>
                            </div>
                        </div>

                        {{-- Ping --}}
                        <div class="flex items-center justify-between ns-surface rounded-xl px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background:#6b7280;"></span>
                                <span class="text-sm font-semibold text-base-content/70">Ping</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black tabular-nums"
                                      x-text="formatMs(summary.latency)"></span>
                                <span class="text-sm font-semibold text-base-content/50 ml-1">ms</span>
                            </div>
                        </div>

                        {{-- Jitter --}}
                        <div class="flex items-center justify-between ns-surface rounded-xl px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background:#6b7280;"></span>
                                <span class="text-sm font-semibold text-base-content/70">Jitter</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black tabular-nums"
                                      x-text="formatMs(summary.jitter)"></span>
                                <span class="text-sm font-semibold text-base-content/50 ml-1">ms</span>
                            </div>
                        </div>

                        {{-- Error --}}
                        <div class="rounded-xl border border-error/40 bg-error/5 p-4"
                             x-show="status === 'error'" x-cloak>
                            <p class="text-sm font-semibold text-error">Ölçüm tamamlanamadı</p>
                            <p class="mt-1 text-xs text-base-content/70" x-text="error"></p>
                        </div>

                        {{-- Footer --}}
                        <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <p class="text-xs text-base-content/45">
                                Cloudflare ağı · tarayıcı tabanlı
                                <span x-show="finishedAt" x-cloak>
                                    · <span x-text="finishedAt?.toLocaleTimeString('tr-TR')"></span>
                                </span>
                            </p>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('packages.index') }}" class="btn btn-ghost btn-xs">Paketleri incele</a>
                                <a href="{{ route('compare') }}" class="btn btn-ghost btn-xs">Karşılaştır</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ===== Info strip ===== --}}
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="ns-surface p-5">
                    <div class="ns-meta-label">Ping</div>
                    <p class="mt-2 text-sm text-base-content/70 leading-relaxed">
                        Sunucuya ilk paketin ulaşma süresi. Oyun ve görüntülü görüşmede gecikmeyi belirler.
                    </p>
                </div>
                <div class="ns-surface p-5">
                    <div class="ns-meta-label">Jitter</div>
                    <p class="mt-2 text-sm text-base-content/70 leading-relaxed">
                        Ping değerinin oynama aralığı. Düşük jitter stabil bir bağlantı demektir.
                    </p>
                </div>
                <div class="ns-surface p-5">
                    <div class="ns-meta-label">Paket kaybı</div>
                    <p class="mt-2 text-sm text-base-content/70 leading-relaxed">
                        Hedefe ulaşmayan paket yüzdesi. Yüksekse yayın ve oyunda takılma yaşanır.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== FAQ Section ===== --}}
    @if($faqs->isNotEmpty())
        <section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="ns-surface rounded-xl p-6 sm:p-8">
                <h2 class="text-xl font-bold mb-6">Sıkça sorulan sorular</h2>

                <div class="divide-y divide-base-300 border-y border-base-300">
                    @foreach($faqs as $faq)
                        <details class="group">
                            <summary class="cursor-pointer select-none py-4 flex items-center justify-between gap-4">
                                <span class="text-base font-medium">{{ $faq->question }}</span>
                                <span class="shrink-0 text-base-content/40 group-open:rotate-180 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </summary>
                            <div class="pb-5 text-sm text-base-content/70 leading-relaxed">
                                {{ $faq->answer }}
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
