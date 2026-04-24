@extends('layouts.admin')

@section('header')
    Yeni Tarife SEO İçeriği
@endsection

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden p-6">
    <form action="{{ route('admin.tariff-seo.store') }}" method="POST" id="tariffSeoForm">
        @csrf

        {{-- İl / İlçe Seçimi --}}
        <div class="border-b pb-6 mb-6">
            <h4 class="text-base font-bold mb-4">Konum Seçimi</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- İl --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        İl <span class="text-red-500">*</span>
                    </label>
                    <select name="city_slug" id="citySelect" required
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">— İl seçin —</option>
                        @foreach($catalog as $slug => $data)
                            <option value="{{ $slug }}"
                                    data-name="{{ $data['name'] }}"
                                    {{ old('city_slug') === $slug ? 'selected' : '' }}>
                                {{ $data['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_slug')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <input type="hidden" name="city_name" id="cityName" value="{{ old('city_name') }}">
                </div>

                {{-- İlçe (opsiyonel) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        İlçe <span class="text-gray-400 text-xs">(boş bırakırsanız il sayfası oluşturulur)</span>
                    </label>
                    <select name="district_slug" id="districtSelect"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">— Önce il seçin —</option>
                    </select>
                    @error('district_slug')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <input type="hidden" name="district_name" id="districtName" value="{{ old('district_name') }}">
                </div>
            </div>

            {{-- Oluşacak URL önizlemesi --}}
            <div class="mt-4 p-3 bg-gray-50 rounded-md border border-gray-200">
                <p class="text-xs text-gray-500 mb-1">Oluşacak URL:</p>
                <p class="text-sm font-mono text-blue-700" id="urlPreview">—</p>
            </div>
        </div>

        {{-- İçerik --}}
        <div class="border-b pb-6 mb-6">
            <h4 class="text-base font-bold mb-4">Sayfa İçeriği</h4>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        H1 Başlığı
                        <span class="text-gray-400 text-xs">(boş bırakırsanız otomatik üretilir)</span>
                    </label>
                    <input type="text" name="h1_title" value="{{ old('h1_title') }}"
                           placeholder="Örn: İstanbul Ev İnternet Kampanyaları ve Fiyat Karşılaştırma"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Giriş Metni
                        <span class="text-gray-400 text-xs">(boş bırakırsanız otomatik üretilir)</span>
                    </label>
                    <textarea name="intro_text" rows="3"
                              placeholder="Örn: İstanbul'daki ev interneti kullanıcıları için hazırladığımız güncel tarife listesidir."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('intro_text') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Alt SEO Metni
                        <span class="text-gray-400 text-xs">(sayfa altında gösterilir — bölge/mahalle bilgilendirmesi)</span>
                    </label>
                    <textarea name="seo_footer_text" rows="6"
                              placeholder="Örn: Pendik, İstanbul'un Anadolu yakasında yer alan bir ilçedir. Pendik'te fiber altyapı büyük ölçüde yaygınlaşmış olup..."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('seo_footer_text') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Meta Etiketleri --}}
        <div class="pb-6 mb-6">
            <h4 class="text-base font-bold mb-4">Meta Etiketleri</h4>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Meta Başlık (Title Tag)
                        <span class="text-gray-400 text-xs">(boş bırakırsanız H1 kullanılır)</span>
                    </label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Açıklama (Description)</label>
                    <textarea name="meta_description" rows="3"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('meta_description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.tariff-seo.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">İptal</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Kaydet
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const catalog = @json($catalog);
    const citySelect    = document.getElementById('citySelect');
    const districtSelect = document.getElementById('districtSelect');
    const cityNameInput  = document.getElementById('cityName');
    const districtNameInput = document.getElementById('districtName');
    const urlPreview     = document.getElementById('urlPreview');
    const baseUrl        = '{{ url('/internet-tarifeleri') }}';

    function updateDistricts() {
        const slug = citySelect.value;
        const data = catalog[slug];

        districtSelect.innerHTML = '<option value="">— İlçe seçin (opsiyonel) —</option>';
        districtNameInput.value = '';

        if (data) {
            cityNameInput.value = data.name;
            Object.entries(data.districts).forEach(([dSlug, dName]) => {
                const opt = document.createElement('option');
                opt.value = dSlug;
                opt.textContent = dName;
                opt.dataset.name = dName;
                districtSelect.appendChild(opt);
            });
        } else {
            cityNameInput.value = '';
        }
        updatePreview();
    }

    function updatePreview() {
        const cSlug = citySelect.value;
        const dSlug = districtSelect.value;

        if (!cSlug) {
            urlPreview.textContent = '—';
            return;
        }

        if (dSlug) {
            urlPreview.textContent = baseUrl + '/' + cSlug + '/ucuz-' + dSlug + '-ev-interneti-fiyatlari';
        } else {
            urlPreview.textContent = baseUrl + '/ucuz-' + cSlug + '-ev-interneti-fiyatlari';
        }
    }

    districtSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        districtNameInput.value = opt.dataset.name || '';
        updatePreview();
    });

    citySelect.addEventListener('change', updateDistricts);

    // Sayfa yüklendiğinde eski değer varsa ilçeleri doldur
    if (citySelect.value) updateDistricts();
})();
</script>
@endpush
@endsection
