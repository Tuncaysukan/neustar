@extends('layouts.admin')

@section('header')
    Tarife SEO Yönetimi
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden">

        {{-- Toolbar --}}
        <div class="p-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h3 class="text-lg font-bold">İl / İlçe Tarife SEO İçerikleri</h3>
            </div>
            <div class="flex items-center gap-2">
                {{-- Arama --}}
                <form method="GET" action="{{ route('admin.tariff-seo.index') }}" class="flex gap-2">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="İl veya ilçe ara..."
                           class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <select name="type" onchange="this.form.submit()"
                            class="border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                        <option value="">Tümü</option>
                        <option value="city"     {{ request('type') === 'city'     ? 'selected' : '' }}>Sadece İller</option>
                        <option value="district" {{ request('type') === 'district' ? 'selected' : '' }}>Sadece İlçeler</option>
                    </select>
                </form>
                <a href="{{ route('admin.tariff-seo.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm whitespace-nowrap">
                    + Yeni Ekle
                </a>
            </div>
        </div>

        {{-- Tablo --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İl / İlçe</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">H1 Başlığı</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sayfa URL</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        @php
                            use App\Models\TariffSeoContent;
                            $isDistrict = (bool) $item->district_slug;
                            if ($isDistrict) {
                                $pageUrl = route('tariffs.district', [
                                    'citySlug' => $item->city_slug,
                                    'urlSlug'  => TariffSeoContent::districtUrlSlug($item->district_slug),
                                ]);
                            } else {
                                $pageUrl = route('tariffs.city', [
                                    'urlSlug' => TariffSeoContent::cityUrlSlug($item->city_slug),
                                ]);
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 whitespace-nowrap">
                                @if($isDistrict)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">İlçe</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">İl</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap font-medium">
                                {{ $item->city_name }}
                                @if($isDistrict)
                                    <span class="text-gray-400 mx-1">/</span>
                                    {{ $item->district_name }}
                                @endif
                            </td>
                            <td class="px-5 py-3 max-w-xs truncate text-gray-600">
                                {{ $item->h1_title ?: $item->resolvedH1() }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <a href="{{ $pageUrl }}" target="_blank"
                                   class="text-blue-600 hover:underline text-xs font-mono truncate max-w-[200px] inline-block">
                                    {{ $pageUrl }}
                                </a>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.tariff-seo.edit', $item) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-3">Düzenle</a>
                                <form action="{{ route('admin.tariff-seo.destroy', $item) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Bu SEO içeriğini silmek istediğinize emin misiniz?')">
                                        Sil
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                                Henüz tarife SEO içeriği eklenmemiş.
                                <a href="{{ route('admin.tariff-seo.create') }}" class="text-blue-600 hover:underline ml-1">İlk içeriği ekle →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t">
            {{ $items->links() }}
        </div>
    </div>
@endsection
