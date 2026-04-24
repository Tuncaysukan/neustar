@extends('frontend.layouts.app')

@section('title', 'Taahhüt Sayacı - Neustar')

@section('content')
    <section class="py-12 sm:py-16" x-data="{
        startDate: '',
        months: '12',
        result: null,
        compute() {
            if (!this.startDate) return;
            const start = new Date(this.startDate + 'T00:00:00');
            const end = new Date(start);
            end.setMonth(end.getMonth() + Number(this.months));
            const now = new Date();
            const diffMs = end.getTime() - now.getTime();
            const diffDays = Math.max(0, Math.ceil(diffMs / (1000 * 60 * 60 * 24)));
            const diffMonths = Math.max(0, Math.floor(diffDays / 30));
            this.result = { end, diffDays, diffMonths };
        }
    }">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="max-w-2xl">
                <div class="ns-section-eyebrow">Araç</div>
                <h1 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight">Taahhüt sayacı</h1>
                <p class="mt-3 text-sm sm:text-base text-base-content/70 leading-relaxed">
                    Taahhüt bitişini net gör, geçiş zamanlamasını doğru ayarla.
                </p>
            </div>

            <div class="mt-10 grid grid-cols-1 lg:grid-cols-12 gap-5">

                {{-- Form --}}
                <div class="lg:col-span-7 ns-surface p-6 sm:p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ns-meta-label block mb-1.5">Başlangıç</label>
                            <input type="date" class="input input-bordered w-full" x-model="startDate" @change="compute()">
                        </div>
                        <div>
                            <label class="ns-meta-label block mb-1.5">Süre (Ay)</label>
                            <select class="select select-bordered w-full" x-model="months" @change="compute()">
                                <option value="12">12</option>
                                <option value="24">24</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-lg mt-6 w-full" @click="compute()">
                        Hesapla
                    </button>

                    <div class="mt-6" x-show="result" x-transition x-cloak>
                        <div class="rounded-xl bg-base-200 border border-base-300 p-6">
                            <div class="ns-meta-label">Kalan</div>
                            <div class="mt-3 grid grid-cols-2 gap-2.5">
                                <div class="rounded-md bg-base-100 border border-base-300 p-4 text-center">
                                    <div class="text-3xl font-bold tabular-nums" x-text="result?.diffDays ?? 0"></div>
                                    <div class="ns-meta-label mt-1">Gün</div>
                                </div>
                                <div class="rounded-md bg-base-100 border border-base-300 p-4 text-center">
                                    <div class="text-3xl font-bold tabular-nums" x-text="result?.diffMonths ?? 0"></div>
                                    <div class="ns-meta-label mt-1">Ay</div>
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-base-content/70">
                                Bitiş tarihi:
                                <span class="font-semibold text-base-content" x-text="result ? result.end.toLocaleDateString('tr-TR') : ''"></span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tip --}}
                <aside class="lg:col-span-5">
                    <div class="ns-surface p-6">
                        <div class="ns-section-eyebrow">İpucu</div>
                        <h3 class="mt-2 text-lg font-semibold">Geçiş zamanlaması</h3>
                        <p class="mt-2 text-sm text-base-content/70 leading-relaxed">
                            Taahhüdün bitmesine 2–3 ay kala kampanyaları taramak daha avantajlı olur.
                            Bazı operatörler cayma bedeli desteği sunabiliyor.
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('packages.index') }}" class="btn btn-ghost btn-sm">Paketleri incele</a>
                            <a href="{{ route('compare') }}" class="btn btn-ghost btn-sm">Karşılaştır</a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
