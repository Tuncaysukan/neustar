@extends('frontend.layouts.app')

@php
    use App\Models\TariffSeoContent;

    $h1      = $seo?->resolvedH1()
               ?? ($metaFromTemplate['h1'] ?? null)
               ?? ($cityName . ' ' . $districtName . ' Ev İnternet Kampanyaları ve Fiyat Karşılaştırma');
    $intro   = $seo?->resolvedIntro()
               ?? ($metaFromTemplate['intro'] ?? null)
               ?? ($cityName . ' ' . $districtName . ' için hazırladığımız güncel ev interneti tarife listesidir.');
    $metaTitle = $seo?->meta_title
                 ?? ($metaFromTemplate['meta_title'] ?? null)
                 ?? ($h1 . ' — Neustar');
    $metaDesc  = $seo?->meta_description
                 ?? ($metaFromTemplate['meta_description'] ?? null)
                 ?? $intro;
    $seoFooter = $seo?->seo_footer_text ?? ($metaFromTemplate['seo_footer'] ?? null);

    $cityTariffUrl = route('tariffs.city', [
        'urlSlug' => TariffSeoContent::cityUrlSlug($citySlug),
    ]);

    // SSS: sayfa bazlı varsa onlar, yoksa genel
    $faqList = collect();
    if ($seo && !empty($seo->faqs)) {
        $faqList = collect($seo->faqs)->filter(fn($f) => !empty($f['question']) && !empty($f['answer']));
    } elseif ($generalFaqs->isNotEmpty()) {
        $faqList = $generalFaqs->map(fn($f) => ['question' => $f->question, 'answer' => $f->answer]);
    }
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDesc)

@section('content')

{{-- ===== Hero / Header ===== --}}
<section class="border-b border-base-300 bg-base-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-xs text-base-content/55" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-base-content transition">Ana sayfa</a>
            <span>/</span>
            <a href="{{ route('packages.index') }}" class="hover:text-base-content transition">İnternet Tarifeleri</a>
            <span>/</span>
            <a href="{{ $cityTariffUrl }}" class="hover:text-base-content transition">{{ $cityName }}</a>
            <span>/</span>
            <span class="text-base-content">{{ $districtName }}</span>
        </nav>

        <div class="mt-4 max-w-3xl">
            <div class="ns-section-eyebrow">{{ $cityName }} / {{ $districtName }}</div>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight">{{ $h1 }}</h1>
            <p class="mt-3 text-sm sm:text-base text-base-content/65 leading-relaxed">
                {{ $intro }}
            </p>
        </div>
    </div>
</section>

{{-- ===== Tarife Kartları ===== --}}
<section class="py-10 sm:py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Sonuç sayısı --}}
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-base-content/60">
                <span class="font-semibold text-base-content">{{ $packages->count() }}</span>
                aktif tarife listeleniyor
            </p>
            <a href="{{ $cityTariffUrl }}" class="text-xs text-primary hover:underline">
                ← {{ $cityName }} tüm tarifeleri
            </a>
        </div>

        @if($packages->isEmpty())
            <div class="rounded-xl border border-dashed border-base-300 bg-base-100 p-16 text-center">
                <p class="text-sm text-base-content/60">{{ $districtName }} için henüz tarife eklenmemiş.</p>
                <a href="{{ route('packages.index') }}" class="btn btn-sm btn-outline mt-4">
                    Tüm tarifeleri gör
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($packages as $package)
                    @include('frontend.tariffs._package-card', ['package' => $package])
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ===== SEO Alt Metin ===== --}}
@if($seoFooter)
<section class="border-t border-base-300 bg-base-100">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="prose prose-sm max-w-none text-base-content/70 leading-relaxed">
            {!! nl2br(e($seoFooter)) !!}
        </div>
    </div>
</section>
@endif

{{-- ===== FAQ Section ===== --}}
@if($faqList->isNotEmpty())
<section class="border-t border-base-300">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        <h2 class="text-xl font-bold mb-6">{{ $cityName }} {{ $districtName }} İnternet Hakkında Sıkça Sorulanlar</h2>
        <div class="divide-y divide-base-300 border-y border-base-300">
            @foreach($faqList as $faq)
                @php
                    $q = is_array($faq) ? ($faq['question'] ?? '') : $faq->question;
                    $a = is_array($faq) ? ($faq['answer'] ?? '') : $faq->answer;
                @endphp
                @if($q && $a)
                <details class="group">
                    <summary class="cursor-pointer select-none py-4 flex items-center justify-between gap-4">
                        <span class="text-base font-medium">{{ $q }}</span>
                        <span class="shrink-0 text-base-content/40 group-open:rotate-180 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                    </summary>
                    <div class="pb-5 text-sm text-base-content/70 leading-relaxed">{{ $a }}</div>
                </details>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
