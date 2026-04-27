@extends('layouts.admin')
@section('header') Taahhüt Hatırlatıcıları @endsection

@section('content')
<div class="space-y-4">

    {{-- Özet --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Toplam</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">30 Günde Bitiyor</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $expiring }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between gap-3">
            <h3 class="text-base font-bold">Hatırlatıcı Listesi</h3>
            <form method="GET" action="{{ route('admin.commitment-reminders.index') }}">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="E-posta veya telefon ara..."
                       class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </form>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">İletişim</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Başlangıç</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Süre</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bitiş</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kalan</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reminders as $r)
                @php
                    $daysLeft = now()->diffInDays($r->end_date, false);
                    $isExpired = $daysLeft < 0;
                    $isSoon    = $daysLeft >= 0 && $daysLeft <= 30;
                @endphp
                <tr class="hover:bg-gray-50 {{ $isExpired ? 'bg-red-50' : ($isSoon ? 'bg-orange-50' : '') }}">
                    <td class="px-5 py-3">
                        @if($r->email) <p class="text-gray-800">{{ $r->email }}</p> @endif
                        @if($r->phone) <p class="text-gray-500 text-xs">{{ $r->phone }}</p> @endif
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ \Carbon\Carbon::parse($r->start_date)->format('d.m.Y') }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $r->months }} ay</td>
                    <td class="px-5 py-3 font-medium {{ $isExpired ? 'text-red-600' : ($isSoon ? 'text-orange-600' : 'text-gray-800') }}">
                        {{ \Carbon\Carbon::parse($r->end_date)->format('d.m.Y') }}
                    </td>
                    <td class="px-5 py-3">
                        @if($isExpired)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Süresi doldu</span>
                        @elseif($isSoon)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700">{{ $daysLeft }} gün</span>
                        @else
                            <span class="text-gray-500 text-xs">{{ $daysLeft }} gün</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <form action="{{ route('admin.commitment-reminders.destroy', $r) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs"
                                    onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Hatırlatıcı bulunamadı.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t">{{ $reminders->links() }}</div>
    </div>
</div>
@endsection
