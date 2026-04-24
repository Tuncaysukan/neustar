@php
    $isEdit = $record->exists;
    $action = $isEdit
        ? route('admin.infrastructure.update', $record)
        : route('admin.infrastructure.store');
@endphp

<div class="bg-white rounded-lg shadow overflow-hidden p-6">
    <form action="{{ $action }}" method="POST" class="space-y-8">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded p-3">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Bölge --}}
        <section>
            <h3 class="text-base font-semibold text-gray-900">Bölge</h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Slug değerleri girdiğiniz isimlerden otomatik üretilir.
            </p>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">İl <span class="text-red-500">*</span></label>
                    <input type="text" name="city_name" required
                           value="{{ old('city_name', $record->city_name) }}"
                           placeholder="Örn. İstanbul"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">İlçe</label>
                    <input type="text" name="district_name"
                           value="{{ old('district_name', $record->district_name) }}"
                           placeholder="Örn. Kadıköy"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-[11px] text-gray-400">Boş bırakılırsa kayıt il-geneli baseline olur.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mahalle</label>
                    <input type="text" name="neighborhood_name"
                           value="{{ old('neighborhood_name', $record->neighborhood_name) }}"
                           placeholder="Örn. Caferağa Mah."
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </section>

        {{-- Kapsama --}}
        <section>
            <h3 class="text-base font-semibold text-gray-900">Kapsama oranları</h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Yüzde 0–100 arası. Boş bırakılan teknoloji "bilinmiyor" olarak işlenir.
            </p>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fiber (%)</label>
                    <input type="number" min="0" max="100" name="fiber_coverage"
                           value="{{ old('fiber_coverage', $record->fiber_coverage) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">VDSL (%)</label>
                    <input type="number" min="0" max="100" name="vdsl_coverage"
                           value="{{ old('vdsl_coverage', $record->vdsl_coverage) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ADSL (%)</label>
                    <input type="number" min="0" max="100" name="adsl_coverage"
                           value="{{ old('adsl_coverage', $record->adsl_coverage) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </section>

        {{-- Hız --}}
        <section>
            <h3 class="text-base font-semibold text-gray-900">Maksimum hız</h3>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">İndirme (Mbps)</label>
                    <input type="number" min="0" max="10000" name="max_down_mbps"
                           value="{{ old('max_down_mbps', $record->max_down_mbps) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Yükleme (Mbps)</label>
                    <input type="number" min="0" max="10000" name="max_up_mbps"
                           value="{{ old('max_up_mbps', $record->max_up_mbps) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </section>

        {{-- Not --}}
        <section>
            <label class="block text-sm font-medium text-gray-700">Yönetim notu (opsiyonel)</label>
            <textarea name="notes" rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Örn: 2026 Q2 itibarıyla GPON genişletmesi tamamlandı.">{{ old('notes', $record->notes) }}</textarea>
        </section>

        <div class="flex justify-end">
            <a href="{{ route('admin.infrastructure.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2 hover:bg-gray-300">İptal</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Kaydet
            </button>
        </div>
    </form>
</div>
