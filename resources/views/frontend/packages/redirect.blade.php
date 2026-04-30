@extends('frontend.layouts.app')

@section('title', $package->name . ' — Başvuru Yönlendirmesi')
@section('meta_description', $package->operator->name . ' resmi sitesine yönlendiriliyorsunuz.')

@php $target = $target ?? $package->affiliate_url ?? $package->operator->website_url ?? null; @endphp

@section('content')
<div class="px-4 pt-2 pb-4 sm:py-8 bg-base-100"
     x-data="{
         countdown: 5,
         done: false,
         init() {
             const target = {{ $target ? json_encode($target) : 'null' }};
             if (!target) { this.done = true; return; }
             const t = setInterval(() => {
                 this.countdown--;
                 if (this.countdown <= 0) {
                     clearInterval(t);
                     this.done = true;
                     window.location.href = target;
                 }
             }, 1000);
         }
     }">

    <div class="w-full max-w-lg mx-auto text-center">

        {{-- Operatör logosu --}}
        <div class="flex justify-center mb-2 pt-2">
            <x-brand-mark :operator="$package->operator" size="xl" rounded="xl" />
        </div>

        {{-- Başlık --}}
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight mt-0">
            {{ $package->operator->name }} resmi sitesine yönlendiriliyorsunuz
        </h1>
        <p class="mt-1 text-sm text-base-content/60 leading-relaxed">
            <span class="font-medium text-base-content">{{ $package->name }}</span> paketi için
            başvurmak üzere operatörün resmi sayfasına aktarılıyorsunuz.
        </p>

        {{-- Paket özet kutusu --}}
        <div class="mt-4 ns-surface rounded-2xl p-4 text-left">
            <div class="flex items-center gap-3 pb-3 border-b border-base-300">
                <x-brand-mark :operator="$package->operator" size="md" />
                <div class="min-w-0">
                    <div class="text-xs text-base-content/55">{{ $package->operator->name }}</div>
                    <div class="text-sm font-semibold leading-snug">{{ $package->name }}</div>
                </div>
                @if($package->infrastructure_type)
                    <span class="ml-auto ns-pkg-infra shrink-0">{{ $package->infrastructure_type }}</span>
                @endif
            </div>

            <dl class="mt-3 grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-base-content/55">Aylık ücret</dt>
                    <dd class="mt-0.5 text-base font-bold text-primary">
                        {{ number_format($package->price, 2, ',', '.') }}
                        <span class="text-xs font-normal text-base-content/55">TL</span>
                    </dd>
                </div>
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
        </div>

        {{-- Geri sayım / loading --}}
        @if($target)
        <div class="mt-5">
            {{-- Spinner + geri sayım --}}
            <div class="flex flex-col items-center gap-3" x-show="!done">
                <div class="relative w-14 h-14">
                    <svg class="w-14 h-14 -rotate-90" viewBox="0 0 64 64">
                        <circle cx="32" cy="32" r="28"
                                fill="none" stroke="currentColor"
                                stroke-width="4" class="text-base-300"/>
                        <circle cx="32" cy="32" r="28"
                                fill="none" stroke="currentColor"
                                stroke-width="4" stroke-linecap="round"
                                class="text-primary transition-all duration-1000"
                                :stroke-dasharray="`${(5 - countdown) / 5 * 175.9} 175.9`"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-lg font-bold"
                          x-text="countdown"></span>
                </div>
                <p class="text-sm text-base-content/60">
                    <span x-text="countdown"></span> saniye içinde yönlendiriliyorsunuz…
                </p>
            </div>

            {{-- Yönlendirme tamamlandı --}}
            <div class="flex flex-col items-center gap-2" x-show="done" x-cloak>
                <div class="w-10 h-10 rounded-full bg-success/15 text-success flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm text-base-content/60">Yönlendirme tamamlandı.</p>
            </div>

            {{-- Manuel link --}}
            <div class="mt-4 flex flex-col sm:flex-row items-center justify-center gap-2">
                <a href="{{ $target }}"
                   target="_blank" rel="noopener noreferrer nofollow"
                   class="btn btn-primary w-full sm:w-auto">
                    Hemen git →
                </a>
                <a href="{{ route('packages.show', $package->slug) }}"
                   class="btn btn-ghost btn-sm text-base-content/60">
                    ← Pakete geri dön
                </a>
            </div>

            <p class="mt-3 text-[11px] text-base-content/40 leading-relaxed max-w-sm mx-auto">
                Yönlendirme çalışmıyorsa "Hemen git" butonuna tıklayın.
            </p>
        </div>
        @else
        <div class="mt-5 p-4 rounded-xl border border-dashed border-base-300 text-center">
            <p class="text-sm text-base-content/60">Bu paket için henüz başvuru linki tanımlanmamış.</p>
            <a href="{{ route('packages.show', $package->slug) }}" class="btn btn-outline btn-sm mt-3">
                ← Pakete geri dön
            </a>
        </div>
        @endif

    </div>
</div>
@endsection
