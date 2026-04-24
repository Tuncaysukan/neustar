@extends('frontend.layouts.app')

@section('title', 'Blog — Neustar')
@section('meta_description', 'Tarife inceleme, hız testi, altyapı rehberleri.')

@section('content')
    {{-- ===== Header ===== --}}
    <section class="border-b border-base-300 bg-base-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="max-w-2xl">
                <div class="ns-section-eyebrow">Blog</div>
                <h1 class="mt-2 text-2xl sm:text-4xl font-bold tracking-tight">
                    Tarife, altyapı, hız testi — arkasındaki detaylar.
                </h1>
                <p class="mt-4 text-base text-base-content/65 leading-relaxed">
                    Operatörlerin kampanya sayfalarında görmeyeceğin notlar ve kısa rehberler.
                </p>

                <form action="{{ route('blog.index') }}" method="GET" class="mt-6 flex items-center gap-2 max-w-lg">
                    @if($category)
                        <input type="hidden" name="kategori" value="{{ $category }}">
                    @endif
                    <div class="relative flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                             class="h-4 w-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-base-content/40">
                            <path fill-rule="evenodd" d="M8.5 3.5a5 5 0 103.976 8.043l2.99 2.99a.75.75 0 101.06-1.06l-2.99-2.99A5 5 0 008.5 3.5zm-3.5 5a3.5 3.5 0 117 0 3.5 3.5 0 01-7 0z" clip-rule="evenodd" />
                        </svg>
                        <input type="search" name="q" value="{{ $search }}"
                               placeholder="Ara: altyapı, taahhüt, hız…"
                               class="input input-bordered w-full pl-10 rounded-md font-medium">
                    </div>
                    <button class="btn btn-primary">Ara</button>
                </form>
            </div>
        </div>
    </section>

    {{-- ===== Categories ===== --}}
    @if($categories->isNotEmpty())
        <div class="border-b border-base-300 bg-base-100">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-2 overflow-x-auto">
                <a href="{{ route('blog.index') }}"
                   class="shrink-0 px-3 py-1 rounded-md text-sm font-medium transition no-underline
                          {{ ! $category ? 'bg-base-content text-base-100' : 'text-base-content/70 hover:bg-base-200' }}">
                    Tümü
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('blog.index', ['kategori' => $cat]) }}"
                       class="shrink-0 px-3 py-1 rounded-md text-sm font-medium transition no-underline
                              {{ $category === $cat ? 'bg-base-content text-base-100' : 'text-base-content/70 hover:bg-base-200' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ===== Content ===== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        @if($search)
            <div class="mb-8 text-sm text-base-content/65">
                <span class="font-semibold text-base-content">“{{ $search }}”</span> için
                <span class="font-semibold text-base-content">{{ $blogs->total() }}</span> sonuç
                @if($category) · kategori: <span class="font-semibold">{{ $category }}</span> @endif
            </div>
        @endif

        {{-- Featured --}}
        @if($featured && ! $search)
            <a href="{{ route('blog.show', $featured->slug) }}"
               class="ns-surface rounded-xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 group transition hover:border-base-content hover:border-opacity-20 no-underline">
                <div class="lg:col-span-7 relative aspect-[16/10] lg:aspect-auto lg:min-h-[360px] bg-base-200">
                    @if($featured->image)
                        <img src="{{ $featured->image }}" alt="{{ $featured->title }}"
                             class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                    @endif
                </div>
                <div class="lg:col-span-5 p-6 sm:p-10 flex flex-col justify-between">
                    <div>
                        @if($featured->category)
                            <div class="text-xs font-medium text-primary">{{ $featured->category }}</div>
                        @endif
                        <h2 class="mt-2 text-xl sm:text-2xl font-bold leading-tight line-clamp-3">
                            {{ $featured->title }}
                        </h2>
                        <p class="mt-3 text-sm text-base-content/70 leading-relaxed line-clamp-3">
                            {{ $featured->display_excerpt }}
                        </p>
                    </div>
                    <div class="mt-6 flex items-center justify-between text-xs text-base-content/55">
                        <span>{{ optional($featured->display_date)->format('d M Y') }} · {{ $featured->reading_time }} dk</span>
                        <span class="text-primary font-medium">Oku →</span>
                    </div>
                </div>
            </a>
        @endif

        {{-- Grid --}}
        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($blogs as $post)
                <a href="{{ route('blog.show', $post->slug) }}"
                   class="ns-surface rounded-xl overflow-hidden group transition hover:border-base-content hover:border-opacity-20 no-underline flex flex-col">
                    <div class="relative h-44 bg-base-200">
                        @if($post->image)
                            <img src="{{ $post->image }}" alt="{{ $post->title }}"
                                 class="h-full w-full object-cover" loading="lazy">
                        @endif
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        @if($post->category)
                            <div class="text-xs font-medium text-base-content/60">{{ $post->category }}</div>
                        @endif
                        <h3 class="mt-1 text-base font-semibold leading-snug line-clamp-2">
                            {{ $post->title }}
                        </h3>
                        <p class="mt-2 text-sm text-base-content/65 leading-relaxed line-clamp-3">
                            {{ $post->display_excerpt }}
                        </p>
                        <div class="mt-auto pt-4 flex items-center justify-between text-xs text-base-content/55">
                            <span>{{ optional($post->display_date)->format('d M Y') }}</span>
                            <span>{{ $post->reading_time }} dk</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="md:col-span-2 lg:col-span-3 rounded-xl border border-dashed border-base-300 bg-base-100 p-10 text-center">
                    <p class="text-sm text-base-content/60">
                        @if($search) “{{ $search }}” için sonuç bulunamadı. @else Henüz yazı yayınlanmadı. @endif
                    </p>
                    @if($search || $category)
                        <a href="{{ route('blog.index') }}" class="btn btn-sm btn-outline mt-4">Filtreleri temizle</a>
                    @endif
                </div>
            @endforelse
        </div>

        @if($blogs->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $blogs->links() }}
            </div>
        @endif
    </section>

    <div class="h-16"></div>
@endsection
