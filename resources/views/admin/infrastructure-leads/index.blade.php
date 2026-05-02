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
        <div class="p-4 border-b">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-3">
                <div>
                    <h3 class="text-lg font-bold">Gelen başvurular</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Kullanıcılar altyapı sorgusu sonrası "beni arayın" formuyla bırakıyor.
                    </p>
                </div>
                {{-- Excel Export --}}
                <a href="{{ route('admin.infrastructure-leads.export', array_filter(['q' => $q, 'status' => $status, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'operator_id' => request('operator_id')])) }}"
                   class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel İndir
                </a>
            </div>

            {{-- Filtreler --}}
            <form method="GET" action="{{ route('admin.infrastructure-leads.index') }}"
                  class="space-y-2">

                {{-- Satır 1: Arama + Durum + Operatör --}}
                <div class="flex flex-wrap gap-2">
                    <input type="search" name="q" value="{{ $q }}"
                           placeholder="Ad / telefon / il ara"
                           class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 flex-1 min-w-[160px]">

                    <select name="status" class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tüm durumlar</option>
                        @foreach($statusLabels as $key => $label)
                            <option value="{{ $key }}" @selected($status === $key)>
                                {{ $label }} ({{ $counts[$key] ?? 0 }})
                            </option>
                        @endforeach
                    </select>

                    <select name="operator_id" class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tüm operatörler</option>
                        @foreach($operators as $op)
                            <option value="{{ $op->id }}" @selected(request('operator_id') == $op->id)>{{ $op->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Satır 2: Tarih + Butonlar --}}
                <div class="flex flex-wrap items-center gap-2">
                    <label class="text-xs text-gray-500 whitespace-nowrap">Tarih aralığı:</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <span class="text-gray-400 text-xs">—</span>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">

                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Filtrele</button>
                    @if($q || $status || $dateFrom || $dateTo || request('operator_id'))
                        <a href="{{ route('admin.infrastructure-leads.index') }}"
                           class="px-3 py-2 rounded text-sm bg-gray-100 text-gray-600 hover:bg-gray-200">
                            Temizle
                        </a>
                    @endif
                </div>
            </form>

            {{-- Aktif filtre özeti --}}
            @if($dateFrom || $dateTo || request('operator_id'))
            <div class="mt-2 text-xs text-blue-700 bg-blue-50 rounded px-3 py-1.5 flex flex-wrap gap-3">
                @if(request('operator_id'))
                    @php $selectedOp = $operators->firstWhere('id', request('operator_id')); @endphp
                    @if($selectedOp) <span>🏢 Operatör: <strong>{{ $selectedOp->name }}</strong></span> @endif
                @endif
                @if($dateFrom) <span>📅 Başlangıç: <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d.m.Y') }}</strong></span> @endif
                @if($dateTo) <span>📅 Bitiş: <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d.m.Y') }}</strong></span> @endif
            </div>
            @endif
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
