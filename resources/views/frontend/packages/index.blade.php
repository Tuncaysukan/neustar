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

    // Tek tek hız değerleri (örnek, DB'den dinamik çekilebilir)
    $speedOptions = [
        '16'  => '16 Mbps',
        '35'  => '35 Mbps',
        '50'  => '50 Mbps',
        '100' => '100 Mbps',
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

    $modemOptions = [
        'free' => 'Ücretsiz',
        'paid' => 'Ücretli',
    ];
@endphp

@section('content')
<section class="py-10 sm:py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- ====== Header ====== --}}
        <div class="max-w-2xl">
           
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight">Ev İnterneti Kampanyaları</h1>
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
                    + count($filters['modem'] ?? []);
            @endphp
            <aside class="lg:col-span-3"
                   x-data="{
                       isDesktop: window.matchMedia('(min-width: 1024px)').matches,
                       openMobile: false,
                       init() {
                           const mql = window.matchMedia('(min-width: 1024px)');
                           mql.addEventListener('change', e => { this.isDesktop = e.matches; });
                       },
                       submitFilter(e) {
                           let form = document.getElementById('filterForm');
                           
                           let operators = [];
                           let infras = [];
                           let speeds = [];
                           let modems = [];
                           
                           form.querySelectorAll('input[type=checkbox]').forEach(el => {
                               if (el.checked) {
                                   if (el.name === 'operator[]') operators.push(el);
                                   if (el.name === 'infrastructure[]') infras.push(el);
                                   if (el.name === 'speed[]') speeds.push(el);
                                   if (el.name === 'modem[]') modems.push(el);
                               }
                           });
                           
                           let basePath = '{{ route('packages.index') }}';
                           let hasSlug = false;
                           
                           // Sadece 1 operatör seçiliyse SEO URL oluştur
                           if (operators.length === 1) {
                               basePath += '/' + operators[0].dataset.slug;
                               hasSlug = true;
                               // Eğer 1 operatör ve 1 altyapı seçiliyse
                               if (infras.length === 1) {
                                   let infraVal = infras[0].value;
                                   basePath += '/' + infraVal.replace('_', '-');
                               }
                           }
                           
                           let params = new URLSearchParams();
                           
                           if (operators.length !== 1) {
                               operators.forEach(op => params.append('operator[]', op.value));
                           }
                           
                           if (operators.length !== 1 || infras.length !== 1) {
                               infras.forEach(inf => params.append('infrastructure[]', inf.value));
                           }
                           
                           speeds.forEach(cb => params.append('speed[]', cb.value));
                           modems.forEach(cb => params.append('modem[]', cb.value));
                           
                           ['commitment'].forEach(name => {
                               form.querySelectorAll('input[type=radio], input[type=checkbox]').forEach(el => {
                                   if (el.name === name && el.checked && el.value) params.append(name, el.value);
                               });
                           });
                           
                           ['price_min', 'price_max', 'sort'].forEach(name => {
                               form.querySelectorAll('input[type=number], input[type=hidden]').forEach(el => {
                                   if (el.name === name && el.value) params.append(name, el.value);
                               });
                           });
                           
                           let qs = params.toString();
                           window.location.href = qs ? basePath + '?' + qs : basePath;
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
                          @change="submitFilter"
                          @submit.prevent="submitFilter"
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
                                                       data-slug="{{ $op->slug }}"
                                                       @checked(in_array($op->id, $filters['operator']))
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
                                                   class="checkbox checkbox-sm checkbox-primary">
                                            <span class="text-sm">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Hız (tek tek) --}}
                            <div class="px-5 py-4">
                                <div class="text-xs font-semibold uppercase tracking-wider text-base-content/55 mb-3">
                                    Hız
                                </div>
                                <div class="space-y-2">
                                    @foreach($speedOptions as $value => $label)
                                        <label class="flex items-center gap-2.5 cursor-pointer">
                                            <input type="checkbox" name="speed[]" value="{{ $value }}"
                                                   @checked(in_array($value, $filters['speed']))
                                                   class="checkbox checkbox-sm checkbox-primary">
                                            <span class="text-sm">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Modem --}}
                            <div class="px-5 py-4">
                                <div class="text-xs font-semibold uppercase tracking-wider text-base-content/55 mb-3">
                                    Modem
                                </div>
                                <div class="space-y-2">
                                    @foreach($modemOptions as $value => $label)
                                        <label class="flex items-center gap-2.5 cursor-pointer">
                                            <input type="checkbox" name="modem[]" value="{{ $value }}"
                                                   @checked(in_array($value, $filters['modem'] ?? []))
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
                                                   class="radio radio-sm radio-primary">
                                            <span class="text-sm">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Fiyat --}}
                            <div class="px-5 py-4">
                                <div class="text-xs font-semibold uppercase tracking-wider text-base-content/55 mb-3">
                                    Başvuru tipi
                                </div>
                                <div class="space-y-2 mb-3">
                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                        <input type="radio" name="frontend_mode" value="bireysel" checked
                                               class="radio radio-sm radio-primary">
                                        <span class="text-sm">Bireysel</span>
                                    </label>
                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                        <input type="radio" name="frontend_mode" value="kurumsal"
                                               class="radio radio-sm radio-primary">
                                        <span class="text-sm">Kurumsal</span>
                                    </label>
                                </div>
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
                            @include('frontend.packages._filter-chip', ['label' => $speedOptions[$sp] ?? ($sp . ' Mbps'), 'remove' => request()->fullUrlWithQuery(['speed' => array_values(array_diff($filters['speed'], [$sp])) ?: null])])
                        @endforeach

                        @foreach($filters['modem'] ?? [] as $md)
                            @include('frontend.packages._filter-chip', ['label' => $modemOptions[$md] ?? $md, 'remove' => request()->fullUrlWithQuery(['modem' => array_values(array_diff($filters['modem'], [$md])) ?: null])])
                        @endforeach

                        @if(in_array($filters['commitment'], ['0', '1'], true))
                            @include('frontend.packages._filter-chip', ['label' => $filters['commitment'] === '0' ? 'Taahhütsüz' : 'Taahhütli', 'remove' => request()->fullUrlWithQuery(['commitment' => null])])
                        @endif

                        @if($filters['price_min'] !== null || $filters['price_max'] !== null)
                            @include('frontend.packages._filter-chip', ['label' => ($filters['price_min'] ?? '0') . '–' . ($filters['price_max'] ?? '∞') . ' TL', 'remove' => request()->fullUrlWithQuery(['price_min' => null, 'price_max' => null])])
                        @endif

                    </div>
                @endif

                {{-- Yatay Kart Listesi --}}
                <div class="mt-6 flex flex-col gap-4">
                    @forelse($packages as $package)
                        <article class="relative bg-base-100 rounded-xl p-4 sm:p-5 border border-base-200 hover:border-primary/40 shadow-sm transition-all duration-200 {{ $package->is_sponsored ? 'ring-1 ring-primary/20 bg-primary/[0.01]' : '' }}">
                            
                            {{-- Top Section: Logo, Stats, Button in ONE ROW --}}
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-2 sm:gap-4 border-b border-base-200/80 pb-1 mb-1 sm:pb-4 sm:mb-4">
                                
                                {{-- Logo --}}
                                <div class="w-32 shrink-0 flex flex-col justify-center">
                                    <div class="h-10 flex items-center justify-center">
                                        <x-brand-mark :operator="$package->operator" class="w-full h-full object-contain object-center mx-auto mix-blend-multiply dark:mix-blend-normal" />
                                    </div>
                                </div>

                                {{-- Stats --}}
                                <div class="flex-1 flex flex-row items-center justify-between gap-2 lg:px-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-semibold text-base-content/50 uppercase tracking-wide mb-0.5">Hız</span>
                                        <div class="flex items-baseline gap-0.5">
                                            <span class="text-sm font-bold text-base-content">{{ $package->speed }}</span>
                                            <span class="text-[10px] text-base-content/60">Mbps</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-semibold text-base-content/50 uppercase tracking-wide mb-0.5">Limit</span>
                                        <span class="text-sm font-bold text-base-content">{{ $package->quota === 'Sınırsız' ? 'Limitsiz' : $package->quota }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-semibold text-base-content/50 uppercase tracking-wide mb-0.5">Taahhüt</span>
                                        <span class="text-sm font-bold text-base-content">{{ $package->commitment_period > 0 ? $package->commitment_period.' ay' : 'Yok' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-semibold text-base-content/50 uppercase tracking-wide mb-0.5">Fiyat</span>
                                        <div class="flex items-baseline gap-0.5">
                                            <span class="text-[15px] font-bold text-base-content">{{ number_format($package->price, 2, ',', '.') }}</span>
                                            <span class="text-[10px] font-bold text-base-content/70">TL</span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Bottom Section: Title & Actions --}}
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                <div class="flex-1 min-w-0 pr-2">
                                    <a href="{{ route('packages.show', $package->slug) }}" class="inline-block group">
                                        <h3 class="text-[13px] sm:text-[14px] font-bold text-base-content group-hover:text-primary transition-colors">
                                            {{ $package->name }}
                                        </h3>
                                    </a>
                                    <p class="text-[11px] sm:text-[12px] text-base-content/60 mt-1 leading-relaxed line-clamp-2">
                                        {{ strip_tags($package->description ?? "Taahhütsüz olarak {$package->speed} Mbps hız ve kotasız olarak sunulmaktadır. {$package->operator->name} ürünüdür.") }}
                                    </p>

                                    <div class="mt-2 flex items-center gap-3">
                                        <a href="{{ route('packages.show', $package->slug) }}"
                                           class="inline-flex items-center gap-1 text-[11px] sm:text-[12px] font-bold text-[#3d87d9] hover:text-[#2f6fbf] transition-colors">
                                            Tarife detayı
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                        </a>

                                        <div class="flex-1 flex justify-center">
                                            <label class="inline-flex items-center gap-2 text-[12px] font-bold text-[#3d87d9] select-none cursor-pointer"
                                                   x-data
                                                   @click.stop
                                                   @mousedown.stop>
                                                <input type="checkbox"
                                                       class="checkbox checkbox-xs border-base-300 w-[18px] h-[18px] [--chkbg:#fdee00] [--chkfg:#000]"
                                                       :checked="$store.compare.has({{ $package->id }})"
                                                       @change="
                                                           const res = $store.compare.has({{ $package->id }})
                                                               ? ($store.compare.remove({{ $package->id }}), { ok: true, reason: 'removed' })
                                                               : $store.compare.add({{ $package->id }});
                                                           if (!res.ok && res.reason === 'limit') {
                                                               $event.target.checked = false;
                                                               alert('En fazla 5 paket karşılaştırabilirsin.');
                                                           }
                                                       " />
                                                <span>Karşılaştır</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="shrink-0 md:self-center flex flex-col items-end w-full md:w-auto">
                                    @php
                                        $btnUrl = ($package->apply_type ?? 'form') === 'site' ? ($package->external_url ?? 'https://www.enuygunfinans.com/internet-baglantilari/') : (($package->apply_type ?? '') === 'call' ? 'tel:'.$package->call_number : route('packages.apply', $package->slug));
                                    @endphp
                                    <a href="{{ $btnUrl }}" class="btn rounded whitespace-nowrap w-full md:w-[160px] justify-center h-[28px] min-h-[28px] px-3 text-[13px] leading-none shrink-0 bg-[#fdee00] text-black border-0 hover:bg-[#e6d700] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black/30">
                                        Hemen başvur
                                    </a>
                                    @if($package->is_sponsored)
                                        <div class="mt-1 text-[12px] font-bold text-[#3d87d9] hover:text-[#2f6fbf] transition-colors text-center w-full">
                                            Sponsor Sağlayıcı
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-base-300 bg-base-100 p-12 text-center">
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
