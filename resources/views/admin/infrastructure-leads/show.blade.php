@extends('layouts.admin')

@section('header')
    Başvuru #{{ $lead->id }}
@endsection

@section('content')
    @if(session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-2 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- İletişim + adres --}}
        <div class="bg-white rounded-lg shadow p-5 lg:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-wider text-gray-500">Başvuran</div>
                    <h2 class="text-xl font-bold text-gray-900 mt-1">{{ $lead->full_name }}</h2>
                    <div class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-sm">
                        <a href="tel:{{ $lead->phone }}" class="text-blue-600 hover:underline font-medium">{{ $lead->phone }}</a>
                        @if($lead->email)
                            <a href="mailto:{{ $lead->email }}" class="text-gray-600 hover:underline">{{ $lead->email }}</a>
                        @endif
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $lead->status_badge }}">
                    {{ $lead->status_label }}
                </span>
            </div>

            <hr class="my-5 border-gray-100">

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-xs text-gray-500 uppercase">İl / İlçe</div>
                    <div class="font-medium">{{ $lead->city_name }}@if($lead->district_name) / {{ $lead->district_name }}@endif</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase">Mahalle</div>
                    <div class="font-medium">{{ $lead->neighborhood_name ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase">Sokak / Cadde</div>
                    <div class="font-medium">{{ $lead->street ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase">Bina No</div>
                    <div class="font-medium">{{ $lead->building_no ?: '—' }}</div>
                </div>
            </div>

            @if($lead->lookup_snapshot)
                @php($snap = $lead->lookup_snapshot)
                <hr class="my-5 border-gray-100">
                <div class="text-xs uppercase tracking-wider text-gray-500 mb-2">Altyapı sorgu sonucu</div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach(($snap['technologies'] ?? []) as $tech)
                        <div class="border border-gray-200 rounded-md p-3">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-800">{{ $tech['label'] ?? '—' }}</span>
                                <span class="text-xs text-gray-500 tabular-nums">%{{ $tech['coverage'] ?? 0 }}</span>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                {{ ($tech['available'] ?? false) ? 'Hizmet var' : 'Sınırlı / yok' }}
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(!empty($snap['max_down_mbps']))
                    <div class="mt-3 text-sm text-gray-600">
                        Maksimum hız: <span class="font-semibold">{{ $snap['max_down_mbps'] }} / {{ $snap['max_up_mbps'] ?? '—' }} Mbps</span>
                    </div>
                @endif
            @endif

            @if($lead->ip || $lead->user_agent)
                <hr class="my-5 border-gray-100">
                <div class="text-xs text-gray-400 font-mono break-all">
                    IP: {{ $lead->ip ?: '—' }}<br>
                    UA: {{ $lead->user_agent ?: '—' }}<br>
                    Tarih: {{ $lead->created_at->format('d.m.Y H:i:s') }}
                </div>
            @endif
        </div>

        {{-- Durum / not --}}
        <div class="bg-white rounded-lg shadow p-5">
            <form action="{{ route('admin.infrastructure-leads.update', $lead) }}" method="POST">
                @csrf
                @method('PATCH')

                <h3 class="font-semibold text-gray-900">Durumu güncelle</h3>

                <div class="mt-3">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Durum</label>
                    <select name="status" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        @foreach($statusLabels as $key => $label)
                            <option value="{{ $key }}" @selected($lead->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-3">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Notlar</label>
                    <textarea name="admin_notes" rows="6"
                              class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Arama notu, öneri, red gerekçesi...">{{ old('admin_notes', $lead->admin_notes) }}</textarea>
                </div>

                <button type="submit"
                        class="mt-4 w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                    Kaydet
                </button>
            </form>

            <a href="{{ route('admin.infrastructure-leads.index') }}"
               class="block text-center text-xs text-gray-500 hover:text-gray-700 mt-4">
                ← Listeye dön
            </a>
        </div>
    </div>
@endsection
