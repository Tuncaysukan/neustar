@extends('layouts.admin')

@section('header')
    {{ isset($sponsor) ? 'Sponsoru Düzenle' : 'Yeni Sponsor' }}
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form action="{{ isset($sponsor) ? route('admin.sponsors.update', $sponsor) : route('admin.sponsors.store') }}" method="POST">
            @csrf
            @if(isset($sponsor))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sponsor Adı</label>
                    <input type="text" name="name" value="{{ old('name', $sponsor->name ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Link</label>
                    <input type="url" name="link" value="{{ old('link', $sponsor->link ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Pozisyon (home_top, sidebar vb.)</label>
                    <input type="text" name="position" value="{{ old('position', $sponsor->position ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Sıralama</label>
                    <input type="number" name="order" value="{{ old('order', $sponsor->order ?? 0) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="inline-flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $sponsor->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.sponsors.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2 hover:bg-gray-300">İptal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kaydet</button>
            </div>
        </form>
    </div>
@endsection
