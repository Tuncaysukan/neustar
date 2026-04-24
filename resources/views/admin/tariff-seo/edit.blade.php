@extends('layouts.admin')

@section('header')
    Tarife SEO Düzenle —
    {{ $item->city_name }}{{ $item->district_name ? ' / ' . $item->district_name : '' }}
@endsection

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden p-6">

    {{-- Konum bilgisi (salt okunur) --}}
    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="flex flex-wrap items-center gap-4 text-sm">
            <div><span class="text-gray-500">İl:</span> <span class="font-semibold">{{ $item->city_name }}</span></div>
            @if($item->district_name)
            <div><span class="text-gray-500">İlçe:</span> <span class="font-semibold">{{ $item->district_name }}</span></div>
            @endif
            <div>
                <span class="text-gray-500">Sayfa URL:</span>
                @php
                    use App\Models\TariffSeoContent;
                    $pageUrl = $item->district_slug
                        ? route('tariffs.district', ['citySlug' => $item->city_slug, 'urlSlug' => TariffSeoContent::districtUrlSlug($item->district_slug)])
                        : route('tariffs.city', ['urlSlug' => TariffSeoContent::cityUrlSlug($item->city_slug)]);
                    // Altyapı sorgulama URL'si
                    $infraUrl = $item->district_slug
                        ? url('/internet-altyapi/' . $item->city_slug . '/' . $item->district_slug)
                        : url('/internet-altyapi/' . $item->city_slug);
                @endphp
                <a href="{{ $pageUrl }}" target="_blank" class="ml-1 text-blue-600 hover:underline font-mono text-xs">Tarife sayfası ↗</a>
                <a href="{{ $infraUrl }}" target="_blank" class="ml-3 text-purple-600 hover:underline font-mono text-xs">Altyapı sayfası ↗</a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.tariff-seo.update', $item) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ── 1. Meta Etiketleri ── --}}
        <div class="border-b pb-6 mb-6">
            <h4 class="text-base font-bold mb-4">Meta Etiketleri</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Başlık (Title Tag)</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $item->meta_title) }}"
                           placeholder="{{ $item->city_name }}{{ $item->district_name ? ' ' . $item->district_name : '' }} İnternet Altyapı Sorgulama | Neustar"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Önerilen: 50–60 karakter</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Açıklama (Description)</label>
                    <textarea name="meta_description" rows="3"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('meta_description', $item->meta_description) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Önerilen: 150–160 karakter</p>
                </div>
            </div>
        </div>

        {{-- ── 2. Sayfa İçeriği ── --}}
        <div class="border-b pb-6 mb-6">
            <h4 class="text-base font-bold mb-4">Sayfa İçeriği</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        H1 Başlığı
                        <span class="text-gray-400 text-xs">(boş = otomatik: "{{ $item->resolvedH1() }}")</span>
                    </label>
                    <input type="text" name="h1_title" value="{{ old('h1_title', $item->h1_title) }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Giriş Metni
                        <span class="text-gray-400 text-xs">(boş = otomatik: "{{ Str::limit($item->resolvedIntro(), 80) }}")</span>
                    </label>
                    <textarea name="intro_text" rows="3"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('intro_text', $item->intro_text) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Alt SEO Metni
                        <span class="text-gray-400 text-xs">(sayfa altında gösterilir — bölge/mahalle bilgilendirmesi)</span>
                    </label>
                    <textarea name="seo_footer_text" rows="7"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('seo_footer_text', $item->seo_footer_text) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── 3. SSS Yönetimi ── --}}
        <div class="pb-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-base font-bold">Sıkça Sorulan Sorular (SSS)</h4>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Bu sayfaya özel SSS'ler. Boş bırakırsanız genel SSS'ler gösterilir.
                    </p>
                </div>
                <button type="button" id="addFaq"
                        class="bg-green-600 text-white px-3 py-1.5 rounded text-sm hover:bg-green-700">
                    + Soru Ekle
                </button>
            </div>

            <div id="faqList" class="space-y-3">
                @php $existingFaqs = old('faqs', $item->faqs ?? []); @endphp
                @forelse($existingFaqs as $i => $faq)
                <div class="faq-row border border-gray-200 rounded-lg p-4 bg-gray-50" data-index="{{ $i }}">
                    <div class="flex items-start gap-3">
                        <div class="flex-1 space-y-2">
                            <input type="text"
                                   name="faqs[{{ $i }}][question]"
                                   value="{{ $faq['question'] ?? '' }}"
                                   placeholder="Soru..."
                                   class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                            <textarea name="faqs[{{ $i }}][answer]"
                                      rows="3"
                                      placeholder="Cevap..."
                                      class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">{{ $faq['answer'] ?? '' }}</textarea>
                        </div>
                        <button type="button" class="remove-faq text-red-500 hover:text-red-700 mt-1 shrink-0" title="Kaldır">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 italic" id="emptyMsg">Henüz SSS eklenmedi. "Soru Ekle" ile başlayın.</p>
                @endforelse
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.tariff-seo.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">İptal</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Kaydet
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    let idx = {{ count($existingFaqs ?? []) }};

    const list     = document.getElementById('faqList');
    const addBtn   = document.getElementById('addFaq');
    const emptyMsg = document.getElementById('emptyMsg');

    function hideEmpty() {
        if (emptyMsg) emptyMsg.style.display = 'none';
    }

    function showEmptyIfNeeded() {
        if (!emptyMsg) return;
        const rows = list.querySelectorAll('.faq-row');
        emptyMsg.style.display = rows.length === 0 ? '' : 'none';
    }

    addBtn.addEventListener('click', function () {
        hideEmpty();
        const div = document.createElement('div');
        div.className = 'faq-row border border-gray-200 rounded-lg p-4 bg-gray-50';
        div.dataset.index = idx;
        div.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="flex-1 space-y-2">
                    <input type="text"
                           name="faqs[${idx}][question]"
                           placeholder="Soru..."
                           class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    <textarea name="faqs[${idx}][answer]"
                              rows="3"
                              placeholder="Cevap..."
                              class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <button type="button" class="remove-faq text-red-500 hover:text-red-700 mt-1 shrink-0" title="Kaldır">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>`;
        list.appendChild(div);
        div.querySelector('input').focus();
        idx++;
    });

    list.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-faq');
        if (!btn) return;
        btn.closest('.faq-row').remove();
        showEmptyIfNeeded();
    });
})();
</script>
@endpush
