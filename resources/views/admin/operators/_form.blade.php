@php($o = $operator ?? null)

<form action="{{ $o ? route('admin.operators.update', $o) : route('admin.operators.store') }}"
      method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($o) @method('PUT') @endif

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Operatör adı</label>
            <input type="text" name="name"
                   value="{{ old('name', $o->name ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   required>
        </div>

        {{-- Website --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Web sitesi URL</label>
            <input type="url" name="website_url"
                   value="{{ old('website_url', $o->website_url ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="https://">
        </div>

        {{-- Description --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Açıklama</label>
            <textarea name="description" rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $o->description ?? '') }}</textarea>
        </div>

        {{-- SEO Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">SEO Başlık</label>
            <input type="text" name="seo_title"
                   value="{{ old('seo_title', $o->seo_title ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- SEO Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">SEO Açıklama</label>
            <input type="text" name="seo_description"
                   value="{{ old('seo_description', $o->seo_description ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- SEO Text --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">SEO Metni (Sayfa altında gösterilir)</label>
            <textarea name="seo_text" rows="6"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('seo_text', $o->seo_text ?? '') }}</textarea>
        </div>

        {{-- Logo --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Ana logo</label>

            <div class="mt-2 flex items-center gap-4">
                <div class="shrink-0">
                    @if($o && $o->logo)
                        <img src="{{ $o->logo_url }}" alt="{{ $o->name }} logo"
                             class="h-16 w-16 rounded-md object-contain bg-white border border-gray-200 p-1">
                    @else
                        <div class="h-16 w-16 rounded-md bg-gray-100 border border-gray-200 grid place-items-center text-gray-400 text-xl font-semibold">
                            {{ $o->initial ?? '?' }}
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <input type="file" name="logo_file"
                           accept="image/png,image/jpeg,image/webp,image/svg+xml"
                           class="block w-full text-sm text-gray-700
                                  file:mr-3 file:py-2 file:px-4 file:rounded-md
                                  file:border-0 file:text-sm file:font-medium
                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">
                        PNG, JPG, WEBP veya SVG. En fazla 512 KB. Tercihen şeffaf arka plan, 256×256+.
                    </p>

                    @if($o && $o->logo)
                        <label class="mt-3 inline-flex items-center text-sm text-gray-600">
                            <input type="checkbox" name="remove_logo" value="1"
                                   class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                            <span class="ml-2">Mevcut logoyu kaldır</span>
                        </label>
                    @endif
                </div>
            </div>
        </div>

        {{-- Çoklu Logo Galerisi --}}
        @if($o)
        <div class="md:col-span-2 border-t pt-5">
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700">Logo Galerisi</label>
                <span class="text-xs text-gray-400">Açık/koyu tema, favicon gibi farklı versiyonlar ekleyin</span>
            </div>

            {{-- Mevcut logolar --}}
            @if($o->logos->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4" id="logoGallery">
                @foreach($o->logos as $logo)
                <div class="relative border rounded-lg p-2 bg-gray-50 group" id="logo-item-{{ $logo->id }}">
                    {{-- Birincil rozeti --}}
                    @if($logo->is_primary)
                        <span class="absolute top-1 left-1 bg-green-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">Ana</span>
                    @endif

                    <img src="{{ $logo->url }}" alt="{{ $logo->label ?: 'Logo' }}"
                         class="h-14 w-full object-contain rounded">

                    <div class="mt-1.5 text-center">
                        <span class="text-[10px] text-gray-500 block truncate">{{ $logo->label ?: $logo->variant }}</span>
                    </div>

                    <div class="mt-2 flex items-center justify-between gap-1">
                        {{-- Birincil yap --}}
                        @if(!$logo->is_primary)
                        <button type="button"
                                onclick="setPrimary({{ $logo->id }})"
                                class="text-[10px] text-blue-600 hover:underline">
                            Ana yap
                        </button>
                        @else
                        <span class="text-[10px] text-green-600">✓ Ana logo</span>
                        @endif

                        {{-- Sil --}}
                        <button type="button"
                                onclick="markDelete({{ $logo->id }})"
                                class="text-[10px] text-red-500 hover:text-red-700">
                            Sil
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Yeni logo ekle --}}
            <div class="border-2 border-dashed border-gray-200 rounded-lg p-4" id="newLogoArea">
                <p class="text-xs font-semibold text-gray-600 mb-3">Yeni logo ekle</p>

                <div id="newLogoRows" class="space-y-3">
                    <div class="new-logo-row grid grid-cols-1 sm:grid-cols-3 gap-2 items-end">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Dosya</label>
                            <input type="file" name="extra_logos[]"
                                   accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                   class="block w-full text-xs text-gray-700 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Etiket</label>
                            <input type="text" name="extra_logo_labels[]"
                                   placeholder="Açık tema, Koyu tema..."
                                   class="block w-full border-gray-300 rounded text-xs shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Varyant</label>
                            <select name="extra_logo_variants[]"
                                    class="block w-full border-gray-300 rounded text-xs shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="light">Açık tema</option>
                                <option value="dark">Koyu tema</option>
                                <option value="favicon">Favicon</option>
                                <option value="other">Diğer</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="addLogoRow()"
                        class="mt-3 text-xs text-blue-600 hover:underline">
                    + Başka logo ekle
                </button>
            </div>

            {{-- Silinecek logo ID'leri --}}
            <input type="hidden" name="delete_logo_ids" id="deleteLogoIds" value="">
            {{-- Birincil logo ID --}}
            <input type="hidden" name="primary_logo_id" id="primaryLogoId" value="">
        </div>
        @endif

        {{-- Active --}}
        <div class="md:col-span-2">
            <label class="inline-flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $o->is_active ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-600">Aktif</span>
            </label>
        </div>
    </div>

    <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
        <a href="{{ route('admin.operators.index') }}"
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">İptal</a>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            {{ $o ? 'Güncelle' : 'Kaydet' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
function addLogoRow() {
    const container = document.getElementById('newLogoRows');
    const row = container.querySelector('.new-logo-row').cloneNode(true);
    // Input'ları temizle
    row.querySelectorAll('input[type=file], input[type=text]').forEach(el => el.value = '');
    row.querySelectorAll('select').forEach(el => el.selectedIndex = 0);
    container.appendChild(row);
}

const toDelete = new Set();

function markDelete(id) {
    if (!confirm('Bu logoyu silmek istediğinize emin misiniz?')) return;
    toDelete.add(id);
    document.getElementById('deleteLogoIds').value = [...toDelete].join(',');
    const el = document.getElementById('logo-item-' + id);
    if (el) el.style.opacity = '0.3';
}

function setPrimary(id) {
    document.getElementById('primaryLogoId').value = id;
    // Tüm "Ana yap" butonlarını sıfırla
    document.querySelectorAll('[id^="logo-item-"]').forEach(el => {
        const badge = el.querySelector('.bg-green-500');
        if (badge) badge.remove();
    });
    // Seçileni işaretle
    const el = document.getElementById('logo-item-' + id);
    if (el) {
        const badge = document.createElement('span');
        badge.className = 'absolute top-1 left-1 bg-green-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded';
        badge.textContent = 'Ana';
        el.style.position = 'relative';
        el.prepend(badge);
    }
}
</script>
@endpush
