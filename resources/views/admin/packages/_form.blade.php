@php
    $p = $package ?? null;
@endphp

<div class="bg-white rounded-lg shadow overflow-hidden p-6">
    <form action="{{ $action }}" method="POST" class="space-y-6">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        {{-- Operator + Ad --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Operatör <span class="text-red-500">*</span></label>
                <select name="operator_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">— Seçin —</option>
                    @foreach($operators as $operator)
                        <option value="{{ $operator->id }}"
                            {{ old('operator_id', $p->operator_id ?? '') == $operator->id ? 'selected' : '' }}>
                            {{ $operator->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Paket Adı <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $p->name ?? '') }}" required
                       placeholder="Örn. 1.000 Mbps Fiber GigaHome"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        {{-- Fiyat + Hız --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Fiyat (TL) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $p->price ?? '') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Hız (Mbps) <span class="text-red-500">*</span></label>
                <input type="number" name="speed" value="{{ old('speed', $p->speed ?? '') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Upload (Mbps)</label>
                <input type="number" name="upload_speed" value="{{ old('upload_speed', $p->upload_speed ?? '') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Taahhüt (Ay) <span class="text-red-500">*</span></label>
                <input type="number" name="commitment_period" value="{{ old('commitment_period', $p->commitment_period ?? 0) }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500">Taahhütsüz için 0 bırakın.</p>
            </div>
        </div>

        {{-- Kota + Altyapı --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Kota <span class="text-red-500">*</span></label>
                <input type="text" name="quota" value="{{ old('quota', $p->quota ?? 'Sınırsız') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Altyapı Tipi</label>
                <select name="infrastructure_type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @php $infra = old('infrastructure_type', $p->infrastructure_type ?? ''); @endphp
                    <option value="" {{ $infra === '' ? 'selected' : '' }}>— Seçin —</option>
                    @foreach(['Fiber', 'VDSL', 'ADSL', 'Kablo', '5G', '4.5G'] as $type)
                        <option value="{{ $type }}" {{ $infra === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Durum + Sponsor (vurgulu kutu) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $p->is_active ?? true) ? 'checked' : '' }}
                           class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <div>
                        <span class="block text-sm font-semibold text-gray-800">Aktif</span>
                        <span class="block text-xs text-gray-500">Kapalıysa sitede listelenmez.</span>
                    </div>
                </label>
            </div>

            <div class="rounded-lg border-2 border-blue-200 bg-blue-50 p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="is_sponsored" value="0">
                    <input type="checkbox" name="is_sponsored" value="1"
                           {{ old('is_sponsored', $p->is_sponsored ?? false) ? 'checked' : '' }}
                           class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <div>
                        <span class="block text-sm font-semibold text-blue-900">
                            Sponsor Sağlayıcı
                            <span class="ml-2 inline-flex items-center gap-1 rounded-full bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5">
                                Sponsor
                            </span>
                        </span>
                        <span class="block text-xs text-blue-700/80 mt-1">
                            İşaretlenirse paket listelerin en üstünde "Sponsor Sağlayıcı" rozetiyle vurgulanır
                            ve anasayfa "Günün Fırsatları" bölümünde gösterilir.
                        </span>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-4 border-t">
            <a href="{{ route('admin.packages.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">İptal</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ $method === 'POST' ? 'Paketi Oluştur' : 'Değişiklikleri Kaydet' }}
            </button>
        </div>
    </form>
</div>
