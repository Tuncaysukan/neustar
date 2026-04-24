@extends('frontend.layouts.app')

@section('title', 'İnternet Paketleri — Neustar')

@php
    $sortOptions = [
        'featured'   => 'Önerilen',
        'price-asc'  => 'Fiyat ↑',
        'price-desc' => 'Fiyat ↓',
        'speed-desc' => 'Hız ↓',
        'speed-asc'  => 'Hız ↑',
    ];

    $speedTiers = [
        '16-35'    => '16–35 Mbps',
        '50-100'   => '50–100 Mbps',
        '200-1000' => '200 Mbps +',
    ];

    $infraOptions = [
        'fiber'          => 'Fiber',
        'vdsl'           => 'VDSL',
        'adsl'           => 'ADSL',
        'fixed_wireless' => 'Sabit Kablosuz',
    ];

    $commitmentOptions = [
        ''  => 'Farketmez',
        '0' => 'Taahhütsüz',
        '1' => 'Taahhütli',
    ];
@endphp

@section('content')
<section class="py-10 sm:py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- ====== Header ====== --}}
        <div class="max-w-2xl">
            <div class="ns-section-eyebrow">Liste</div>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight">İnternet Paketleri</h1>
            <p class="mt-3 text-sm sm:text-base text-base-content/65 leading-relaxed">
                Operatör, altyapı ve fiyata göre filtrele. Aktif {{ $packages->total() }} tarife.
            </p>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- ====== Sidebar (filter form) ====== --}}
            @php
                $activeCount = count($filters['operator'])
                    + count($filters['infrastructure'])
                    + count($filters['speed'])
                    + (in_array($filters['commitment'], ['0','1'], true) ? 1 : 0)
                    + (($filters['price_min'] !== null || $filters['price_max'] !== null) ? 1 : 0)
                    + ($filters['sponsor_only'] ? 1 : 0);
            @endphp
            <aside class="lg:col-span-3"
                   x-data="{
                       isDesktop: window.matchMedia('(min-width: 1024px)').matches,
                       openMobile: false,
                       init() {
                           const mql = window.matchMedia('(min-width: 1024px)');
                           mql.addEventListener('change', e => { this.isDesktop = e.matches; });
                       }
                   }">
                <div class="lg:sticky lg:top-20">

                    {{-- Mobile toggle button --}}
                    <button type="button"
                            class="lg:hidden w-full ns-surface rounded-xl px-4 py-3 mb-3 flex items-center justify-between gap-3"
                            @click="openMobile = !openMobile"
                            :aria-expanded="openMobile">
                        <span class="flex items-center gap-2 text-sm font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                            </svg>
                            Filtrele
                            @if($activeCount > 0)
                                <span class="ml-1 inline-flex items-center justify-center h-5 min-w-5 px-1.5 rounded-full bg-primary text-primary-content text-[11px] font-semibold">
                                    {{ $activeCount }}
                                </span>
                            @endif
                        </span>
                        <span class="text-base-content/40 transition"
                              :class="openMobile ? 'rotate-180' : ''">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>

                    <form method="GET" action="{{ route('packages.index') }}" id="filterForm"
                          class="ns-surface rounded-xl"
                          x-show="isDesktop || openMobile"
                          x-cloak>
                        {{-- Preserve sort across filter changes --}}
                        @if($sort !== 'featured')
                            <input type="hidden" name="sort" value="{{ $sort }}">
                        @endif

                        {{-- Header --}}
                        <div class="px-5 py-4 border-b border-base-300 flex items-center justify-between">
                            <span class="text-sm font-semibold">Filtrele</span>
                            @if($hasActiveFilter)
                                <a href="{{ route('packages.index', ['sort' => $sort !== 'featured' ? $sort : null]) }}"
                                   class="text-xs font-medium text-primary hover:underline">
                                    Temizle
                                </a>
                            @endif
                        </div>

                        <div class="divide-y divide-base-300">

                            {{-- Sponsor only --}}
                            <div class="px-5 py-4">
                                <label class="flex items-center gap-2.5 cursor-pointer">
                                    <input type="checkbox" name="sponsor_only" value="1"
                                           @checked($filters['sponsor_only'])
                                           onchange="this.form.submit()"
                                           class="checkbox checkbox-sm checkbox-primary">
                                    <span class="text-sm">Sadece sponsor paketleri</span>
                                </label>
                            </div>

                            {{-- Operatör --}}
                            @if($operators->isNotEmpty())
                                <div class="px-5 py-4">
                                    <div class="text-xs font-semibold uppercase tracking-wider text-base-content/55 mb-3">
                                        Operatör
                                    </div>
                                    <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                        @foreach($operators as $op)
                                            <label class="flex items-center gap-2.5 cursor-pointer">
                                                <input type="checkbox" name="operator[]" value="{{ $op->id }}"
                                                       @checked(in_array($op->id, $filters['operator']))
                                                       onchange="this.form.submit()"
                                                       class="checkbox checkbox-sm checkbox-primary">
                                                <span class="text-sm truncate">{{ $op->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Altyapı --}}
                            <div class="px-5 py-4">
                                <div class="text-xs font-semibold uppercase tracking-wider text-base-content/55 mb-3">
                                    Altyapı
                                </div>
                                <div class="space-y-2">
                                    @foreach($infraOptions as $value => $label)
                                        <label class="flex items-center gap-2.5 cursor-pointer">
                                            <input type="checkbox" name="infrastructure[]" value="{{ $value }}"
                                                   @checked(in_array($value, $filters['infrastructure']))
                                                   onchange="this.form.submit()"
                                                   class="checkbox checkbox-sm checkbox-primary">
                                            <span class="text-sm">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Hız --}}
                            <div class="px-5 py-4">
                                <div class="text-xs font-semibold uppercase tracking-wider text-base-content/55 mb-3">
                                    Hız
                                </div>
                                <div class="space-y-2">
                                    @foreach($speedTiers as $value => $label)
                                        <label class="flex items-center gap-2.5 cursor-pointer">
                                            <input type="checkbox" name="speed[]" value="{{ $value }}"
                                                   @checked(in_array($value, $filters['speed']))
                                                   onchange="this.form.submit()"
                                                   class="checkbox checkbox-sm checkbox-primary">
                                            <span class="text-sm">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Taahhüt --}}
                            <div class="px-5 py-4">
                                <div class="text-xs font-semibold uppercase tracking-wider text-base-content/55 mb-3">
                                    Taahhüt
                                </div>
                                <div class="space-y-2">
                                    @foreach($commitmentOptions as $value => $label)
                                        <label class="flex items-center gap-2.5 cursor-pointer">
                                            <input type="radio" name="commitment" value="{{ $value }}"
                                                   @checked((string) ($filters['commitment'] ?? '') === (string) $value)
                                                   onchange="this.form.submit()"
                                                   class="radio radio-sm radio-primary">
                                            <span class="text-sm">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Fiyat --}}
                            <div class="px-5 py-4">
                                <div class="text-xs font-semibold uppercase tracking-wider text-base-content/55 mb-3">
                                    Aylık Fiyat (TL)
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="price_min" inputmode="numeric"
                                           value="{{ $filters['price_min'] }}"
                                           placeholder="{{ $priceBounds?->min_price ? (int) $priceBounds->min_price : 'Min' }}"
                                           class="input input-bordered input-sm w-full rounded-md text-sm">
                                    <span class="text-base-content/40">—</span>
                                    <input type="number" name="price_max" inputmode="numeric"
                                           value="{{ $filters['price_max'] }}"
                                           placeholder="{{ $priceBounds?->max_price ? (int) $priceBounds->max_price : 'Max' }}"
                                           class="input input-bordered input-sm w-full rounded-md text-sm">
                                </div>
                                <button type="submit" class="mt-3 btn btn-sm btn-outline w-full">
                                    Fiyatı uygula
                                </button>
                            </div>
                        </div>

                        {{-- No-JS fallback --}}
                        <noscript>
                            <div class="px-5 py-4 border-t border-base-300">
                                <button type="submit" class="btn btn-primary btn-sm w-full">Filtrele</button>
                            </div>
                        </noscript>
                    </form>
                </div>
            </aside>

            {{-- ====== Content ====== --}}
            <div class="lg:col-span-9">

                {{-- Sort toolbar --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    @php $queryWithoutSort = array_filter(request()->except(['sort', 'page']), fn($v) => $v !== null && $v !== ''); @endphp
                    <div class="inline-flex items-center gap-1 p-1 rounded-md bg-base-200 border border-base-300 overflow-x-auto">
                        @foreach($sortOptions as $key => $label)
                            <a href="{{ route('packages.index', array_merge($queryWithoutSort, ['sort' => $key])) }}"
                               class="ns-sort-pill {{ $sort === $key ? 'ns-sort-pill--active' : '' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <div class="text-sm text-base-content/60">
                        <span class="font-semibold text-base-content">{{ $packages->total() }}</span> sonuç
                    </div>
                </div>

                {{-- Active filter chips --}}
                @if($hasActiveFilter)
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-xs text-base-content/55">Aktif filtreler:</span>

                        @foreach($filters['operator'] as $opId)
                            @php $op = $operators->firstWhere('id', $opId); @endphp
                            @if($op)
                                @include('frontend.packages._filter-chip', ['label' => $op->name, 'remove' => request()->fullUrlWithQuery(['operator' => array_values(array_diff($filters['operator'], [$opId])) ?: null])])
                            @endif
                        @endforeach

                        @foreach($filters['infrastructure'] as $infra)
                            @include('frontend.packages._filter-chip', ['label' => $infraOptions[$infra] ?? $infra, 'remove' => request()->fullUrlWithQuery(['infrastructure' => array_values(array_diff($filters['infrastructure'], [$infra])) ?: null])])
                        @endforeach

                        @foreach($filters['speed'] as $sp)
                            @include('frontend.packages._filter-chip', ['label' => $speedTiers[$sp] ?? $sp, 'remove' => request()->fullUrlWithQuery(['speed' => array_values(array_diff($filters['speed'], [$sp])) ?: null])])
                        @endforeach

                        @if(in_array($filters['commitment'], ['0', '1'], true))
                            @include('frontend.packages._filter-chip', ['label' => $filters['commitment'] === '0' ? 'Taahhütsüz' : 'Taahhütli', 'remove' => request()->fullUrlWithQuery(['commitment' => null])])
                        @endif

                        @if($filters['price_min'] !== null || $filters['price_max'] !== null)
                            @include('frontend.packages._filter-chip', ['label' => ($filters['price_min'] ?? '0') . '–' . ($filters['price_max'] ?? '∞') . ' TL', 'remove' => request()->fullUrlWithQuery(['price_min' => null, 'price_max' => null])])
                        @endif

                        @if($filters['sponsor_only'])
                            @include('frontend.packages._filter-chip', ['label' => 'Sponsor', 'remove' => request()->fullUrlWithQuery(['sponsor_only' => null])])
                        @endif
                    </div>
                @endif

                {{-- Grid --}}
                <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                    @forelse($packages as $package)
                        <article class="ns-pkg-card {{ $package->is_sponsored ? 'ns-pkg-card--sponsored' : '' }}">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <x-brand-mark :operator="$package->operator" size="md" />
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold truncate">{{ $package->operator->name }}</div>
                                        @if($package->is_sponsored)
                                            <div class="text-xs text-primary">Sponsor</div>
                                        @endif
                                    </div>
                                </div>

                                @if($package->infrastructure_type)
                                    <span class="ns-pkg-infra">{{ $package->infrastructure_type }}</span>
                                @endif
                            </div>

                            <a href="{{ route('packages.show', $package->slug) }}" class="block mt-5 no-underline">
                                <h3 class="text-base font-semibold leading-snug line-clamp-2 text-base-content hover:text-primary">
                                    {{ $package->name }}
                                </h3>
                            </a>

                            <dl class="mt-5 grid grid-cols-3 gap-3 text-sm border-t border-base-300 pt-5">
                                <div>
                                    <dt class="text-base-content/55">Hız</dt>
                                    <dd class="mt-0.5 font-semibold">{{ $package->speed }} Mbps</dd>
                                </div>
                                <div>
                                    <dt class="text-base-content/55">Taahhüt</dt>
                                    <dd class="mt-0.5 font-semibold">
                                        {{ $package->commitment_period > 0 ? $package->commitment_period.' ay' : 'Yok' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-base-content/55">Kota</dt>
                                    <dd class="mt-0.5 font-semibold truncate">{{ $package->quota }}</dd>
                                </div>
                            </dl>

                            <div class="mt-auto pt-5 flex items-end justify-between gap-3">
                                <div>
                                    <span class="text-2xl font-bold text-base-content">
                                        {{ number_format($package->price, 2, ',', '.') }}
                                    </span>
                                    <span class="text-sm text-base-content/60 ml-0.5">TL/ay</span>
                                </div>

                                <div class="flex items-center gap-2" x-data>
                                    <button type="button"
                                            class="btn btn-sm btn-outline"
                                            @click.prevent.stop="
                                                const res = $store.compare.has({{ $package->id }})
                                                    ? ($store.compare.remove({{ $package->id }}), { ok: true, reason: 'removed' })
                                                    : $store.compare.add({{ $package->id }});
                                                if (!res.ok && res.reason === 'limit') alert('En fazla 5 paket karşılaştırabilirsin.');
                                            "
                                            :class="$store.compare.has({{ $package->id }}) ? 'btn-error' : ''">
                                        <span x-text="$store.compare.has({{ $package->id }}) ? 'Çıkar' : 'Kıyasla'"></span>
                                    </button>

                                    <a href="{{ route('packages.show', $package->slug) }}"
                                       class="btn btn-sm btn-primary">
                                        Detay
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="lg:col-span-2 xl:col-span-3 rounded-xl border border-dashed border-base-300 bg-base-100 p-12 text-center">
                            <p class="text-sm text-base-content/60">Filtrelerinize uyan paket bulunamadı.</p>
                            <a href="{{ route('packages.index') }}" class="btn btn-sm btn-outline mt-4">
                                Filtreleri temizle
                            </a>
                        </div>
                    @endforelse
                </div>

                @if($packages->hasPages())
                    <div class="mt-10 flex justify-center">
                        {{ $packages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
