@extends('layouts.admin')

@section('header')
    Altyapı Durumu
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold">Bölge bazlı altyapı kayıtları</h3>
                <p class="text-xs text-gray-500 mt-1">
                    İl / ilçe / mahalle kapsama oranları — sorgu servisi bu tabloyu kaynak olarak kullanır.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('admin.infrastructure.index') }}">
                    <input type="search" name="q" value="{{ $q }}"
                           placeholder="İl / ilçe / mahalle ara"
                           class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                </form>
                <a href="{{ route('admin.infrastructure.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm whitespace-nowrap">
                    Yeni kayıt
                </a>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-4 py-3 text-left">Bölge</th>
                    <th class="px-4 py-3 text-right">Fiber</th>
                    <th class="px-4 py-3 text-right">VDSL</th>
                    <th class="px-4 py-3 text-right">ADSL</th>
                    <th class="px-4 py-3 text-right">Hız (Mbps)</th>
                    <th class="px-4 py-3 text-left">Not</th>
                    <th class="px-4 py-3 text-right w-40">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse($rows as $row)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $row->city_name ?? $row->city_slug }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $row->district_name ?: '—' }}
                                @if($row->neighborhood_name)
                                    <span class="text-gray-400">·</span> {{ $row->neighborhood_name }}
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ $row->fiber_coverage !== null ? $row->fiber_coverage . '%' : '—' }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ $row->vdsl_coverage  !== null ? $row->vdsl_coverage  . '%' : '—' }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ $row->adsl_coverage  !== null ? $row->adsl_coverage  . '%' : '—' }}</td>
                        <td class="px-4 py-3 text-right tabular-nums text-gray-600">
                            @if($row->max_down_mbps || $row->max_up_mbps)
                                {{ $row->max_down_mbps ?? '—' }} / {{ $row->max_up_mbps ?? '—' }}
                            @else — @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 max-w-xs truncate">
                            {{ Str::limit($row->notes, 60) }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.infrastructure.edit', $row) }}"
                               class="text-indigo-600 hover:text-indigo-900 mr-3">Düzenle</a>
                            <form action="{{ route('admin.infrastructure.destroy', $row) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Silmek istediğinize emin misiniz?')">
                                    Sil
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">
                            Kayıt yok. <a href="{{ route('admin.infrastructure.create') }}" class="text-blue-600 underline">İlk kaydı ekle</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $rows->links() }}
        </div>
    </div>
@endsection
