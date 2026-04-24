@extends('layouts.admin')

@section('header')
    Paketler
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">Paket Listesi</h3>
            <a href="{{ route('admin.packages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Yeni Paket</a>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operatör</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hız</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sponsor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($packages as $package)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $package->operator->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $package->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($package->price, 2) }} TL</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $package->speed }} Mbps</td>
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
