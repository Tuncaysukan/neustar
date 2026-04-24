@extends('layouts.admin')

@section('header', 'Operatörler')

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">Operatör Listesi</h3>
            <a href="{{ route('admin.operators.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Yeni operatör
            </a>
        </div>

        @if(session('success'))
            <div class="m-4 rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marka</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($operators as $operator)
                    <tr>
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                @if($operator->logo)
                                    <img src="{{ $operator->logo_url }}" alt="{{ $operator->name }}"
                                         class="h-10 w-10 rounded-md object-contain bg-white border border-gray-200 p-1">
                                @else
                                    <div class="h-10 w-10 rounded-md grid place-items-center text-sm font-semibold text-gray-500 bg-gray-100 border border-gray-200">
                                        {{ $operator->initial }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $operator->name }}</div>
                                    @if($operator->website_url)
                                        <div class="text-xs text-gray-500">{{ parse_url($operator->website_url, PHP_URL_HOST) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ $operator->slug }}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $operator->packages()->count() }}
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $operator->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $operator->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.operators.edit', $operator) }}"
                               class="text-indigo-600 hover:text-indigo-900 mr-3">Düzenle</a>
                            <form action="{{ route('admin.operators.destroy', $operator) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Silmek istediğinize emin misiniz? Operatöre bağlı paketler de etkilenebilir.')">
                                    Sil
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4">
            {{ $operators->links() }}
        </div>
    </div>
@endsection
