@extends('layouts.admin')

@section('header')
    İletişim Mesajları
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <p class="text-xs text-gray-500 mb-3">
                <code>/iletisim</code> formundan gelen mesajlar.
            </p>
            <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="flex flex-wrap items-end gap-2">
                <input type="search" name="q" value="{{ $q }}"
                       placeholder="Ad, e-posta, konu, metin ara"
                       class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 flex-1 min-w-[180px]">
                <div class="flex items-center gap-2">
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <span class="text-gray-400 text-xs">—</span>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Filtrele</button>
                @if($q || $dateFrom || $dateTo)
                    <a href="{{ route('admin.contact-messages.index') }}" class="px-3 py-2 rounded text-sm bg-gray-100 text-gray-600 hover:bg-gray-200">Temizle</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Tarih</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Gönderen</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Konu</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-600"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($messages as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap text-gray-600">{{ $m->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <div class="font-medium text-gray-900">{{ $m->name }}</div>
                            <a href="mailto:{{ $m->email }}" class="text-blue-600 hover:underline text-xs">{{ $m->email }}</a>
                        </td>
                        <td class="px-4 py-2 text-gray-700 max-w-xs truncate">{{ $m->subject ?: '—' }}</td>
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            <a href="{{ route('admin.contact-messages.show', $m) }}" class="text-blue-600 hover:underline mr-3">Aç</a>
                            <form action="{{ route('admin.contact-messages.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Bu mesajı silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">Kayıt bulunamadı.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($messages->hasPages())
            <div class="p-4 border-t">{{ $messages->links() }}</div>
        @endif
    </div>
@endsection
