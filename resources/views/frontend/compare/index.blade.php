@extends('frontend.layouts.app')

@section('title', 'Paket Karşılaştırma - En İyi İnternet Paketleri')

@section('content')
    <section class="py-12 sm:py-16" x-data>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
                <div class="max-w-2xl">
                    <div class="ns-section-eyebrow">Karşılaştırma</div>
                    <h1 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight">Paket karşılaştır</h1>
                    <p class="mt-3 text-sm sm:text-base text-base-content/70 leading-relaxed">
                        En fazla 5 paketi metrik metrik yan yana koy. Mobilde akıcı, masaüstünde tablo odaklı.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('packages.index') }}" class="btn btn-ghost btn-sm">Paket seç</a>
                    <button type="button" class="btn btn-ghost btn-sm"
                            @click="$store.compare.clear(); window.location.href = '{{ route('compare') }}'">
                        Temizle
                    </button>
                </div>
            </div>

            @if($packages->count() > 0)

                {{-- Summary strip --}}
                <div class="mt-8 ns-surface p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-sm text-base-content/70">
                            <span class="font-semibold text-base-content">{{ $packages->count() }}</span> paket seçili.
                            Daha fazla paket eklemek için listeye dönebilirsin.
                        </p>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('packages.index') }}" class="btn btn-primary btn-sm">Paket ekle</a>
                            <a href="{{ route('home') }}" class="btn btn-ghost btn-sm">Anasayfa</a>
                        </div>
                    </div>
                </div>

                {{-- ================================================================
                     Tek responsive karşılaştırma tablosu
                     - Mobil + desktop aynı yapı
                     - Paket sayısı kadar kolon (boş slot yok)
                     - Sol metrik kolonu sticky → yatay kaydırmada görünür kalır
                ================================================================ --}}
                @php
                    $cols       = $packages->count();
                    // Mobile'da kolon min 200px, desktop 220px; metrik kolonu 140/176px
                    $minWidthPx = 140 + ($cols * 210);
                @endphp

                <div class="mt-8">
                    <div class="ns-surface overflow-hidden">
                        <div class="overflow-x-auto">
                            <div style="min-width: {{ $minWidthPx }}px;">
                                <div class="grid gap-0"
                                     style="grid-template-columns: minmax(140px, 176px) repeat({{ $cols }}, minmax(200px, 1fr));">

                                    {{-- Header --}}
                                    <div class="sticky left-0 z-20 bg-base-200 border-b border-base-300 p-4 sm:p-5">
                                        <div class="ns-meta-label">Metrik</div>
                                        <div class="mt-1 text-sm sm:text-base font-semibold">Kıyas tablosu</div>
                                    </div>

                                    @foreach($packages as $package)
                                        <div class="bg-base-100 border-b border-l border-base-300 p-4 sm:p-5 relative">
                                            <button type="button"
                                                    class="absolute top-2 right-2 w-6 h-6 rounded-full flex items-center justify-center
                                                           text-base-content/40 hover:text-error hover:bg-error/10 transition"
                                                    @click="$store.compare.remove({{ $package->id }})"
                                                    aria-label="Paketi kaldır">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.28 3.22a.75.75 0 00-1.06 1.06L8.94 10l-5.72 5.72a.75.75 0 101.06 1.06L10 11.06l5.72 5.72a.75.75 0 101.06-1.06L11.06 10l5.72-5.72a.75.75 0 00-1.06-1.06L10 8.94 4.28 3.22z" clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <div class="flex items-center gap-2 pr-6">
                                                <x-brand-mark :operator="$package->operator" size="sm" />
                                                <span class="text-xs font-semibold truncate">{{ $package->operator->name }}</span>
                                            </div>
                                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                                <span class="ns-pkg-infra">{{ $package->infrastructure_type }}</span>
                                            </div>
                                            <div class="mt-2.5 text-sm font-semibold leading-snug line-clamp-2 min-h-[2.6em]">
                                                {{ $package->name }}
                                            </div>
                                            <div class="mt-3 text-xl sm:text-2xl font-bold text-primary tracking-tight">
                                                {{ number_format($package->price, 2, ',', '.') }}
                                                <span class="text-xs font-semibold text-base-content/60">TL/Ay</span>
                                            </div>
                                            <a href="{{ route('packages.show', $package->slug) }}"
                                               class="btn btn-primary btn-sm mt-3 w-full">Detay</a>
                                        </div>
                                    @endforeach

                                    {{-- Data rows --}}
                                    @foreach([
                                        ['İndirme Hızı', fn($p) => $p->speed . ' Mbps'],
                                        ['Taahhüt',      fn($p) => $p->commitment_period > 0 ? $p->commitment_period . ' Ay' : 'Taahhütsüz'],
                                        ['Altyapı',      fn($p) => $p->infrastructure_type],
                                        ['Kota',         fn($p) => $p->quota],
                                    ] as $row)
                                        <div class="sticky left-0 z-10 bg-base-100 border-b border-base-300 p-4 sm:p-5 text-xs sm:text-sm font-semibold text-base-content/75">
                                            {{ $row[0] }}
                                        </div>
                                        @foreach($packages as $package)
                                            <div class="bg-base-100 border-b border-l border-base-300 p-4 sm:p-5 text-center text-sm">
                                                {{ $row[1]($package) }}
                                            </div>
                                        @endforeach
                                    @endforeach

                                    {{-- Highlights --}}
                                    <div class="sticky left-0 z-10 bg-base-100 p-4 sm:p-5 text-xs sm:text-sm font-semibold text-base-content/75">
                                        Öne çıkanlar
                                    </div>
                                    @foreach($packages as $package)
                                        <div class="bg-base-100 border-l border-base-300 p-4 sm:p-5">
                                            <ul class="space-y-2 text-xs sm:text-sm text-base-content/75">
                                                @php($count = 0)
                                                @foreach(explode("\n", (string) $package->advantages) as $adv)
                                                    @if(trim($adv) && $count < 3)
                                                        @php($count++)
                                                        <li class="flex gap-2">
                                                            <span class="text-primary">•</span>
                                                            <span>{{ $adv }}</span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                                @if($count === 0)
                                                    <li class="text-base-content/50">Belirtilmedi</li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Mobile scroll hint --}}
                        @if($cols > 1)
                            <div class="lg:hidden px-4 py-2 text-[11px] text-base-content/50 border-t border-base-300 bg-base-200/40">
                                → Yan kaydırarak tüm paketleri karşılaştırabilirsin
                            </div>
                        @endif
                    </div>
                </div>

            @else
                <div class="mt-12 rounded-xl border border-dashed border-base-300 bg-base-100 p-12 text-center">
                    <h2 class="text-xl font-bold">Henüz seçim yok</h2>
                    <p class="mt-2 text-sm text-base-content/70">
                        En az 2 paket seçerek karşılaştırmaya başlayabilirsin.
                    </p>
                    <a href="{{ route('packages.index') }}" class="btn btn-primary btn-md mt-5">Paketleri listele</a>
                </div>
            @endif
        </div>
    </section>

    {{-- ===== FAQ Section ===== --}}
    @if($faqs->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
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
