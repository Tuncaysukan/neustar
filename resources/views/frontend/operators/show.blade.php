@extends('frontend.layouts.app')

@section('title', $operator->name . ' İnternet Paketleri - Neustar')

@section('content')
    {{-- ===== Header ===== --}}
    <section class="border-b border-base-300 bg-base-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-14">
            <div class="max-w-3xl">
                <nav class="ns-section-eyebrow flex items-center gap-1.5" aria-label="Breadcrumb">
                    <a class="hover:text-base-content transition" href="{{ route('operators.index') }}">Markalar</a>
                    <span class="opacity-50">/</span>
                    <span class="text-base-content">{{ $operator->name }}</span>
                </nav>

                <div class="mt-4 flex items-center gap-4">
                    <x-brand-mark :operator="$operator" size="xl" rounded="lg" />
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">
                        {{ $operator->name }} paketleri
                    </h1>
                </div>

                <p class="mt-3 text-sm sm:text-base text-base-content/70 leading-relaxed max-w-2xl">
                    {{ $operator->description ?: 'Bölgenize özel kampanya ve tarifeleri net metriklerle inceleyin.' }}
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-2">
                    <a href="{{ route('packages.index') }}" class="btn btn-primary btn-sm">Tüm paketler</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Packages ===== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($operator->packages as $package)
                <div class="ns-surface ns-surface--hover p-6 group flex flex-col">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <span class="ns-pkg-infra">{{ $package->infrastructure_type }}</span>
                            <a href="{{ route('packages.show', $package->slug) }}" class="block mt-3 no-underline">
                                <h3 class="text-base font-semibold leading-snug line-clamp-2 text-base-content group-hover:text-primary transition">
                                    {{ $package->name }}
                                </h3>
                            </a>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="ns-meta-label">Hız</div>
                            <div class="mt-1 text-sm font-semibold">{{ $package->speed }} Mbps</div>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-2.5">
                        <div class="ns-data-cell">
                            <div class="ns-meta-label">Taahhüt</div>
                            <div class="mt-1 text-sm font-semibold">
                                {{ $package->commitment_period > 0 ? $package->commitment_period . ' Ay' : 'Yok' }}
                            </div>
                        </div>
                        <div class="ns-data-cell">
                            <div class="ns-meta-label">Kota</div>
                            <div class="mt-1 text-sm font-semibold">{{ $package->quota }}</div>
                        </div>
                    </div>

                    <div class="mt-auto pt-5 flex items-end justify-between">
                        <div>
                            <div class="ns-meta-label">Aylık</div>
                            <div class="mt-1 text-2xl font-bold tracking-tight text-primary">
                                {{ number_format($package->price, 2, ',', '.') }}
                                <span class="text-sm font-semibold text-base-content/60">TL</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('packages.show', $package->slug) }}"
                               class="btn btn-sm btn-outline">
                                Detay
                            </a>

                            @if(($package->apply_type ?? 'form') === 'site')
                                <a href="{{ $package->external_url ?? 'https://www.enuygunfinans.com/internet-baglantilari/' }}"
                                   target="_blank" rel="noopener noreferrer nofollow"
                                   class="btn btn-sm btn-primary">
                                    Başvur
                                </a>
                            @elseif(($package->apply_type ?? '') === 'call')
                                <a href="tel:{{ $package->call_number }}"
                                   class="btn btn-sm btn-primary">
                                    Hemen Ara
                                </a>
                            @else
                                <a href="{{ route('packages.apply', $package->slug) }}"
                                   class="btn btn-sm btn-primary">
                                    Başvur
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-base-300 bg-base-100 p-12 text-center">
                    <p class="text-sm text-base-content/60">
                        Bu operatöre ait henüz aktif bir paket bulunmuyor.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ===== FAQ Section ===== --}}
    @if($faqs->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="ns-surface rounded-xl p-6 sm:p-8">
                <h2 class="text-xl font-bold mb-6">Sıkça sorulan sorular</h2>

                <div class="space-y-4">
                    @foreach($faqs as $faq)
                        <details class="collapse collapse-plus ns-surface border border-base-300 rounded-xl bg-base-100">
                            <summary class="collapse-title text-sm font-semibold">
                                {{ $faq->question }}
                            </summary>
                            <div class="collapse-content text-sm text-base-content/70"> 
                                <p>{{ $faq->answer }}</p>
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===== SEO Text Section ===== --}}
    @if($operator->seo_text)
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="ns-surface rounded-xl p-6 sm:p-8">
                <div class="prose prose-sm max-w-none text-base-content/70 leading-relaxed">
                    {!! nl2br(e($operator->seo_text)) !!}
                </div>
            </div>
        </section>
    @endif
@endsection
