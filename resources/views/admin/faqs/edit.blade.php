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
                        <select name="page_type" id="page_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="general" {{ old('page_type', $faq->page_type ?? '') == 'general' ? 'selected' : '' }}>Genel</option>
                            <option value="home" {{ old('page_type', $faq->page_type ?? '') == 'home' ? 'selected' : '' }}>Anasayfa</option>
                            <option value="operator" {{ old('page_type', $faq->page_type ?? '') == 'operator' ? 'selected' : '' }}>Operatör Sayfası</option>
                            <option value="package" {{ old('page_type', $faq->page_type ?? '') == 'package' ? 'selected' : '' }}>Paket Sayfası</option>
                            <option value="compare" {{ old('page_type', $faq->page_type ?? '') == 'compare' ? 'selected' : '' }}>Karşılaştırma Sayfası</option>
                            <option value="speed_test" {{ old('page_type', $faq->page_type ?? '') == 'speed_test' ? 'selected' : '' }}>Hız Testi Sayfası</option>
                            <option value="commitment" {{ old('page_type', $faq->page_type ?? '') == 'commitment' ? 'selected' : '' }}>Taahhüt Sayacı Sayfası</option>
                            <option value="location" {{ old('page_type', $faq->page_type ?? '') == 'location' ? 'selected' : '' }}>İl/İlçe Sayfası</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sıralama</label>
                        <input type="number" name="order" value="{{ old('order', $faq->order ?? 0) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Relation ID seçimi (Operatör veya Paket) --}}
                @php($currentPageType = old('page_type', $faq->page_type ?? ''))
                <div id="operator_select_wrapper" class="{{ $currentPageType == 'operator' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700">Operatör Seçin</label>
                    <select name="relation_id_operator" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 relation-select">
                        <option value="">-- Operatör Seçin --</option>
                        @foreach($operators as $operator)
                            <option value="{{ $operator->id }}" {{ old('relation_id', $faq->relation_id ?? '') == $operator->id ? 'selected' : '' }}>{{ $operator->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="package_select_wrapper" class="{{ $currentPageType == 'package' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700">Paket Seçin</label>
                    <select name="relation_id_package" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 relation-select">
                        <option value="">-- Paket Seçin --</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ old('relation_id', $faq->relation_id ?? '') == $package->id ? 'selected' : '' }}>{{ $package->name }} ({{ $package->operator->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Hidden field for actual relation_id --}}
                <input type="hidden" name="relation_id" id="relation_id" value="{{ old('relation_id', $faq->relation_id ?? '') }}">

                <script>
                    document.getElementById('page_type').addEventListener('change', function() {
                        const operatorWrapper = document.getElementById('operator_select_wrapper');
                        const packageWrapper = document.getElementById('package_select_wrapper');
                        const relationIdField = document.getElementById('relation_id');

                        operatorWrapper.classList.add('hidden');
                        packageWrapper.classList.add('hidden');
                        relationIdField.value = '';

                        if (this.value === 'operator') {
                            operatorWrapper.classList.remove('hidden');
                        } else if (this.value === 'package') {
                            packageWrapper.classList.remove('hidden');
                        }
                    });

                    // Handle relation select changes
                    document.querySelectorAll('.relation-select').forEach(function(select) {
                        select.addEventListener('change', function() {
                            document.getElementById('relation_id').value = this.value;
                        });
                    });
                </script>

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
