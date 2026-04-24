@php
    $p = $package ?? null;
@endphp

<div class="bg-white rounded-lg shadow overflow-hidden p-6">
    <form action="{{ $action }}" method="POST" class="space-y-6">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        {{-- Operator + Ad --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Operatör <span class="text-red-500">*</span></label>
                <select name="operator_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">— Seçin —</option>
                    @foreach($operators as $operator)
                        <option value="{{ $operator->id }}"
                            {{ old('operator_id', $p->operator_id ?? '') == $operator->id ? 'selected' : '' }}>
                            {{ $operator->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Paket Adı <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $p->name ?? '') }}" required
                       placeholder="Örn. 1.000 Mbps Fiber GigaHome"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        {{-- Fiyat + Hız --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Fiyat (TL) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $p->price ?? '') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Hız (Mbps) <span class="text-red-500">*</span></label>
                <input type="number" name="speed" value="{{ old('speed', $p->speed ?? '') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Upload (Mbps)</label>
                <input type="number" name="upload_speed" value="{{ old('upload_speed', $p->upload_speed ?? '') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Taahhüt (Ay) <span class="text-red-500">*</span></label>
                <input type="number" name="commitment_period" value="{{ old('commitment_period', $p->commitment_period ?? 0) }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500">Taahhütsüz için 0 bırakın.</p>
            </div>
        </div>

        {{-- Kota + Altyapı --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Kota <span class="text-red-500">*</span></label>
                <input type="text" name="quota" value="{{ old('quota', $p->quota ?? 'Sınırsız') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Altyapı Tipi</label>
                <select name="infrastructure_type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @php $infra = old('infrastructure_type', $p->infrastructure_type ?? ''); @endphp
                    <option value="" {{ $infra === '' ? 'selected' : '' }}>— Seçin —</option>
                    @foreach(['Fiber', 'VDSL', 'ADSL', 'Kablo', '5G', '4.5G'] as $type)
                        <option value="{{ $type }}" {{ $infra === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Modem --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Modem</label>
            <select name="modem_included" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="" @selected(!($p->modem_included ?? ''))>Belirtilmemiş</option>
                <option value="free" @selected(($p->modem_included ?? '') === 'free')>Ücretsiz</option>
                <option value="paid" @selected(($p->modem_included ?? '') === 'paid')>Ücretli</option>
            </select>
        </div>

        {{-- Başvuru Tipi ve Detayları --}}
        <div class="border-t pt-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-4">Başvuru Tipi ve Detayları</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Başvuru Tipi <span class="text-red-500">*</span></label>
                    <select name="apply_type" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="form" @selected(old('apply_type', $p->apply_type ?? 'form') === 'form')>Form Doldurma</option>
                        <option value="site" @selected(old('apply_type', $p->apply_type ?? '') === 'site')>Sağlayıcının Sitesine Gönder</option>
                        <option value="call" @selected(old('apply_type', $p->apply_type ?? '') === 'call')>Hemen Ara</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sağlayıcı URL (Dış Link)</label>
                    <input type="url" name="external_url" value="{{ old('external_url', $p->external_url ?? '') }}"
                           placeholder="https://..."
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Arama Numarası</label>
                    <input type="text" name="call_number" value="{{ old('call_number', $p->call_number ?? '') }}"
                           placeholder="05..."
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Seçilen tipe göre kullanıcı ilgili aksiyona yönlendirilir.</p>
        </div>

        {{-- Tarife Hakkında --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Tarife Hakkında
                <span class="text-gray-400 font-normal text-xs ml-1">(paket detay sayfasında gösterilir)</span>
            </label>
            <textarea name="description" rows="6"
                      placeholder="Örn: TurkNet altyapısı ile 1.000 Mbps eşit indirme ve yükleme hızlarıyla taahhütsüz ve gerçek sınırsız internet..."
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $p->description ?? '') }}</textarea>
        </div>

        {{-- Artılar / Eksiler --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Artılar
                    <span class="text-gray-400 font-normal text-xs ml-1">(her satır ayrı madde)</span>
                </label>
                <textarea name="advantages" rows="5"
                          placeholder="İlk 3 ay sabit 349 TL avantajı&#10;100 Mbps yüksek hızda internet&#10;Taahhütsüz kullanım"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('advantages', $p->advantages ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Eksiler
                    <span class="text-gray-400 font-normal text-xs ml-1">(her satır ayrı madde)</span>
                </label>
                <textarea name="disadvantages" rows="5"
                          placeholder="12 ay taahhüt şartı&#10;Cayma durumunda indirim iadesi"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('disadvantages', $p->disadvantages ?? '') }}</textarea>
            </div>
        </div>

        {{-- SEO --}}
        <div class="border-t pt-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-4">SEO (opsiyonel)</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">SEO Başlığı</label>
                    <input type="text" name="seo_title" value="{{ old('seo_title', $p->seo_title ?? '') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">SEO Açıklaması</label>
                    <textarea name="seo_description" rows="2"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('seo_description', $p->seo_description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Durum + Sponsor (vurgulu kutu) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $p->is_active ?? true) ? 'checked' : '' }}
                           class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <div>
                        <span class="block text-sm font-semibold text-gray-800">Aktif</span>
                        <span class="block text-xs text-gray-500">Kapalıysa sitede listelenmez.</span>
                    </div>
                </label>
            </div>

            <div class="rounded-lg border-2 border-blue-200 bg-blue-50 p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="is_sponsored" value="0">
                    <input type="checkbox" name="is_sponsored" value="1"
                           {{ old('is_sponsored', $p->is_sponsored ?? false) ? 'checked' : '' }}
                           class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <div>
                        <span class="block text-sm font-semibold text-blue-900">
                            Sponsor Sağlayıcı
                            <span class="ml-2 inline-flex items-center gap-1 rounded-full bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5">
                                Sponsor
                            </span>
                        </span>
                        <span class="block text-xs text-blue-700/80 mt-1">
                            İşaretlenirse paket listelerin en üstünde "Sponsor Sağlayıcı" rozetiyle vurgulanır
                            ve anasayfa "Günün Fırsatları" bölümünde gösterilir.
                        </span>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-4 border-t">
            <a href="{{ route('admin.packages.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">İptal</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ $method === 'POST' ? 'Paketi Oluştur' : 'Değişiklikleri Kaydet' }}
            </button>
        </div>
    </form>
</div>
