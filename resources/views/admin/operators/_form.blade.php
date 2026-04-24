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

        {{-- Logo --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Marka logosu</label>

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
