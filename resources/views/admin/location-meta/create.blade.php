@extends('layouts.admin')

@section('header')
    {{ isset($template) ? 'Şablonu Düzenle' : 'Yeni Meta Şablonu' }}
@endsection

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden p-6">

    {{-- Placeholder rehberi --}}
    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm">
        <p class="font-semibold text-yellow-800 mb-2">Kullanılabilir placeholder'lar:</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            @foreach([
                '{il}'       => 'İl adı (İstanbul)',
                '{ilce}'     => 'İlçe adı (Kadıköy)',
                '{il_seo}'   => 'İl slug (istanbul)',
                '{ilce_seo}' => 'İlçe slug (kadikoy)',
            ] as $ph => $desc)
            <div class="bg-white border border-yellow-200 rounded px-2 py-1.5">
                <code class="text-xs font-mono text-yellow-700">{{ $ph }}</code>
                <p class="text-xs text-gray-500 mt-0.5">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <form action="{{ isset($template) ? route('admin.location-meta.update', $template) : route('admin.location-meta.store') }}"
          method="POST" id="tplForm">
        @csrf
        @if(isset($template)) @method('PUT') @endif

        <div class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Şablon Adı <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $template->name ?? '') }}"
                           placeholder="Örn: Standart İlçe Şablonu"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                @if(!isset($template))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tür</label>
                    <select name="type" id="typeSelect"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="district" {{ old('type') === 'district' ? 'selected' : '' }}>İlçe sayfaları</option>
                        <option value="city"     {{ old('type') === 'city'     ? 'selected' : '' }}>İl sayfaları</option>
                    </select>
                </div>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Meta Başlık (Title Tag) Şablonu
                </label>
                <input type="text" name="meta_title_tpl"
                       value="{{ old('meta_title_tpl', $template->meta_title_tpl ?? '') }}"
                       placeholder="{il} {ilce} Ev İnterneti Altyapı Sorgulama ve Paketleri | Neustar"
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-400">Önerilen: 50–60 karakter</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Meta Açıklama Şablonu
                </label>
                <textarea name="meta_description_tpl" rows="3"
                          placeholder="{il} {ilce} için fiber, VDSL ve ADSL altyapı sorgulaması. {ilce} internet paketlerini karşılaştırın."
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('meta_description_tpl', $template->meta_description_tpl ?? '') }}</textarea>
                <p class="mt-1 text-xs text-gray-400">Önerilen: 150–160 karakter</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">H1 Başlık Şablonu</label>
                <input type="text" name="h1_tpl"
                       value="{{ old('h1_tpl', $template->h1_tpl ?? '') }}"
                       placeholder="{il} {ilce} Ev İnternet Kampanyaları ve Altyapı Sorgulama"
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Giriş Metni Şablonu</label>
                <textarea name="intro_tpl" rows="3"
                          placeholder="{il} {ilce} için güncel internet altyapı durumunu sorgulayın ve en uygun tarifeleri karşılaştırın."
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('intro_tpl', $template->intro_tpl ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    SEO Alt Metin Şablonu
                    <span class="text-gray-400 font-normal text-xs">(sayfa altında gösterilir)</span>
                </label>
                <textarea name="seo_footer_tpl" rows="6"
                          placeholder="{il} {ilce} bölgesinde fiber internet altyapısı hızla gelişmektedir. {ilce} mahallelerinde Türk Telekom, Superonline ve diğer operatörlerin fiber altyapısı mevcuttur."
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('seo_footer_tpl', $template->seo_footer_tpl ?? '') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_default" value="0">
                    <input type="checkbox" name="is_default" value="1"
                           {{ old('is_default', $template->is_default ?? false) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span class="text-sm text-gray-700">
                        Varsayılan şablon olarak işaretle
                        <span class="text-gray-400 text-xs">(sayfaya özel kayıt yoksa bu şablon kullanılır)</span>
                    </span>
                </label>
            </div>

        </div>

        {{-- Önizleme --}}
        <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg" id="preview">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Önizleme (İstanbul / Kadıköy)</p>
            <dl class="space-y-2 text-sm">
                <div class="flex gap-2">
                    <dt class="text-gray-500 w-28 shrink-0">Meta Başlık:</dt>
                    <dd class="font-medium text-gray-800" id="prev-title">—</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="text-gray-500 w-28 shrink-0">Meta Açıklama:</dt>
                    <dd class="text-gray-700" id="prev-desc">—</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="text-gray-500 w-28 shrink-0">H1:</dt>
                    <dd class="font-semibold text-gray-800" id="prev-h1">—</dd>
                </div>
            </dl>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.location-meta.index') }}"
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
    const fields = {
        title: document.querySelector('[name=meta_title_tpl]'),
        desc:  document.querySelector('[name=meta_description_tpl]'),
        h1:    document.querySelector('[name=h1_tpl]'),
    };
    const prevs = {
        title: document.getElementById('prev-title'),
        desc:  document.getElementById('prev-desc'),
        h1:    document.getElementById('prev-h1'),
    };

    const sample = {
        '{il}': 'İstanbul', '{ilce}': 'Kadıköy',
        '{il_seo}': 'istanbul', '{ilce_seo}': 'kadikoy',
    };

    function render(tpl) {
        if (!tpl) return '—';
        return tpl.replace(/\{il\}|\{ilce\}|\{il_seo\}|\{ilce_seo\}/g, m => sample[m] || m);
    }

    function update() {
        prevs.title.textContent = render(fields.title?.value);
        prevs.desc.textContent  = render(fields.desc?.value);
        prevs.h1.textContent    = render(fields.h1?.value);
    }

    Object.values(fields).forEach(f => f?.addEventListener('input', update));
    update();
})();
</script>
@endpush
