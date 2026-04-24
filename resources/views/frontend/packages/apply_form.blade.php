@extends('frontend.layouts.app')

@section('title', $package->name . ' — Başvuru Formu')

@section('content')
<section class="py-12 sm:py-20 bg-base-200">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            
            {{-- Sol: Paket Özeti --}}
            <div>
                <div class="ns-section-eyebrow">Başvuru</div>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-base-content">
                    {{ $package->operator->name }} başvurusu
                </h1>
                <p class="mt-4 text-base-content/70 leading-relaxed">
                    Aşağıdaki formu doldurarak <span class="font-semibold">{{ $package->name }}</span> paketi için ön başvurunuzu iletebilirsiniz. 
                    Uzman ekibimiz sizi arayarak süreci tamamlayacaktır.
                </p>

                <div class="mt-10 ns-surface rounded-2xl p-6 border border-base-300">
                    <div class="flex items-center gap-4 pb-5 border-b border-base-300">
                        <x-brand-mark :operator="$package->operator" size="md" />
                        <div class="min-w-0">
                            <div class="text-xs text-base-content/55">{{ $package->operator->name }}</div>
                            <div class="text-sm font-semibold leading-snug">{{ $package->name }}</div>
                        </div>
                    </div>

                    <dl class="mt-6 space-y-4">
                        <div class="flex justify-between text-sm">
                            <dt class="text-base-content/55">Hız</dt>
                            <dd class="font-semibold">{{ $package->speed }} Mbps</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-base-content/55">Taahhüt</dt>
                            <dd class="font-semibold">{{ $package->commitment_period > 0 ? $package->commitment_period . ' Ay' : 'Yok' }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-base-content/55">Kota</dt>
                            <dd class="font-semibold">{{ $package->quota }}</dd>
                        </div>
                        <div class="pt-4 border-t border-base-300 flex justify-between items-baseline">
                            <dt class="text-base-content font-medium">Aylık Ücret</dt>
                            <dd class="text-2xl font-bold text-primary">
                                {{ number_format($package->price, 2, ',', '.') }}
                                <span class="text-sm font-semibold text-base-content/60">TL</span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Sağ: Form --}}
            <div class="ns-surface rounded-2xl p-8 shadow-sm">
                @if(session('status'))
                    <div class="text-center py-8">
                        <div class="mx-auto w-16 h-16 rounded-full bg-success/15 text-success flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold mb-2">Başvurunuz Alındı</h2>
                        <p class="text-sm text-base-content/60 leading-relaxed">
                            {{ session('status') }}
                        </p>
                        <a href="{{ route('home') }}" class="btn btn-primary mt-8">Ana Sayfaya Dön</a>
                    </div>
                @else
                    <h2 class="text-lg font-bold mb-6">Bilgilerinizi Bırakın</h2>
                    
                    <form action="{{ route('packages.apply.submit', $package->slug) }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label class="ns-meta-label block mb-1.5 text-xs font-semibold uppercase tracking-wider">Ad Soyad</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                   class="input input-bordered w-full focus:ring-primary/20" 
                                   placeholder="Ahmet Yılmaz">
                            @error('full_name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="ns-meta-label block mb-1.5 text-xs font-semibold uppercase tracking-wider">E-posta Adresi</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="input input-bordered w-full focus:ring-primary/20" 
                                   placeholder="ahmet@example.com">
                            @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div x-data="{ phone: '{{ old('phone', '+90(5__)___-____') }}' }">
                            <label class="ns-meta-label block mb-1.5 text-xs font-semibold uppercase tracking-wider">Telefon Numarası</label>
                            <input type="tel" name="phone" required
                                   class="input input-bordered w-full focus:ring-primary/20"
                                   x-model="phone" @input="maskPhone($event.target); phone = $event.target.value" @focus="maskPhone($event.target)">
                            @error('phone') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-2">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="kvkk" required
                                       class="mt-1 rounded border-base-300 text-primary focus:ring-primary/40">
                                <span class="text-xs text-base-content/60 leading-relaxed">
                                    <a href="{{ url('/kvkk-aydinlatma-metni') }}" target="_blank" class="underline underline-offset-2">KVKK aydınlatma metnini</a>
                                    okudum, bilgilerimin işlenmesine ve uzmanlar tarafından aranmaya izin veriyorum.
                                </span>
                            </label>
                            @error('kvkk') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-full mt-4">
                            Başvuruyu Tamamla →
                        </button>
                    </form>

                    <p class="mt-6 text-[11px] text-base-content/40 text-center">
                        Bilgileriniz 256-bit SSL sertifikası ile korunmaktadır. 
                        Sadece bu başvuru ile ilgili bilgilendirme yapılacaktır.
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
