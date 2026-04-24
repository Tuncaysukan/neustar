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

        {{-- SSS --}}
        <div class="border-t pt-6 pb-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-base font-bold">Sıkça Sorulan Sorular (SSS)</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Bu sayfaya özel SSS'ler. Boş bırakırsanız genel SSS'ler gösterilir.</p>
                </div>
                <button type="button" id="addFaq"
                        class="bg-green-600 text-white px-3 py-1.5 rounded text-sm hover:bg-green-700">
                    + Soru Ekle
                </button>
            </div>
            <div id="faqList" class="space-y-3">
                <p class="text-sm text-gray-400 italic" id="emptyMsg">Henüz SSS eklenmedi.</p>
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

<script>
(function () {
    let idx = 0;
    const list   = document.getElementById('faqList');
    const addBtn = document.getElementById('addFaq');
    const empty  = document.getElementById('emptyMsg');

    function rowHtml(i) {
        return `<div class="faq-row border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="flex items-start gap-3">
                <div class="flex-1 space-y-2">
                    <input type="text" name="faqs[${i}][question]" placeholder="Soru..."
                           class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    <textarea name="faqs[${i}][answer]" rows="3" placeholder="Cevap..."
                              class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <button type="button" class="remove-faq text-red-500 hover:text-red-700 mt-1 shrink-0" title="Kaldır">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>`;
    }

    addBtn.addEventListener('click', function () {
        if (empty) empty.style.display = 'none';
        const div = document.createElement('div');
        div.innerHTML = rowHtml(idx++);
        list.appendChild(div.firstElementChild);
        list.lastElementChild.querySelector('input').focus();
    });

    list.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-faq');
        if (!btn) return;
        btn.closest('.faq-row').remove();
        if (empty && !list.querySelector('.faq-row')) empty.style.display = '';
    });
})();
</script>
@endpush
@endsection
