@extends('frontend.layouts.app')

@section('title', $package->name . ' — Başvuru Yönlendirmesi')
@section('meta_description', $package->operator->name . ' resmi sitesine yönlendiriliyorsunuz.')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16"
     x-data="{
         countdown: 5,
         done: false,
         init() {
             const target = {{ $package->operator->website_url ? json_encode($package->operator->website_url) : 'null' }};
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

    <div class="w-full max-w-lg text-center">

        {{-- Operatör logosu --}}
        <div class="flex justify-center mb-6">
            <x-brand-mark :operator="$package->operator" size="xl" rounded="xl" />
        </div>

        {{-- Başlık --}}
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
            {{ $package->operator->name }} resmi sitesine yönlendiriliyorsunuz
        </h1>
        <p class="mt-3 text-sm text-base-content/60 leading-relaxed">
            <span class="font-medium text-base-content">{{ $package->name }}</span> paketi için
            başvurmak üzere operatörün resmi sayfasına aktarılıyorsunuz.
        </p>

        {{-- Paket özet kutusu --}}
        <div class="mt-8 ns-surface rounded-2xl p-6 text-left">
            <div class="flex items-center gap-4 pb-5 border-b border-base-300">
                <x-brand-mark :operator="$package->operator" size="md" />
                <div class="min-w-0">
                    <div class="text-xs text-base-content/55">{{ $package->operator->name }}</div>
                    <div class="text-sm font-semibold leading-snug">{{ $package->name }}</div>
                </div>
                @if($package->infrastructure_type)
                    <span class="ml-auto ns-pkg-infra shrink-0">{{ $package->infrastructure_type }}</span>
                @endif
            </div>

            <dl class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <dt class="text-base-content/55">Aylık ücret</dt>
                    <dd class="mt-0.5 text-lg font-bold text-primary">
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
        @if($package->operator->website_url)
        <div class="mt-8">
            {{-- Spinner + geri sayım --}}
            <div class="flex flex-col items-center gap-4" x-show="!done">
                {{-- Dairesel progress --}}
                <div class="relative w-16 h-16">
                    <svg class="w-16 h-16 -rotate-90" viewBox="0 0 64 64">
                        <circle cx="32" cy="32" r="28"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="4"
                                class="text-base-300"/>
                        <circle cx="32" cy="32" r="28"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="4"
                                stroke-linecap="round"
                                class="text-primary transition-all duration-1000"
                                :stroke-dasharray="`${(5 - countdown) / 5 * 175.9} 175.9`"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-xl font-bold"
                          x-text="countdown"></span>
                </div>
                <p class="text-sm text-base-content/60">
                    <span x-text="countdown"></span> saniye içinde yönlendiriliyorsunuz…
                </p>
            </div>

            {{-- Yönlendirme tamamlandı --}}
            <div class="flex flex-col items-center gap-3" x-show="done" x-cloak>
                <div class="w-12 h-12 rounded-full bg-success/15 text-success flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm text-base-content/60">Yönlendirme tamamlandı.</p>
            </div>

            {{-- Manuel link --}}
            <div class="mt-5 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ $package->operator->website_url }}"
                   target="_blank"
                   rel="noopener noreferrer nofollow"
                   class="btn btn-primary">
                    Hemen git →
                </a>
                <a href="{{ route('packages.show', $package->slug) }}"
                   class="btn btn-ghost btn-sm text-base-content/60">
                    ← Pakete geri dön
                </a>
            </div>

            <p class="mt-4 text-[11px] text-base-content/40 leading-relaxed max-w-sm mx-auto">
                Yönlendirme çalışmıyorsa yukarıdaki "Hemen git" butonuna tıklayın.
                Bu sayfa {{ config('app.name') }} ile operatör arasında bağımsız bir geçiş sayfasıdır.
            </p>
        </div>
        @else
        <div class="mt-8 p-5 rounded-xl border border-dashed border-base-300 text-center">
            <p class="text-sm text-base-content/60">
                Bu paket için henüz başvuru linki tanımlanmamış.
            </p>
            <a href="{{ route('packages.show', $package->slug) }}"
               class="btn btn-outline btn-sm mt-4">
                ← Pakete geri dön
            </a>
        </div>
        @endif

    </div>
</div>
@endsection
