@extends('layouts.admin')

@section('header')
    {{ isset($faq) ? 'Soruyu Düzenle' : 'Yeni Soru' }}
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form action="{{ isset($faq) ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" method="POST">
            @csrf
            @if(isset($faq))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Soru</label>
                    <input type="text" name="question" value="{{ old('question', $faq->question ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Cevap</label>
                    <textarea name="answer" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sayfa Tipi</label>
                        <select name="page_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="general" {{ old('page_type', $faq->page_type ?? '') == 'general' ? 'selected' : '' }}>Genel</option>
                            <option value="home" {{ old('page_type', $faq->page_type ?? '') == 'home' ? 'selected' : '' }}>Anasayfa</option>
                            <option value="operator" {{ old('page_type', $faq->page_type ?? '') == 'operator' ? 'selected' : '' }}>Operatör Sayfası</option>
                            <option value="package" {{ old('page_type', $faq->page_type ?? '') == 'package' ? 'selected' : '' }}>Paket Sayfası</option>
                            <option value="location" {{ old('page_type', $faq->page_type ?? '') == 'location' ? 'selected' : '' }}>İl/İlçe Sayfası</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sıralama</label>
                        <input type="number" name="order" value="{{ old('order', $faq->order ?? 0) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.faqs.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2 hover:bg-gray-300">İptal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kaydet</button>
            </div>
        </form>
    </div>
@endsection
