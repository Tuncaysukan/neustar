@extends('layouts.admin')
@section('header') Yorum Moderasyonu @endsection

@section('content')
<div class="space-y-4">

    {{-- Filtre + toplu onay --}}
    <div class="bg-white rounded-lg shadow p-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reviews.index') }}"
               class="px-3 py-1.5 rounded text-sm {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Tümü
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"
               class="px-3 py-1.5 rounded text-sm {{ request('status') === 'pending' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Bekleyenler
                @if($pendingCount > 0)
                    <span class="ml-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
               class="px-3 py-1.5 rounded text-sm {{ request('status') === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Onaylılar
            </a>
        </div>

        @if($pendingCount > 0)
        <form action="{{ route('admin.reviews.bulk-approve') }}" method="POST">
            @csrf
            <button type="submit"
                    onclick="return confirm('{{ $pendingCount }} bekleyen yorumun tamamını onaylamak istediğinize emin misiniz?')"
                    class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                ✓ Tümünü Onayla ({{ $pendingCount }})
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanıcı / Paket</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yorum</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Puan</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviews as $review)
                <tr class="hover:bg-gray-50 {{ !$review->is_approved ? 'bg-orange-50' : '' }}">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $review->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[180px]">
                            {{ $review->package?->name ?? '—' }}
                        </p>
                    </td>
                    <td class="px-5 py-3 max-w-xs">
                        <p class="text-gray-700 line-clamp-2">{{ $review->comment }}</p>
                    </td>
                    <td class="px-5 py-3">
                        @if($review->rating)
                            <span class="text-yellow-500">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if($review->is_approved)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Onaylı</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700">Bekliyor</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400 whitespace-nowrap">
                        {{ $review->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            @if(!$review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 text-xs font-medium">Onayla</button>
                            </form>
                            @endif
                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium"
                                        onclick="return confirm('Bu yorumu silmek istediğinize emin misiniz?')">
                                    Sil
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Yorum bulunamadı.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t">{{ $reviews->links() }}</div>
    </div>
</div>
@endsection
