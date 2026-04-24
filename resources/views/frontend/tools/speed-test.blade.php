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
            <div class="mt-10 ns-surface p-6 sm:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    {{-- Gauge --}}
                    <div class="lg:col-span-5">
                        <div class="rounded-xl bg-base-200 border border-base-300 p-6 text-center">
                            <div class="ns-meta-label">İndirme hızı</div>

                            <div class="mt-5 mx-auto h-48 w-48 rounded-full border-[6px] grid place-items-center transition-colors"
                                 :class="status === 'running' ? 'border-primary' : (status === 'done' ? 'border-primary/60' : 'border-base-300')">
                                <div>
                                    <div class="text-4xl sm:text-5xl font-bold tabular-nums leading-none"
                                         :class="status === 'idle' ? 'text-base-content/40' : 'text-base-content'">
                                        <span x-text="format(summary.download ?? liveDown)"></span>
                                    </div>
                                    <div class="ns-meta-label mt-2">Mbps</div>
                                </div>
                            </div>

                            <button type="button"
                                    class="btn btn-primary btn-lg w-full mt-6"
                                    x-show="status !== 'running'"
                                    @click="status === 'done' ? restart() : start()">
                                <span x-text="status === 'done' ? 'Yeniden test et' : (status === 'error' ? 'Tekrar dene' : 'Testi başlat')"></span>
                            </button>

                            <button type="button"
                                    class="btn btn-lg w-full mt-6 pointer-events-none"
                                    x-show="status === 'running'" disabled>
                                <span class="loading loading-spinner loading-xs"></span>
                                <span>Ölçülüyor…</span>
                            </button>

                            <div class="mt-3 text-xs text-base-content/60" x-text="phase"></div>
                        </div>
                    </div>

                    {{-- Metrics --}}
                    <div class="lg:col-span-7">

                        {{-- Primary metrics --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <div class="ns-data-cell">
                                <div class="ns-meta-label">Ping</div>
                                <div class="mt-1 text-lg font-semibold tabular-nums leading-tight">
                                    <span x-text="formatMs(summary.latency)"></span><span class="text-xs font-normal text-base-content/55"> ms</span>
                                </div>
                            </div>
                            <div class="ns-data-cell">
                                <div class="ns-meta-label">İndirme</div>
                                <div class="mt-1 text-lg font-semibold tabular-nums leading-tight">
                                    <span x-text="format(summary.download)"></span><span class="text-xs font-normal text-base-content/55"> Mbps</span>
                                </div>
                            </div>
                            <div class="ns-data-cell">
                                <div class="ns-meta-label">Yükleme</div>
                                <div class="mt-1 text-lg font-semibold tabular-nums leading-tight">
                                    <span x-text="format(summary.upload)"></span><span class="text-xs font-normal text-base-content/55"> Mbps</span>
                                </div>
                            </div>
                            <div class="ns-data-cell">
                                <div class="ns-meta-label">Jitter</div>
                                <div class="mt-1 text-lg font-semibold tabular-nums leading-tight">
                                    <span x-text="formatMs(summary.jitter)"></span><span class="text-xs font-normal text-base-content/55"> ms</span>
                                </div>
                            </div>
                        </div>

                        {{-- Secondary metrics (shown after finish) --}}
                        <div class="mt-2.5 grid grid-cols-1 sm:grid-cols-3 gap-2.5"
                             x-show="status === 'done'" x-cloak x-transition.opacity>
                            <div class="ns-data-cell">
                                <div class="ns-meta-label">Yüklü ping (ind.)</div>
                                <div class="mt-1 text-sm font-semibold tabular-nums">
                                    <span x-text="formatMs(summary.downLoadedLatency)"></span>
                                    <span class="text-xs font-normal text-base-content/55"> ms</span>
                                </div>
                            </div>
                            <div class="ns-data-cell">
                                <div class="ns-meta-label">Yüklü ping (yük.)</div>
                                <div class="mt-1 text-sm font-semibold tabular-nums">
                                    <span x-text="formatMs(summary.upLoadedLatency)"></span>
                                    <span class="text-xs font-normal text-base-content/55"> ms</span>
                                </div>
                            </div>
                            <div class="ns-data-cell" x-show="packetLossSupported" x-cloak>
                                <div class="ns-meta-label">Paket kaybı</div>
                                <div class="mt-1 text-sm font-semibold tabular-nums">
                                    <span x-text="formatPct(summary.packetLoss)"></span>
                                    <span class="text-xs font-normal text-base-content/55"> %</span>
                                </div>
                            </div>
                        </div>

                        {{-- Error state --}}
                        <div class="mt-4 rounded-md border border-error/40 bg-error/5 p-4"
                             x-show="status === 'error'" x-cloak>
                            <p class="text-sm font-semibold text-error">Ölçüm tamamlanamadı</p>
                            <p class="mt-1 text-xs text-base-content/70" x-text="error"></p>
                        </div>

                        {{-- Footer --}}
                        <div class="mt-5 pt-4 border-t border-base-300 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <p class="text-xs text-base-content/55">
                                Cloudflare ağı üzerinden ölçülüyor · tarayıcı tabanlı, gerçek ağ trafiği.
                                <span x-show="finishedAt" x-cloak>
                                    · <span x-text="finishedAt?.toLocaleTimeString('tr-TR')"></span>
                                </span>
                            </p>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('packages.index') }}" class="btn btn-ghost btn-sm">Paketleri incele</a>
                                <a href="{{ route('compare') }}" class="btn btn-ghost btn-sm">Karşılaştır</a>
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
@endsection
