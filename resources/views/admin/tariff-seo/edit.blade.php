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
            <div>
                <span class="text-gray-500">İl:</span>
                <span class="font-semibold ml-1">{{ $item->city_name }}</span>
            </div>
            @if($item->district_name)
            <div>
                <span class="text-gray-500">İlçe:</span>
                <span class="font-semibold ml-1">{{ $item->district_name }}</span>
            </div>
            @endif
            <div>
                <span class="text-gray-500">Sayfa URL:</span>
                @php
                    use App\Models\TariffSeoContent;
                    $pageUrl = $item->district_slug
                        ? route('tariffs.district', ['citySlug' => $item->city_slug, 'urlSlug' => TariffSeoContent::districtUrlSlug($item->district_slug)])
                        : route('tariffs.city', ['urlSlug' => TariffSeoContent::cityUrlSlug($item->city_slug)]);
                @endphp
                <a href="{{ $pageUrl }}" target="_blank"
                   class="ml-1 text-blue-600 hover:underline font-mono text-xs">
                    {{ $pageUrl }}
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.tariff-seo.update', $item) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- İçerik --}}
        <div class="border-b pb-6 mb-6">
            <h4 class="text-base font-bold mb-4">Sayfa İçeriği</h4>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        H1 Başlığı
                        <span class="text-gray-400 text-xs">(boş bırakırsanız otomatik: "{{ $item->resolvedH1() }}")</span>
                    </label>
                    <input type="text" name="h1_title" value="{{ old('h1_title', $item->h1_title) }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Giriş Metni
                        <span class="text-gray-400 text-xs">(boş bırakırsanız otomatik: "{{ $item->resolvedIntro() }}")</span>
                    </label>
                    <textarea name="intro_text" rows="3"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('intro_text', $item->intro_text) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Alt SEO Metni
                        <span class="text-gray-400 text-xs">(sayfa altında gösterilir — bölge/mahalle bilgilendirmesi)</span>
                    </label>
                    <textarea name="seo_footer_text" rows="8"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('seo_footer_text', $item->seo_footer_text) }}</textarea>
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
                    </label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $item->meta_title) }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Açıklama (Description)</label>
                    <textarea name="meta_description" rows="3"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('meta_description', $item->meta_description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.tariff-seo.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">İptal</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Güncelle
            </button>
        </div>
    </form>
</div>
@endsection
