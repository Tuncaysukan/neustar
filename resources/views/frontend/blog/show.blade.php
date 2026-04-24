@extends('frontend.layouts.app')

@section('title', ($blog->seo_title ?: $blog->title) . ' — Neustar')
@section('meta_description', $blog->seo_description ?: $blog->display_excerpt)

@section('content')
    <article class="bg-base-100">
        {{-- ===== Header ===== --}}
        <header class="border-b border-base-300">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pt-10 pb-12 sm:pt-14">
                <nav class="flex items-center gap-2 text-xs text-base-content/55">
                    <a href="{{ route('home') }}" class="hover:text-base-content">Anasayfa</a>
                    <span>/</span>
                    <a href="{{ route('blog.index') }}" class="hover:text-base-content">Blog</a>
                    @if($blog->category)
                        <span>/</span>
                        <a href="{{ route('blog.index', ['kategori' => $blog->category]) }}" class="hover:text-base-content">
                            {{ $blog->category }}
                        </a>
                    @endif
                </nav>

                <h1 class="mt-5 text-3xl sm:text-4xl font-bold leading-tight">
                    {{ $blog->title }}
                </h1>

                @if($blog->display_excerpt)
                    <p class="mt-4 text-base sm:text-lg text-base-content/70 leading-relaxed">
                        {{ $blog->display_excerpt }}
                    </p>
                @endif

                <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-base-content/60">
                    <span>{{ optional($blog->display_date)->isoFormat('D MMMM YYYY') }}</span>
                    <span class="text-base-content/30">·</span>
                    <span>{{ $blog->reading_time }} dk okuma</span>
                    @if($blog->views)
                        <span class="text-base-content/30">·</span>
                        <span>{{ number_format($blog->views, 0, ',', '.') }} görüntülenme</span>
                    @endif
                </div>
            </div>
        </header>

        {{-- Cover --}}
        @if($blog->image)
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 mt-10">
                <img src="{{ $blog->image }}" alt="{{ $blog->title }}"
                     class="w-full h-auto rounded-xl border border-base-300 aspect-[16/9] object-cover"
                     loading="eager">
            </div>
        @endif

        {{-- ===== Body ===== --}}
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 mt-10 grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Share --}}
            <aside class="lg:col-span-2 order-2 lg:order-1">
                <div class="lg:sticky lg:top-24">
                    <div class="text-xs font-medium uppercase tracking-wider text-base-content/55 mb-3">
                        Paylaş
                    </div>
                    <div class="flex lg:flex-col gap-2">
                        @php
                            $shareUrl = urlencode(url()->current());
                            $shareTitle = urlencode($blog->title);
                        @endphp
                        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"
                           target="_blank" rel="noopener"
                           class="h-9 w-9 rounded-md border border-base-300 grid place-items-center text-base-content/70 hover:text-primary hover:border-primary transition"
                           aria-label="Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M18.244 2H21l-6.54 7.47L22 22h-6.91l-4.69-6.12L4.8 22H2l7.04-8.04L2 2h6.91l4.23 5.56L18.24 2zm-1.21 18h1.59L7.04 4H5.34l11.69 16z"/></svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                           target="_blank" rel="noopener"
                           class="h-9 w-9 rounded-md border border-base-300 grid place-items-center text-base-content/70 hover:text-primary hover:border-primary transition"
                           aria-label="Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M22 12a10 10 0 10-11.56 9.87v-6.98H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.98A10 10 0 0022 12z"/></svg>
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}"
                           target="_blank" rel="noopener"
                           class="h-9 w-9 rounded-md border border-base-300 grid place-items-center text-base-content/70 hover:text-primary hover:border-primary transition"
                           aria-label="WhatsApp">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M20.5 3.48A11.85 11.85 0 0012.04 0C5.5 0 .16 5.33.16 11.88c0 2.1.55 4.14 1.6 5.95L0 24l6.33-1.66a11.87 11.87 0 005.7 1.45h.01c6.54 0 11.88-5.33 11.88-11.87 0-3.18-1.24-6.16-3.42-8.44zM12.04 21.8h-.01a9.9 9.9 0 01-5.04-1.38l-.36-.22-3.76.99 1-3.66-.23-.38a9.88 9.88 0 01-1.51-5.27c0-5.45 4.44-9.88 9.9-9.88a9.86 9.86 0 019.88 9.89c0 5.45-4.43 9.9-9.87 9.9zm5.42-7.4c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.66.15s-.76.96-.93 1.15c-.17.2-.34.22-.64.07a8.1 8.1 0 01-2.38-1.47 8.96 8.96 0 01-1.65-2.05c-.17-.3-.02-.46.13-.6.13-.14.3-.35.44-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.66-1.6-.9-2.2-.24-.58-.48-.5-.66-.5h-.56c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.48.71.3 1.26.5 1.7.64.71.23 1.36.2 1.87.12.57-.08 1.75-.72 2-1.4.25-.7.25-1.27.17-1.4-.07-.12-.27-.2-.56-.35z"/></svg>
                        </a>
                        <button type="button"
                                x-data="{ copied: false }"
                                @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 1600)"
                                class="h-9 w-9 rounded-md border border-base-300 grid place-items-center text-base-content/70 hover:text-primary hover:border-primary transition relative"
                                :aria-label="copied ? 'Kopyalandı' : 'Linki kopyala'">
                            <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                            <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-primary"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                    </div>
                </div>
            </aside>

            {{-- Content --}}
            <div class="lg:col-span-10 order-1 lg:order-2">
                <div class="ns-prose">
                    {!! $blog->content !!}
                </div>

                {{-- Footer meta --}}
                <div class="mt-10 pt-6 border-t border-base-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($blog->category)
                            <a href="{{ route('blog.index', ['kategori' => $blog->category]) }}" class="ns-tag">
                                # {{ $blog->category }}
                            </a>
                        @endif
                    </div>
                    <a href="{{ route('blog.index') }}" class="btn btn-sm btn-outline">
                        ← Tüm yazılar
                    </a>
                </div>

                {{-- CTA --}}
                <div class="mt-10 rounded-xl border border-base-300 bg-base-100 p-6 flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <div class="flex-1">
                        <h3 class="text-base font-semibold">Uygun tarifeyi bul</h3>
                        <p class="mt-1 text-sm text-base-content/65">
                            Yazıdaki kriterlere uyan paketleri karşılaştır.
                        </p>
                    </div>
                    <a href="{{ route('packages.index') }}" class="btn btn-primary">
                        Paketleri Gör
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== Related ===== --}}
        @if($related->isNotEmpty())
            <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-20">
                <div class="flex items-end justify-between gap-4 flex-wrap">
                    <div>
                        <div class="ns-section-eyebrow">Devamı</div>
                        <h2 class="ns-section-title mt-2">İlgili yazılar</h2>
                    </div>
                    <a href="{{ route('blog.index') }}" class="text-sm font-medium text-primary">
                        Tüm yazılar →
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach($related as $r)
                        <a href="{{ route('blog.show', $r->slug) }}"
                           class="ns-surface rounded-xl overflow-hidden group transition hover:border-base-content hover:border-opacity-20 no-underline flex flex-col">
                            <div class="relative h-40 bg-base-200">
                                @if($r->image)
                                    <img src="{{ $r->image }}" alt="{{ $r->title }}"
                                         class="h-full w-full object-cover" loading="lazy">
                                @endif
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                @if($r->category)
                                    <div class="text-xs font-medium text-base-content/60">{{ $r->category }}</div>
                                @endif
                                <h3 class="mt-1 text-base font-semibold leading-snug line-clamp-2">{{ $r->title }}</h3>
                                <div class="mt-auto pt-4 flex items-center justify-between text-xs text-base-content/55">
                                    <span>{{ optional($r->display_date)->format('d M Y') }}</span>
                                    <span>{{ $r->reading_time }} dk</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="h-20"></div>
    </article>
@endsection
