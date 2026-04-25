@extends('frontend.layouts.app')

@section('title', 'Markalar — Neustar')

@section('content')
    <section class="py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
                <div class="max-w-2xl">
                    <div class="ns-section-eyebrow">Katalog</div>
                    <h1 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight">Markalar</h1>
                    <p class="mt-3 text-sm sm:text-base text-base-content/70 leading-relaxed">
                        İnternet servis sağlayıcılarını inceleyin, paketlerini tek ekranda karşılaştırın.
                    </p>
                </div>
                <a href="{{ route('packages.index') }}" class="btn btn-ghost btn-sm self-start sm:self-auto">
                    Paketlere git
                </a>
            </div>

            {{-- Grid --}}
            <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($operators as $operator)
                    <a href="{{ route('operators.show', $operator->slug) }}"
                       class="ns-surface ns-surface--hover p-6 group no-underline flex flex-col">

                        <div class="flex items-center justify-between">
                            <span class="ns-meta-label">ISP</span>
                            @if($operator->is_active)
                                <span class="ns-meta-label text-primary">aktif</span>
                            @endif
                        </div>

                        <div class="mt-5 rounded-md bg-base-200 border border-base-300 h-24 flex items-center justify-center overflow-hidden px-3">
                            @if($operator->logo)
                                <img src="{{ $operator->logo_url }}" alt="{{ $operator->name }} logosu"
                                     class="h-full w-full object-contain opacity-90 group-hover:opacity-100 transition"
                                     loading="lazy">
                            @else
                                <x-brand-mark :operator="$operator" size="lg" class="h-full w-full" />
                            @endif
                        </div>

                        <h3 class="mt-5 text-base font-semibold leading-snug text-base-content group-hover:text-primary transition">
                            {{ $operator->name }}
                        </h3>

                        <p class="mt-2 text-sm text-base-content/65 leading-relaxed line-clamp-3">
                            {{ $operator->description ?: 'Güncel internet kampanyaları ve yüksek hız seçenekleri.' }}
                        </p>

                        <div class="mt-5 pt-4 border-t border-base-300 flex items-center justify-between text-sm">
                            <span class="font-semibold text-[#3d87d9]">Paketleri gör</span>
                            <span class="text-[#3d87d9] font-semibold group-hover:translate-x-0.5 transition-transform">→</span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $operators->links() }}
            </div>
        </div>
    </section>
@endsection
