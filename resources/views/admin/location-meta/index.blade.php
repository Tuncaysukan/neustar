@extends('layouts.admin')

@section('header')
    Meta Şablon Yönetimi
@endsection

@section('content')
<div class="space-y-4">

    {{-- Açıklama kutusu --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
        <strong>Nasıl çalışır?</strong>
        Şablon içinde <code class="bg-blue-100 px-1 rounded">{il}</code>,
        <code class="bg-blue-100 px-1 rounded">{ilce}</code>,
        <code class="bg-blue-100 px-1 rounded">{il_seo}</code>,
        <code class="bg-blue-100 px-1 rounded">{ilce_seo}</code> placeholder'larını kullanın.
        "Tüm Sayfalara Uygula" butonu şablonu Türkiye'deki tüm il veya ilçe sayfalarına tek seferde yazar.
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="text-lg font-bold">Şablonlar</h3>
            <a href="{{ route('admin.location-meta.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                + Yeni Şablon
            </a>
        </div>

        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şablon Adı</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tür</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meta Başlık Şablonu</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Varsayılan</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($templates as $tpl)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium">{{ $tpl->name }}</td>
                    <td class="px-5 py-3">
                        @if($tpl->type === 'district')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">İlçe</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">İl</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-600 max-w-xs truncate font-mono text-xs">
                        {{ $tpl->meta_title_tpl ?: '—' }}
                    </td>
                    <td class="px-5 py-3">
                        @if($tpl->is_default)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">✓ Varsayılan</span>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('admin.location-meta.edit', $tpl) }}"
                               class="text-indigo-600 hover:text-indigo-900 text-sm">Düzenle</a>

                            {{-- Tek tıkla uygula --}}
                            <form action="{{ route('admin.location-meta.apply', $tpl) }}" method="POST"
                                  onsubmit="return confirm('Bu şablon {{ $tpl->type === 'district' ? 'tüm ilçe' : 'tüm il' }} sayfalarına uygulanacak. Mevcut içerikler üzerine yazılacak. Devam edilsin mi?')">
                                @csrf
                                <button type="submit"
                                        class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                                    ⚡ Tüm {{ $tpl->type === 'district' ? 'İlçe' : 'İl' }} Sayfalarına Uygula
                                </button>
                            </form>

                            <form action="{{ route('admin.location-meta.destroy', $tpl) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900 text-sm"
                                        onclick="return confirm('Şablonu silmek istediğinize emin misiniz?')">
                                    Sil
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                        Henüz şablon eklenmemiş.
                        <a href="{{ route('admin.location-meta.create') }}" class="text-blue-600 hover:underline ml-1">İlk şablonu oluştur →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
