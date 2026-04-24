{{--
    Tarife sayfaları için paket kartı.
    Değişkenler: $package (InternetPackage with operator)
--}}
<article class="ns-pkg-card {{ $package->is_sponsored ? 'ns-pkg-card--sponsored' : '' }} flex flex-col">

    {{-- Operatör başlığı --}}
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

    {{-- Paket adı --}}
    <a href="{{ route('packages.show', $package->slug) }}" class="block mt-5 no-underline">
        <h3 class="text-base font-semibold leading-snug line-clamp-2 text-base-content hover:text-primary transition">
            {{ $package->name }}
        </h3>
    </a>

    {{-- Hız / Taahhüt / Kota --}}
    <dl class="mt-5 grid grid-cols-3 gap-3 text-sm border-t border-base-300 pt-5">
        <div>
            <dt class="text-base-content/55">Hız</dt>
            <dd class="mt-0.5 font-semibold">{{ $package->speed }} Mbps</dd>
        </div>
        <div>
            <dt class="text-base-content/55">Taahhüt</dt>
            <dd class="mt-0.5 font-semibold">
                {{ $package->commitment_period > 0 ? $package->commitment_period . ' ay' : 'Yok' }}
            </dd>
        </div>
        <div>
            <dt class="text-base-content/55">Kota</dt>
            <dd class="mt-0.5 font-semibold truncate">{{ $package->quota }}</dd>
        </div>
    </dl>

    {{-- Fiyat + Butonlar --}}
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

    {{-- Dış bağlantı: Operatörün resmi sitesi --}}
    @if($package->operator->website_url)
    <div class="mt-4 pt-4 border-t border-base-300">
        <a href="{{ $package->operator->website_url }}"
           target="_blank"
           rel="noopener noreferrer nofollow"
           class="flex items-center gap-1.5 text-xs text-base-content/55 hover:text-primary transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            Detaylı bilgi ve resmi başvuru için operatörün sitesini ziyaret edin
        </a>
    </div>
    @endif

</article>
