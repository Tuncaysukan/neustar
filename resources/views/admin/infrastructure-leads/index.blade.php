@extends('layouts.admin')

@section('header')
    Altyapı Başvuruları
@endsection

@section('content')
    @if(session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-2 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold">Gelen başvurular</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Kullanıcılar altyapı sorgusu sonrası "beni arayın" formuyla bırakıyor.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.infrastructure-leads.index') }}"
                  class="flex items-center gap-2">
                <select name="status" class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tüm durumlar</option>
                    @foreach($statusLabels as $key => $label)
                        <option value="{{ $key }}" @selected($status === $key)>
                            {{ $label }} ({{ $counts[$key] ?? 0 }})
                        </option>
                    @endforeach
                </select>

                <input type="search" name="q" value="{{ $q }}"
                       placeholder="Ad / telefon / il ara"
                       class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">

                <button type="submit" class="bg-gray-800 text-white px-3 py-2 rounded text-sm">Filtrele</button>
            </form>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-4 py-3 text-left">İletişim</th>
                    <th class="px-4 py-3 text-left">Adres</th>
                    <th class="px-4 py-3 text-left">Altyapı</th>
                    <th class="px-4 py-3 text-left">Durum</th>
                    <th class="px-4 py-3 text-left">Tarih</th>
                    <th class="px-4 py-3 text-right w-32">İşlem</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse($leads as $lead)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $lead->full_name }}</div>
                            <a href="tel:{{ $lead->phone }}" class="text-xs text-blue-600 hover:underline">{{ $lead->phone }}</a>
                            @if($lead->email)
                                <div class="text-xs text-gray-400">{{ $lead->email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-gray-900">{{ $lead->city_name }}@if($lead->district_name) / {{ $lead->district_name }}@endif</div>
                            <div class="text-xs text-gray-500">
                                {{ $lead->neighborhood_name ?: '—' }}
                                @if($lead->street) · {{ $lead->street }}@endif
                                @if($lead->building_no) No: {{ $lead->building_no }}@endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            @php($snap = $lead->lookup_snapshot ?? null)
                            @if($snap && !empty($snap['primary']))
                                <div class="font-medium text-gray-800">
                                    {{ $snap['primary']['label'] ?? '—' }}
                                    @if(!empty($snap['primary']['coverage']))
                                        <span class="text-gray-400">· %{{ $snap['primary']['coverage'] }}</span>
                                    @endif
                                </div>
                                @if(!empty($snap['max_down_mbps']))
                                    <div class="text-gray-500">Max {{ $snap['max_down_mbps'] }} Mbps</div>
                                @endif
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $lead->status_badge }}">
                                {{ $lead->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                            {{ $lead->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.infrastructure-leads.show', $lead) }}"
                               class="text-indigo-600 hover:text-indigo-900 mr-3">Detay</a>
                            <form action="{{ route('admin.infrastructure-leads.destroy', $lead) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Başvuruyu silmek istediğinize emin misiniz?')">
                                    Sil
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">
                            Henüz başvuru yok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $leads->links() }}
        </div>
    </div>
@endsection
