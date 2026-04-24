@extends('frontend.layouts.app')

@section('content')
    {{-- ====================== HERO (B: compact, search-led) ====================== --}}
    <section class="ns-hero">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 pt-14 pb-16 sm:pt-20 sm:pb-20 relative z-10">
            <div class="max-w-2xl">
                <span class="ns-hero-kicker">İnternet tarifeleri</span>
                <h1 class="ns-hero-title mt-4">
                    En uygun tarifeyi bul.
                </h1>
                <p class="mt-4 text-base sm:text-lg text-neutral-content/70 max-w-lg leading-relaxed">
                    Operatör, altyapı ve hıza göre filtrele. Fiyatları yan yana gör.
                </p>
            </div>

            {{-- Search bar: 3 inputs + submit, filling the width below the title --}}
            <form action="{{ route('packages.index') }}" method="GET" class="mt-10">
                <div class="ns-searchbar">
                    <div class="grid grid-cols-1 md:grid-cols-12 items-stretch md:divide-x md:divide-base-300">
                        <div class="md:col-span-4 ns-searchfield">
                            <span class="ns-searchlabel">Operatör</span>
                            <div class="relative">
                                <select name="operator" class="ns-searchselect pr-8">
                                    <option value="">Tümü</option>
                                    @foreach($operators as $op)
                                        <option value="{{ $op->id }}">{{ $op->name }}</option>
                                    @endforeach
                                </select>
                                <span class="pointer-events-none absolute right-1 top-1/2 -translate-y-1/2 text-base-content/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="md:col-span-3 ns-searchfield">
                            <span class="ns-searchlabel">Altyapı</span>
                            <div class="relative">
                                <select name="infrastructure" class="ns-searchselect pr-8">
                                    <option value="">Farketmez</option>
                                    <option value="fiber">Fiber</option>
                                    <option value="vdsl">VDSL</option>
                                    <option value="adsl">ADSL</option>
                                </select>
                                <span class="pointer-events-none absolute right-1 top-1/2 -translate-y-1/2 text-base-content/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="md:col-span-3 ns-searchfield">
                            <span class="ns-searchlabel">Hız</span>
                            <div class="relative">
                                <select name="speed" class="ns-searchselect pr-8">
                                    <option value="">Tüm hızlar</option>
                                    <option value="16-35">16–35 Mbps</option>
                                    <option value="50-100">50–100 Mbps</option>
                                    <option value="200-1000">200 Mbps ve üzeri</option>
                                </select>
                                <span class="pointer-events-none absolute right-1 top-1/2 -translate-y-1/2 text-base-content/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="md:col-span-2 p-2">
                            <button type="submit" class="btn btn-primary w-full h-full">
                                Tarifeleri Gör
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- ====================== Stats + Operators (merged strip) ====================== --}}
    <section class="bg-base-100 border-b border-base-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center gap-5 lg:gap-8">

                <div class="flex items-center gap-8 shrink-0">
                    <div>
                        <div class="text-xl font-bold text-base-content">
                            {{ $operators->count() ?: 8 }}
                        </div>
                        <div class="text-xs text-base-content/55">Operatör</div>
                    </div>
                    <div class="h-8 w-px bg-base-300"></div>
                    <div>
                        <div class="text-xl font-bold text-base-content">
                            {{ \App\Models\InternetPackage::where('is_active', true)->count() }}
                        </div>
                        <div class="text-xs text-base-content/55">Aktif tarife</div>
                    </div>
                </div>

                @if($operators->count() > 0)
                    <div class="hidden lg:block h-8 w-px bg-base-300"></div>
                    <div class="flex flex-wrap items-center gap-2 flex-1 min-w-0">
                        @foreach($operators as $op)
                            <a href="{{ route('operators.show', $op->slug) }}"
                               class="ns-logo-chip inline-flex items-center gap-2"
                               title="{{ $op->name }}">
                                <x-brand-mark :operator="$op" size="xs" />
                                <span>{{ $op->name }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ====================== Tools ====================== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('speed-test') }}" class="ns-tool-tile group">
                <div class="flex items-start gap-5">
                    <div class="h-11 w-11 shrink-0 rounded-md grid place-items-center bg-primary/10 text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                            <path d="M12 12l4-2"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-base font-semibold">Hız Testi</div>
                        <p class="mt-1 text-sm text-base-content/65 leading-relaxed">
                            İndirme ve yükleme hızını ölç, paketinden aldığın gerçek değeri gör.
                        </p>
                        <div class="mt-4 text-sm font-medium text-primary">
                            Teste başla →
                        </div>
                    </div>
                </div>
            </a>

            <a href="#altyapi-sorgulama" class="ns-tool-tile group">
                <div class="flex items-start gap-5">
                    <div class="h-11 w-11 shrink-0 rounded-md grid place-items-center bg-primary/10 text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 6-9 13-9 13S3 16 3 10a9 9 0 1118 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-base font-semibold">Altyapı Sorgula</div>
                        <p class="mt-1 text-sm text-base-content/65 leading-relaxed">
                            Adresinde fiber, VDSL veya ADSL var mı? Maksimum alınabilir hızı öğren.
                        </p>
                        <div class="mt-4 text-sm font-medium text-primary">
                            Sorgula →
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </section>

    {{-- ====================== Öne Çıkan Paketler ====================== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-20">
        <div class="flex items-end justify-between gap-4 flex-wrap">
            <div>
                <div class="ns-section-eyebrow">Öne çıkan paketler</div>
                <h2 class="ns-section-title mt-2">Editörün seçtikleri</h2>
            </div>
            <a href="{{ route('packages.index') }}" class="text-sm font-medium text-primary">
                Tüm paketler →
            </a>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($featuredPackages as $package)
                <a href="{{ route('packages.show', $package->slug) }}"
                   class="ns-offer p-6 block group">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <x-brand-mark :operator="$package->operator" size="md" />
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-base-content truncate">
                                    {{ $package->operator->name }}
                                </div>
                                @if($package->is_sponsored)
                                    <div class="text-xs text-primary">Sponsor</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h3 class="mt-4 text-base font-semibold leading-snug line-clamp-2">
                        {{ $package->name }}
                    </h3>

                    <div class="mt-5 flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-base-content">
                            {{ number_format($package->price, 0, ',', '.') }}
                        </span>
                        <span class="text-sm text-base-content/60">TL/ay</span>
                    </div>

                    <div class="mt-5 pt-5 border-t border-base-300 grid grid-cols-3 gap-3 text-sm">
                        <div>
                            <div class="text-base-content/55">Hız</div>
                            <div class="mt-0.5 font-semibold">{{ $package->speed }} Mbps</div>
                        </div>
                        <div>
                            <div class="text-base-content/55">Altyapı</div>
                            <div class="mt-0.5 font-semibold">{{ $package->infrastructure_type ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-base-content/55">Taahhüt</div>
                            <div class="mt-0.5 font-semibold">
                                {{ $package->commitment_period > 0 ? $package->commitment_period.' ay' : 'Yok' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-sm font-medium text-primary">
                        İncele →
                    </div>
                </a>
            @empty
                <div class="md:col-span-2 lg:col-span-3 rounded-xl border border-dashed border-base-300 bg-base-100 p-10 text-center">
                    <p class="text-sm text-base-content/60">Henüz öne çıkan paket eklenmemiş.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ====================== Altyapı Sorgulama ====================== --}}
    @include('frontend.partials._address-lookup')

    {{-- ====================== Blog ====================== --}}
    @if($latestBlogs->count() > 0)
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-20">
        <div class="flex items-end justify-between gap-4 flex-wrap">
            <div>
                <div class="ns-section-eyebrow">Yazılar</div>
                <h2 class="ns-section-title mt-2">Blog</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-primary">
                Tüm yazılar →
            </a>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($latestBlogs as $blog)
                <a href="{{ route('blog.show', $blog->slug) }}"
                   class="ns-surface rounded-xl overflow-hidden group transition hover:border-base-content hover:border-opacity-20">
                    <div class="relative h-44 bg-base-200">
                        @if($blog->image)
                            <img src="{{ $blog->image }}" alt="{{ $blog->title }}" class="h-full w-full object-cover" loading="lazy">
                        @endif
                    </div>
                    <div class="p-5">
                        @if($blog->category)
                            <div class="text-xs font-medium text-base-content/60">
                                {{ $blog->category }}
                            </div>
                        @endif
                        <h3 class="mt-1 text-base font-semibold leading-snug line-clamp-2">
                            {{ $blog->title }}
                        </h3>
                        <p class="mt-2 text-sm text-base-content/65 leading-relaxed line-clamp-2">
                            {{ $blog->display_excerpt }}
                        </p>
                        <div class="mt-4 flex items-center justify-between text-xs text-base-content/55">
                            <span>{{ optional($blog->display_date)->format('d M Y') }}</span>
                            <span>{{ $blog->reading_time }} dk okuma</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ====================== SSS ====================== --}}
    @if($faqs->count() > 0)
    <section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 mt-20">
        <div>
            <div class="ns-section-eyebrow">SSS</div>
            <h2 class="ns-section-title mt-2">Sıkça sorulanlar</h2>
        </div>

        <div class="mt-8 divide-y divide-base-300 border-y border-base-300">
            @foreach($faqs as $faq)
                <details class="group">
                    <summary class="cursor-pointer select-none py-4 flex items-center justify-between gap-4">
                        <span class="text-base font-medium text-base-content">{{ $faq->question }}</span>
                        <span class="shrink-0 text-base-content/40 group-open:rotate-180 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </summary>
                    <div class="pb-5 text-sm text-base-content/70 leading-relaxed">
                        {{ $faq->answer }}
                    </div>
                </details>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ====================== İletişim ====================== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-20 mb-20">
        <div class="ns-surface rounded-xl overflow-hidden grid grid-cols-1 lg:grid-cols-12">
            <div class="lg:col-span-4 ns-hero p-8 sm:p-10 relative">
                <div class="relative z-10">
                    <div class="text-xs font-medium uppercase tracking-wider text-primary">İletişim</div>
                    <h3 class="mt-2 text-2xl font-bold">Sorunuz mu var?</h3>
                    <p class="mt-3 text-sm text-neutral-content/70 leading-relaxed">
                        Tarife önerisi, teknik sorun veya iş birliği için bize yazın.
                    </p>

                    <div class="mt-8 space-y-5 text-sm">
                        <div>
                            <div class="text-neutral-content/50 text-xs font-medium uppercase tracking-wider">E-posta</div>
                            <div class="mt-1 font-medium">info@neustar.com</div>
                        </div>
                        <div>
                            <div class="text-neutral-content/50 text-xs font-medium uppercase tracking-wider">Telefon</div>
                            <div class="mt-1 font-medium">0543 180 00 79</div>
                        </div>
                        <div class="text-xs text-neutral-content/50 leading-relaxed pt-4 border-t border-white/10">
                            Hafta içi 09:00 – 18:00 arası müşteri hizmetleri aktif.
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 p-8 sm:p-10 bg-base-100">
                <form method="POST" action="{{ route('contact.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-base-content/80 mb-1.5">Adınız Soyadınız</label>
                        <input name="name" value="{{ old('name') }}" class="input input-bordered w-full rounded-md" required>
                        @error('name') <div class="mt-1 text-xs text-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-base-content/80 mb-1.5">E-posta</label>
                        <input name="email" type="email" value="{{ old('email') }}" class="input input-bordered w-full rounded-md" required>
                        @error('email') <div class="mt-1 text-xs text-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-base-content/80 mb-1.5">Konu</label>
                        <select name="subject" class="select select-bordered w-full rounded-md">
                            <option value="">Genel Bilgi</option>
                            <option @selected(old('subject')==='Tarife Önerisi')>Tarife Önerisi</option>
                            <option @selected(old('subject')==='Teknik Sorun')>Teknik Sorun</option>
                            <option @selected(old('subject')==='İş Ortaklığı')>İş Ortaklığı</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-base-content/80 mb-1.5">Mesajınız</label>
                        <textarea name="message" rows="5" class="textarea textarea-bordered w-full rounded-md" required>{{ old('message') }}</textarea>
                        @error('message') <div class="mt-1 text-xs text-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button class="btn btn-primary px-8" type="submit">Gönder</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
