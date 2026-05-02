@extends('layouts.admin')

@section('header')
    Paketler
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-3">
                <h3 class="text-lg font-bold">Paket Listesi</h3>
                <div class="flex gap-2">
                    <a href="{{ route('admin.packages.export', array_filter(['operator_id' => $operatorId, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}"
                       class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excel İndir
                    </a>
                    <a href="{{ route('admin.packages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm whitespace-nowrap">Yeni Paket</a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.packages.index') }}" class="flex flex-wrap items-center gap-2">
                <select name="operator_id" class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tüm operatörler</option>
                    @foreach($operators as $op)
                        <option value="{{ $op->id }}" @selected($operatorId == $op->id)>{{ $op->name }}</option>
                    @endforeach
                </select>
                <label class="text-xs text-gray-500 whitespace-nowrap">Tarih:</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                <span class="text-gray-400 text-xs">—</span>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Filtrele</button>
                @if($operatorId || $dateFrom || $dateTo)
                    <a href="{{ route('admin.packages.index') }}" class="px-3 py-2 rounded text-sm bg-gray-100 text-gray-600 hover:bg-gray-200">Temizle</a>
                @endif
            </form>

            @if($operatorId || $dateFrom || $dateTo)
            <div class="mt-2 text-xs text-blue-700 bg-blue-50 rounded px-3 py-1.5 flex flex-wrap gap-3">
                @if($operatorId)
                    @php $selOp = $operators->firstWhere('id', $operatorId); @endphp
                    @if($selOp) <span>🏢 <strong>{{ $selOp->name }}</strong></span> @endif
                @endif
                @if($dateFrom) <span>📅 <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d.m.Y') }}</strong></span> @endif
                @if($dateTo) <span>— <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d.m.Y') }}</strong></span> @endif
            </div>
            @endif
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operatör</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hız</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tıklama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sponsor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($packages as $package)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $package->operator->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $package->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($package->price, 2) }} TL</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $package->speed }} Mbps</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="inline-flex items-center gap-1 font-semibold {{ ($package->clicks_count ?? $package->click_count) > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/>
                                </svg>
                                {{ number_format($package->clicks_count ?? $package->click_count) }}
                                @if($dateFrom || $dateTo)
                                    <span class="text-[10px] text-gray-400 font-normal">(filtreli)</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($package->is_sponsored)
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3">
                                        <path d="M10 1l2.39 5.84L18 7.92l-4 4.27.94 5.81L10 15.27 5.06 18 6 12.19 2 7.92l5.61-1.08L10 1z"/>
                                    </svg>
                                    Sponsor
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.packages.edit', $package) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Düzenle</a>
                            <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="inline">
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
            {{ $packages->links() }}
        </div>
    </div>
@endsection
