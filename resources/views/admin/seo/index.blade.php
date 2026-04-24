@extends('layouts.admin')

@section('header')
    SEO Yönetimi
@endsection

@section('content')
<div class="space-y-4">

    {{-- Footer Disclaimer hızlı erişim --}}
    @php
        $disclaimer = $seoContents->firstWhere('page_key', 'footer_disclaimer');
    @endphp
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between gap-4">
        <div class="text-sm text-blue-800">
            <strong>Footer Disclaimer:</strong>
            Sitede footer'da gösterilen sorumluluk reddi metni.
            <code class="bg-blue-100 px-1 rounded text-xs ml-1">page_key: footer_disclaimer</code>
        </div>
        @if($disclaimer)
            <a href="{{ route('admin.seo.edit', $disclaimer) }}"
               class="shrink-0 bg-blue-600 text-white px-3 py-1.5 rounded text-sm hover:bg-blue-700">
                Düzenle
            </a>
        @else
            <a href="{{ route('admin.seo.create') }}"
               class="shrink-0 bg-blue-600 text-white px-3 py-1.5 rounded text-sm hover:bg-blue-700">
                Oluştur
            </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">SEO İçerik Listesi</h3>
            <a href="{{ route('admin.seo.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Yeni İçerik</a>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sayfa Anahtarı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meta Başlık</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($seoContents as $seo)
                    <tr class="{{ $seo->page_key === 'footer_disclaimer' ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">
                            {{ $seo->page_key }}
                            @if($seo->page_key === 'footer_disclaimer')
                                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-700">Footer</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $seo->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seo->meta_title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.seo.edit', $seo) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Düzenle</a>
                            <form action="{{ route('admin.seo.destroy', $seo) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $seoContents->links() }}
        </div>
    </div>
</div>
@endsection
