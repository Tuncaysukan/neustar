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
                    <button type="button" class="btn btn-ghost btn-sm" @click="$store.compare.clear()">Temizle</button>
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

                {{-- Mobile: stacked cards --}}
                <div class="mt-8 grid grid-cols-1 gap-4 lg:hidden">
                    @foreach($packages as $package)
                        <div class="ns-surface p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0 flex items-start gap-3">
                                    <x-brand-mark :operator="$package->operator" size="md" />
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold truncate">{{ $package->operator->name }}</div>
                                        <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                            <span class="ns-pkg-infra">{{ $package->infrastructure_type }}</span>
                                        </div>
                                        <h3 class="mt-2.5 text-base font-semibold leading-snug line-clamp-2">
                                            {{ $package->name }}
                                        </h3>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-ghost btn-xs shrink-0"
                                        @click="$store.compare.remove({{ $package->id }})">
                                    Çıkar
                                </button>
                            </div>

                            <div class="mt-5 grid grid-cols-2 gap-2.5">
                                <div class="ns-data-cell">
                                    <div class="ns-meta-label">Hız</div>
                                    <div class="mt-1 text-sm font-semibold">{{ $package->speed }} Mbps</div>
                                </div>
                                <div class="ns-data-cell">
                                    <div class="ns-meta-label">Kota</div>
                                    <div class="mt-1 text-sm font-semibold">{{ $package->quota }}</div>
                                </div>
                                <div class="ns-data-cell">
                                    <div class="ns-meta-label">Taahhüt</div>
                                    <div class="mt-1 text-sm font-semibold">
                                        {{ $package->commitment_period > 0 ? $package->commitment_period . ' Ay' : 'Yok' }}
                                    </div>
                                </div>
                                <div class="ns-data-cell">
                                    <div class="ns-meta-label">Fiyat</div>
                                    <div class="mt-1 text-sm font-semibold text-primary">
                                        {{ number_format($package->price, 2, ',', '.') }} TL
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-4 border-t border-base-300 flex items-center justify-between">
                                <a href="{{ route('packages.show', $package->slug) }}" class="btn btn-primary btn-sm">Detay</a>
                                <span class="ns-meta-label">Özet</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Desktop: comparison table --}}
                <div class="mt-8 hidden lg:block">
                    <div class="ns-surface overflow-hidden">
                        <div class="overflow-x-auto">
                            <div class="min-w-[1100px]">
                                @php($slots = 5)
                                <div class="grid gap-0" style="grid-template-columns: 16rem repeat({{ $slots }}, 1fr);">

                                    {{-- Header row --}}
                                    <div class="sticky left-0 z-20 bg-base-200 border-b border-base-300 p-5">
                                        <div class="ns-meta-label">Metrik</div>
                                        <div class="mt-1 text-base font-semibold">Kıyas tablosu</div>
                                    </div>

                                    @foreach($packages as $package)
                                        <div class="bg-base-100 border-b border-l border-base-300 p-5">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2.5">
                                                        <x-brand-mark :operator="$package->operator" size="sm" />
                                                        <span class="text-sm font-semibold truncate">{{ $package->operator->name }}</span>
                                                    </div>
                                                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                                        <span class="ns-pkg-infra">{{ $package->infrastructure_type }}</span>
                                                    </div>
                                                    <div class="mt-3 text-sm font-semibold leading-snug line-clamp-2">
                                                        {{ $package->name }}
                                                    </div>
                                                    <div class="mt-3 text-2xl font-bold text-primary tracking-tight">
                                                        {{ number_format($package->price, 2, ',', '.') }}
                                                        <span class="text-sm font-semibold text-base-content/60">TL/Ay</span>
                                                    </div>
                                                    <a href="{{ route('packages.show', $package->slug) }}" class="btn btn-primary btn-sm mt-3 w-full">Detay</a>
                                                </div>
                                                <button type="button" class="btn btn-ghost btn-xs"
                                                        @click="$store.compare.remove({{ $package->id }})"
                                                        aria-label="Kaldır">✕</button>
                                            </div>
                                        </div>
                                    @endforeach

                                    @for($i = $packages->count(); $i < $slots; $i++)
                                        <div class="bg-base-100 border-b border-l border-base-300 p-5">
                                            <div class="h-full rounded-md border border-dashed border-base-300 bg-base-200/60 p-6 text-center flex flex-col justify-center">
                                                <div class="ns-meta-label">Paket ekle</div>
                                                <a href="{{ route('packages.index') }}" class="btn btn-ghost btn-xs mt-3 w-full">Listeye git</a>
                                            </div>
                                        </div>
                                    @endfor

                                    @foreach([
                                        ['İndirme Hızı', fn($p) => $p->speed . ' Mbps'],
                                        ['Taahhüt',      fn($p) => $p->commitment_period > 0 ? $p->commitment_period . ' Ay' : 'Taahhütsüz'],
                                        ['Altyapı',      fn($p) => $p->infrastructure_type],
                                        ['Kota',         fn($p) => $p->quota],
                                    ] as $row)
                                        <div class="sticky left-0 z-10 bg-base-100 border-b border-base-300 p-5 text-sm font-semibold">
                                            {{ $row[0] }}
                                        </div>
                                        @foreach($packages as $package)
                                            <div class="bg-base-100 border-b border-l border-base-300 p-5 text-center text-sm">
                                                {{ $row[1]($package) }}
                                            </div>
                                        @endforeach
                                        @for($i = $packages->count(); $i < $slots; $i++)
                                            <div class="bg-base-200/40 border-b border-l border-base-300"></div>
                                        @endfor
                                    @endforeach

                                    {{-- Highlights row --}}
                                    <div class="sticky left-0 z-10 bg-base-100 p-5 text-sm font-semibold">Öne çıkanlar</div>
                                    @foreach($packages as $package)
                                        <div class="bg-base-100 border-l border-base-300 p-5">
                                            <ul class="space-y-2 text-sm text-base-content/75">
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
                                    @for($i = $packages->count(); $i < $slots; $i++)
                                        <div class="bg-base-200/40 border-l border-base-300"></div>
                                    @endfor
                                </div>
                            </div>
                        </div>
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
@endsection
