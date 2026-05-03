@extends('layouts.admin')

@section('header')
    {{ isset($seo) ? 'SEO İçeriğini Düzenle' : 'Yeni SEO İçeriği' }}
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form action="{{ isset($seo) ? route('admin.seo.update', $seo) : route('admin.seo.store') }}" method="POST">
            @csrf
            @if(isset($seo))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sayfa anahtarı</label>
                        <input type="text" name="page_key" value="{{ old('page_key', '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <p class="mt-1 text-xs text-gray-500">
                            Laravel <strong>route adı</strong> kullanın (örn. <code class="bg-gray-100 px-1 rounded">home</code>, <code class="bg-gray-100 px-1 rounded">packages.index</code>, <code class="bg-gray-100 px-1 rounded">contact</code>).
                            Paket listesinin tüm URL varyantları için yalnızca <code class="bg-gray-100 px-1 rounded">packages.index</code> kaydı yeterlidir.
                            Footer metni için: <code class="bg-gray-100 px-1 rounded">footer_disclaimer</code> (sayfa üst bloğunda gösterilmez).
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Görünür Başlık (H1)</label>
                        <input type="text" name="title" value="{{ old('title', '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Sayfa İçi İçerik (SEO Metni / Disclaimer)</label>
                    <textarea name="content" rows="6" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('content', '') }}</textarea>
                </div>

                <div class="border-t pt-6">
                    <h4 class="text-md font-bold mb-4">Meta Etiketleri</h4>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Başlık (Title Tag)</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Açıklama (Description)</label>
                            <textarea name="meta_description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('meta_description', '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.seo.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2 hover:bg-gray-300">İptal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kaydet</button>
            </div>
        </form>
    </div>
@endsection
