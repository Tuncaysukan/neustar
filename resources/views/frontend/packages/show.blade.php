@extends('frontend.layouts.app')

@section('title', ($package->seo_title ?: $package->name) . ' — Neustar')
@section('meta_description', $package->seo_description ?: \Illuminate\Support\Str::limit($package->description, 160))

@section('content')
    {{-- ===== Header strip ===== --}}
    <section class="border-b border-base-300 bg-base-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10">
                <div class="min-w-0 max-w-2xl">
                    <nav class="flex items-center gap-2 text-xs text-base-content/55">
                        <a class="hover:text-base-content" href="{{ route('home') }}">Anasayfa</a>
                        <span>/</span>
                        <a class="hover:text-base-content" href="{{ route('packages.index') }}">Paketler</a>
                        <span>/</span>
                        <span class="text-base-content">{{ $package->operator->name }}</span>
                    </nav>

                    <div class="mt-4 flex items-center gap-4">
                        <x-brand-mark :operator="$package->operator" size="lg" rounded="lg" />
                        <div class="min-w-0">
                            <a href="{{ route('operators.show', $package->operator->slug) }}"
                               class="text-sm font-semibold text-base-content/80 hover:text-primary transition no-underline">
                                {{ $package->operator->name }}
                            </a>
                            <h1 class="mt-0.5 text-2xl sm:text-4xl font-bold leading-tight">
                                {{ $package->name }}
                            </h1>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        @if($package->infrastructure_type)
                            <span class="ns-pkg-infra">{{ $package->infrastructure_type }}</span>
                        @endif
                        @if($package->is_sponsored)
                            <span class="text-xs font-medium text-primary">Sponsor</span>
                        @endif
                    </div>

                    @if($package->description)
                        <p class="mt-5 text-sm sm:text-base text-base-content/70 leading-relaxed">
                            {{ $package->description }}
                        </p>
                    @endif
                </div>

                {{-- Price card --}}
                <div class="w-full lg:w-[380px]">
                    <div class="ns-surface rounded-xl p-6">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <div class="text-sm text-base-content/55">Aylık ücret</div>
                                <div class="mt-1">
                                    <span class="text-3xl sm:text-4xl font-bold text-primary">
                                        {{ number_format($package->price, 2, ',', '.') }}
                                    </span>
                                    <span class="ml-1 text-sm text-base-content/60">TL</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-base-content/55">Taahhüt</div>
                                <div class="mt-1 text-base font-semibold">
                                    {{ $package->commitment_period > 0 ? $package->commitment_period.' ay' : 'Yok' }}
                                </div>
                            </div>
                        </div>

                        <dl class="mt-5 grid grid-cols-3 gap-3 text-sm border-t border-base-300 pt-5">
                            <div>
                                <dt class="text-base-content/55">Hız</dt>
                                <dd class="mt-0.5 font-semibold">{{ $package->speed }} Mbps</dd>
                            </div>
                            <div>
                                <dt class="text-base-content/55">Upload</dt>
                                <dd class="mt-0.5 font-semibold">{{ $package->upload_speed ? $package->upload_speed.' Mbps' : '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-base-content/55">Kota</dt>
                                <dd class="mt-0.5 font-semibold truncate">{{ $package->quota }}</dd>
                            </div>
                        </dl>

                        <div class="mt-5 flex flex-col sm:flex-row gap-2" x-data>
                            <a href="{{ route('packages.apply', $package->slug) }}"
                               class="btn btn-primary flex-1">
                                Başvur
                            </a>
                            <button type="button"
                                    class="btn btn-outline"
                                    @click="
                                        const res = $store.compare.has({{ $package->id }})
                                            ? ($store.compare.remove({{ $package->id }}), { ok: true, reason: 'removed' })
                                            : $store.compare.add({{ $package->id }});
                                        if (!res.ok && res.reason === 'limit') alert('En fazla 5 paket karşılaştırabilirsin.');
                                    "
                                    :class="$store.compare.has({{ $package->id }}) ? 'btn-error' : ''">
                                <span x-text="$store.compare.has({{ $package->id }}) ? 'Çıkar' : 'Karşılaştır'"></span>
                            </button>
                        </div>

                        <p class="mt-3 text-xs text-base-content/55">
                            Karşılaştırmada en fazla 5 paket tutulur.
                        </p>

                        @if($package->operator->website_url)
                        <div class="mt-3 pt-3 border-t border-base-300">
                            <a href="{{ $package->operator->website_url }}"
                               target="_blank" rel="noopener noreferrer nofollow"
                               class="flex items-center gap-1.5 text-xs text-base-content/50 hover:text-primary transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Detaylı bilgi için operatörün sitesini ziyaret edin
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Body ===== --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- ── Ana içerik (sol/orta) ── --}}
            <div class="lg:col-span-8 space-y-10">

                {{-- Tarife Hakkında --}}
                @if($package->description)
                <section class="ns-surface rounded-xl p-6 sm:p-8">
                    <h2 class="text-xl font-bold mb-4">Tarife hakkında</h2>
                    <div class="text-sm sm:text-base text-base-content/75 leading-relaxed whitespace-pre-line">{{ $package->description }}</div>
                </section>
                @endif

                {{-- Tarife Detayları — ANA İÇERİKTE --}}
                <section class="ns-surface rounded-xl p-6 sm:p-8">
                    <h2 class="text-xl font-bold mb-6">Tarife detayları</h2>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        {{-- Fiyat --}}
                        <div class="ns-surface rounded-xl p-4 border border-primary/20 bg-primary/5 col-span-2 sm:col-span-1">
                            <div class="text-xs text-base-content/55 mb-1">Aylık ücret</div>
                            <div class="text-2xl font-bold text-primary">
                                {{ number_format($package->price, 2, ',', '.') }}
                                <span class="text-sm font-normal text-base-content/55">TL</span>
                            </div>
                        </div>
                        {{-- İndirme --}}
                        <div class="ns-surface rounded-xl p-4">
                            <div class="text-xs text-base-content/55 mb-1">İndirme hızı</div>
                            <div class="text-xl font-bold">{{ $package->speed }}
                                <span class="text-sm font-normal text-base-content/55">Mbps</span>
                            </div>
                        </div>
                        {{-- Yükleme --}}
                        <div class="ns-surface rounded-xl p-4">
                            <div class="text-xs text-base-content/55 mb-1">Yükleme hızı</div>
                            <div class="text-xl font-bold">
                                {{ $package->upload_speed ? $package->upload_speed : '—' }}
                                @if($package->upload_speed)
                                    <span class="text-sm font-normal text-base-content/55">Mbps</span>
                                @endif
                            </div>
                        </div>
                        {{-- Kota --}}
                        <div class="ns-surface rounded-xl p-4">
                            <div class="text-xs text-base-content/55 mb-1">Kota</div>
                            <div class="text-base font-bold">{{ $package->quota }}</div>
                        </div>
                        {{-- Altyapı --}}
                        <div class="ns-surface rounded-xl p-4">
                            <div class="text-xs text-base-content/55 mb-1">Altyapı</div>
                            <div class="text-base font-bold">{{ $package->infrastructure_type ?: '—' }}</div>
                        </div>
                        {{-- Taahhüt --}}
                        <div class="ns-surface rounded-xl p-4">
                            <div class="text-xs text-base-content/55 mb-1">Taahhüt</div>
                            <div class="text-base font-bold">
                                {{ $package->commitment_period > 0 ? $package->commitment_period . ' ay' : 'Taahhütsüz' }}
                            </div>
                        </div>
                    </div>

                    {{-- Başvur CTA --}}
                    <div class="mt-6 flex flex-col sm:flex-row gap-3 pt-6 border-t border-base-300">
                        <a href="{{ route('packages.apply', $package->slug) }}"
                           class="btn sm:w-auto bg-[#fdee00] text-black border-0 hover:bg-[#e6d700] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black/30">
                            Hemen başvur →
                        </a>
                        @if($package->operator->website_url)
                        <a href="{{ $package->operator->website_url }}"
                           target="_blank" rel="noopener noreferrer nofollow"
                           class="btn btn-ghost btn-sm self-center text-xs text-base-content/50 hover:text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Operatörün resmi sitesi
                        </a>
                        @endif
                    </div>
                </section>

                {{-- Paket özeti --}}
                <section class="ns-surface rounded-xl p-6 sm:p-8">
                    <h2 class="text-xl font-bold">Paket özeti</h2>

                    <dl class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div>
                            <dt class="text-base-content/55">İndirme</dt>
                            <dd class="mt-1 text-base font-semibold">{{ $package->speed }} Mbps</dd>
                        </div>
                        <div>
                            <dt class="text-base-content/55">Yükleme</dt>
                            <dd class="mt-1 text-base font-semibold">{{ $package->upload_speed ? $package->upload_speed.' Mbps' : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-base-content/55">Kota</dt>
                            <dd class="mt-1 text-base font-semibold">{{ $package->quota }}</dd>
                        </div>
                        <div>
                            <dt class="text-base-content/55">Altyapı</dt>
                            <dd class="mt-1 text-base font-semibold">{{ $package->infrastructure_type ?: '—' }}</dd>
                        </div>
                    </dl>
                </section>

                {{-- Avantaj / Dezavantaj --}}
                @if($package->advantages || $package->disadvantages)
                <section class="ns-surface rounded-xl p-6 sm:p-8">
                    <h2 class="text-xl font-bold">Artılar &amp; Eksiler</h2>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-semibold text-success">Artılar</h3>
                            <ul class="mt-3 space-y-2 text-sm text-base-content/80">
                                @forelse(explode("\n", (string) $package->advantages) as $adv)
                                    @if(trim($adv))
                                        <li class="flex gap-2">
                                            <span class="text-success mt-0.5">+</span>
                                            <span>{{ $adv }}</span>
                                        </li>
                                    @endif
                                @empty
                                    <li class="text-base-content/55">Belirtilmedi</li>
                                @endforelse
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-error">Eksiler</h3>
                            <ul class="mt-3 space-y-2 text-sm text-base-content/80">
                                @forelse(explode("\n", (string) $package->disadvantages) as $dis)
                                    @if(trim($dis))
                                        <li class="flex gap-2">
                                            <span class="text-error mt-0.5">−</span>
                                            <span>{{ $dis }}</span>
                                        </li>
                                    @endif
                                @empty
                                    <li class="text-base-content/55">Belirtilmedi</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </section>
                @endif

                {{-- Taahhüt Şartları --}}
                <section class="ns-surface rounded-xl p-6 sm:p-8">
                    <h2 class="text-xl font-bold">Taahhüt şartları</h2>

                    <dl class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-base-content/55">Süre</dt>
                            <dd class="mt-1 text-base font-semibold">
                                {{ $package->commitment_period > 0 ? $package->commitment_period.' ay' : 'Taahhütsüz' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-base-content/55">Not</dt>
                            <dd class="mt-1 text-sm text-base-content/70 leading-relaxed">
                                Cayma ve indirim iadesi koşulları operatöre göre değişir.
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- FAQ --}}
                @if($faqs->isNotEmpty())
                <section class="ns-surface rounded-xl p-6 sm:p-8">
                    <h2 class="text-xl font-bold">Sıkça sorulan sorular</h2>

                    <div class="mt-5 space-y-4">
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
                </section>
                @endif

                {{-- Reviews --}}
                <section class="ns-surface rounded-xl p-6 sm:p-8">
                    <h2 class="text-xl font-bold">Kullanıcı yorumları</h2>

                    <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-5">
                            <form method="POST" action="{{ route('packages.reviews.store', $package->slug) }}"
                                  class="rounded-lg border border-base-300 bg-base-100 p-5 space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-base-content/80 mb-1.5">Ad</label>
                                    <input name="name" value="{{ old('name') }}" class="input input-bordered w-full rounded-md" required>
                                    @error('name') <div class="mt-1 text-xs text-error">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-base-content/80 mb-1.5">Puan</label>
                                    <select name="rating" class="select select-bordered w-full rounded-md">
                                        <option value="">Seç (opsiyonel)</option>
                                        @for($i=5; $i>=1; $i--)
                                            <option value="{{ $i }}" @selected(old('rating')===(string)$i)>{{ $i }}/5</option>
                                        @endfor
                                    </select>
                                    @error('rating') <div class="mt-1 text-xs text-error">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-base-content/80 mb-1.5">Yorum</label>
                                    <textarea name="comment" rows="4" class="textarea textarea-bordered w-full rounded-md" required>{{ old('comment') }}</textarea>
                                    @error('comment') <div class="mt-1 text-xs text-error">{{ $message }}</div> @enderror
                                </div>
                                <button class="btn btn-primary w-full" type="submit">Yorumu Gönder</button>
                            </form>
                        </div>

                        <div class="lg:col-span-7 space-y-3">
                            @forelse($package->reviews as $review)
                                <div class="rounded-lg border border-base-300 bg-base-100 p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold">{{ $review->name }}</div>
                                            @if($review->rating)
                                                <div class="mt-0.5 text-xs text-primary font-medium">{{ $review->rating }}/5</div>
                                            @endif
                                        </div>
                                        <div class="text-xs text-base-content/50">{{ $review->created_at?->format('d.m.Y') }}</div>
                                    </div>
                                    <div class="mt-3 text-sm text-base-content/75 leading-relaxed">
                                        {{ $review->comment }}
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-lg border border-dashed border-base-300 bg-base-100 p-8 text-center">
                                    <div class="text-sm text-base-content/55">Henüz yorum yok. İlk yorumu sen bırak.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>

            {{-- ── Aside (sağ) ── --}}
            <aside class="lg:col-span-4 space-y-4">

                {{-- Başvur kutusu --}}
                <div class="ns-surface rounded-xl p-6">
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <h3 class="text-base font-semibold">Hemen başvur</h3>
                        @if($package->is_sponsored)
                            <span class="text-xs font-medium text-primary">Sponsor</span>
                        @endif
                    </div>
                    <p class="text-sm text-base-content/65 leading-relaxed">
                        {{ $package->operator->name }} resmi sitesine yönlendirileceksiniz.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('packages.apply', $package->slug) }}"
                           class="btn btn-primary w-full">
                            Başvur →
                        </a>
                    </div>
                    @if($package->operator->website_url)
                    <div class="mt-3 pt-3 border-t border-base-300">
                        <a href="{{ $package->operator->website_url }}"
                           target="_blank" rel="noopener noreferrer nofollow"
                           class="flex items-center gap-1.5 text-xs text-base-content/50 hover:text-primary transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Operatörün resmi sitesi
                        </a>
                    </div>
                    @endif
                </div>

                {{-- Karşılaştırma sepeti --}}
                <div class="ns-surface rounded-xl p-6">
                    <h3 class="text-base font-semibold">Karşılaştırma sepeti</h3>
                    <p class="mt-1 text-sm text-base-content/65 leading-relaxed">
                        Bu paketi sepete ekle, 5 pakete kadar karşılaştır.
                    </p>
                    <div class="mt-4 flex gap-2" x-data>
                        <button type="button" class="btn btn-outline btn-sm flex-1"
                                @click="
                                    const res = $store.compare.has({{ $package->id }})
                                        ? ($store.compare.remove({{ $package->id }}), { ok: true, reason: 'removed' })
                                        : $store.compare.add({{ $package->id }});
                                    if (!res.ok && res.reason === 'limit') alert('En fazla 5 paket karşılaştırabilirsin.');
                                ">
                            <span x-text="$store.compare.has({{ $package->id }}) ? 'Çıkar' : 'Karşılaştır'"></span>
                        </button>
                        <a class="btn btn-outline btn-sm" :href="$store.compare.url()">Aç</a>
                    </div>
                </div>

            </aside>
        </div>
    </section>
@endsection
