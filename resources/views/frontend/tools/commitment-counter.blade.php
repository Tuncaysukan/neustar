@extends('frontend.layouts.app')

@section('title', 'Taahhüt Sayacı - Neustar')

@section('content')
    <section class="py-12 sm:py-16" x-data="{
                    startDate: '',
                    months: '12',
                    result: null,

                    /* Hatırlatıcı formu */
                    email: '',
                    phone: '+90(5__)___-____',
                    reminderSent: false,
                    reminderLoading: false,
                    reminderError: '',
                    reminderSuccess: '',
                    kvkk: false,

                    compute() {
                        if (!this.startDate) return;
                        const start = new Date(this.startDate + 'T00:00:00');
                        const end   = new Date(start);
                        end.setMonth(end.getMonth() + Number(this.months));
                        const now     = new Date();
                        const diffMs  = end.getTime() - now.getTime();
                        const diffDays   = Math.max(0, Math.ceil(diffMs / (1000 * 60 * 60 * 24)));
                        const diffMonths = Math.max(0, Math.floor(diffDays / 30));
                        this.result = { end, diffDays, diffMonths };
                        /* Yeni hesaplama yapılınca formu sıfırla */
                        this.reminderSent    = false;
                        this.reminderError   = '';
                        this.reminderSuccess = '';
                        this.kvkk            = false;
                    },

                    async submitReminder() {
                        this.reminderError   = '';
                        this.reminderSuccess = '';

                        let submittedPhone = this.phone === '+90(5__)___-____' ? '' : this.phone;
                        if (!this.email && !submittedPhone) {
                            this.reminderError = 'E-posta veya telefon numarasından en az birini girin.';
                            return;
                        }
                        if (!this.kvkk) {
                            this.reminderError = 'Devam etmek için KVKK izni vermeniz gerekmektedir.';
                            return;
                        }
                        if (!this.result) {
                            this.reminderError = 'Önce taahhüt tarihini hesaplayın.';
                            return;
                        }

                        this.reminderLoading = true;
                        try {
                            const res = await fetch('{{ route('commitment-counter.reminder') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    email:           this.email   || null,
                                    phone:           (this.phone === '+90(5__)___-____' ? null : this.phone),
                                    start_date:      this.startDate,
                                    months:          Number(this.months),
                                    end_date:        this.result.end.toISOString().slice(0, 10),
                                    remaining_days:  this.result.diffDays,
                                    kvkk:            true,
                                }),
                            });
                            const json = await res.json();
                            if (json.ok) {
                                this.reminderSent    = true;
                                this.reminderSuccess = json.message;
                            } else {
                                this.reminderError = json.message || 'Bir hata oluştu.';
                            }
                        } catch (e) {
                            this.reminderError = 'Bağlantı hatası. Lütfen tekrar deneyin.';
                        } finally {
                            this.reminderLoading = false;
                        }
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

                {{-- ── Sol: Hesaplama + Hatırlatıcı ── --}}
                <div class="lg:col-span-7 space-y-5">

                    {{-- Hesaplama formu --}}
                    <div class="ns-surface p-6 sm:p-8">
                        <h2 class="text-base font-semibold mb-5">Taahhüt hesapla</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="ns-meta-label block mb-1.5">Başlangıç tarihi</label>
                                <input type="date" class="input input-bordered w-full" x-model="startDate"
                                    @change="compute()">
                            </div>
                            <div>
                                <label class="ns-meta-label block mb-1.5">Taahhüt süresi</label>
                                <select class="select select-bordered w-full" x-model="months" @change="compute()">
                                    <option value="12">12 ay</option>
                                    <option value="24">24 ay</option>
                                </select>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary btn-lg mt-6 w-full" @click="compute()">
                            Hesapla
                        </button>

                        {{-- Sonuç --}}
                        <div class="mt-6" x-show="result" x-transition x-cloak>
                            <div class="rounded-xl bg-base-200 border border-base-300 p-6">

                                {{-- Kadran + bilgi yan yana --}}
                                <div class="flex flex-col sm:flex-row items-center gap-6">

                                    {{-- SVG Kadran --}}
                                    <div class="shrink-0 relative" style="width:176px;height:176px;">
                                        <svg viewBox="0 0 160 160"
                                             style="width:176px;height:176px;transform:rotate(-90deg);">
                                            {{-- Arka plan halkası --}}
                                            <circle cx="80" cy="80" r="60"
                                                    fill="none"
                                                    stroke="#e5e7eb"
                                                    stroke-width="14"/>
                                            {{-- İlerleme halkası (kalan süre) --}}
                                            <circle cx="80" cy="80" r="60"
                                                    fill="none"
                                                    stroke="#1bb6ad"
                                                    stroke-width="14"
                                                    stroke-linecap="round"
                                                    :stroke-dasharray="`${377 * Math.min(1, result ? result.diffDays / (Number(months) * 30) : 0)} 377`"
                                                    style="transition: stroke-dasharray 0.7s ease;"/>
                                        </svg>
                                        {{-- Ortadaki metin --}}
                                        <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                                            <span class="text-3xl font-bold tabular-nums leading-none"
                                                  x-text="result?.diffDays ?? 0"></span>
                                            <span class="text-xs mt-1 font-semibold uppercase tracking-wider"
                                                  style="color:#6b7280;">Gün</span>
                                        </div>
                                    </div>

                                    {{-- Detay bilgiler --}}
                                    <div class="flex-1 w-full space-y-3">
                                        <div class="flex items-center justify-between rounded-lg bg-base-100 border border-base-300 px-4 py-3">
                                            <span class="text-xs text-base-content/55 font-semibold uppercase tracking-wider">Kalan Ay</span>
                                            <span class="text-xl font-bold tabular-nums" x-text="result?.diffMonths ?? 0"></span>
                                        </div>
                                        <div class="flex items-center justify-between rounded-lg bg-base-100 border border-base-300 px-4 py-3">
                                            <span class="text-xs text-base-content/55 font-semibold uppercase tracking-wider">Bitiş Tarihi</span>
                                            <span class="text-sm font-semibold"
                                                  x-text="result ? result.end.toLocaleDateString('tr-TR') : '—'"></span>
                                        </div>
                                        <div class="flex items-center justify-between rounded-lg px-4 py-3"
                                             :class="result && result.diffDays <= 30 ? 'bg-error/10 border border-error/30' :
                                                     result && result.diffDays <= 90 ? 'bg-warning/10 border border-warning/30' :
                                                     'bg-success/10 border border-success/30'">
                                            <span class="text-xs font-semibold uppercase tracking-wider"
                                                  :class="result && result.diffDays <= 30 ? 'text-error' :
                                                          result && result.diffDays <= 90 ? 'text-warning' :
                                                          'text-success'">Durum</span>
                                            <span class="text-xs font-bold"
                                                  :class="result && result.diffDays <= 30 ? 'text-error' :
                                                          result && result.diffDays <= 90 ? 'text-warning' :
                                                          'text-success'"
                                                  x-text="result && result.diffDays === 0 ? 'Taahhüt bitti!' :
                                                           result && result.diffDays <= 30 ? 'Bitiyor — Hemen karşılaştır!' :
                                                           result && result.diffDays <= 90 ? 'Yaklaşıyor — Kampanyaları tara' :
                                                           'Devam ediyor'">
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Hatırlatıcı formu — sonuç hesaplandıktan sonra göster --}}
                    <div class="ns-surface p-6 sm:p-8" x-show="result" x-transition x-cloak>

                        {{-- Başarı mesajı --}}
                        <template x-if="reminderSent">
                            <div class="flex flex-col items-center text-center py-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-success/15 text-success flex items-center justify-center mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-base-content" x-text="reminderSuccess"></p>
                                <p class="mt-1 text-xs text-base-content/55">
                                    Taahhüt bitimine yakın sizi bilgilendireceğiz.
                                </p>
                            </div>
                        </template>

                        <template x-if="!reminderSent">
                            <div>
                                <h2 class="text-base font-semibold mb-1">Hatırlatıcı kur</h2>
                                <p class="text-sm text-base-content/60 mb-5">
                                    Taahhüt bitimine yakın e-posta veya SMS ile bildirim alalım.
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="ns-meta-label block mb-1.5">
                                            E-posta
                                            <span class="text-base-content/40 font-normal">(opsiyonel)</span>
                                        </label>
                                        <input type="email" class="input input-bordered w-full" placeholder="ornek@mail.com"
                                            autocomplete="email" x-model="email">
                                    </div>
                                    <div>
                                        <label class="ns-meta-label block mb-1.5">
                                            Telefon
                                            <span class="text-base-content/40 font-normal">(opsiyonel)</span>
                                        </label>
                                        <input type="tel" class="input input-bordered w-full"
                                            inputmode="tel" autocomplete="tel" x-model="phone"
                                            @input="maskPhone($event.target); phone = $event.target.value"
                                            @focus="maskPhone($event.target)">
                                    </div>
                                </div>

                                <p class="mt-2 text-xs text-base-content/45">
                                    En az birini doldurun. İkisini de girebilirsiniz.
                                </p>

                                <div class="mt-4">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" x-model="kvkk"
                                            class="mt-1 rounded border-base-300 text-primary focus:ring-primary/40">
                                        <span class="text-xs text-base-content/60 leading-relaxed">
                                            <a href="{{ url('/kvkk-aydinlatma-metni') }}"
                                                class="underline underline-offset-2">KVKK aydınlatma metnini</a>
                                            okudum, bilgilerimin işlenmesine izin veriyorum.
                                        </span>
                                    </label>
                                </div>

                                <template x-if="reminderError">
                                    <p class="mt-3 text-xs text-error" x-text="reminderError"></p>
                                </template>

                                <button type="button" class="btn btn-outline mt-5 w-full" :disabled="reminderLoading"
                                    @click="submitReminder()">
                                    <span x-show="!reminderLoading">Hatırlatıcı kur</span>
                                    <span x-show="reminderLoading" class="flex items-center gap-2" x-cloak>
                                        <span class="loading loading-spinner loading-xs"></span>
                                        Kaydediliyor…
                                    </span>
                                </button>

                                <p class="mt-3 text-[11px] text-base-content/40 leading-relaxed">
                                    Bilgileriniz yalnızca hatırlatma amacıyla kullanılır.
                                    <a href="{{ url('/gizlilik-politikasi') }}"
                                        class="underline underline-offset-2">Gizlilik politikası</a>
                                </p>
                            </div>
                        </template>
                    </div>

                </div>

                {{-- ── Sağ: İpucu ── --}}
                <aside class="lg:col-span-5 space-y-4">
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

                    <div class="ns-surface p-6">
                        <div class="ns-section-eyebrow">Bilgi</div>
                        <h3 class="mt-2 text-base font-semibold">Cayma bedeli nedir?</h3>
                        <p class="mt-2 text-sm text-base-content/70 leading-relaxed">
                            Taahhüt süresi dolmadan sözleşmeyi feshettiğinizde operatörün talep ettiği
                            bedeldir. Genellikle kalan ay sayısı × aylık ücret formülüyle hesaplanır.
                        </p>
                    </div>
                </aside>

            </div>
        </div>
    </section>

    {{-- ===== FAQ Section ===== --}}
    @if($faqs->isNotEmpty())
        <section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="ns-surface rounded-xl p-6 sm:p-8">
                <h2 class="text-xl font-bold mb-6">Sıkça sorulan sorular</h2>

                <div class="space-y-4">
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
            </div>
        </section>
    @endif
@endsection