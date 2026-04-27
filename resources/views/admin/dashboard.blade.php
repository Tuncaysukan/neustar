@extends('layouts.admin')

@section('header')
    Dashboard
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Stat kartları ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        {{-- Paketler --}}
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Toplam Paket</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_packages'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <span class="text-green-600 font-medium">{{ $stats['active_packages'] }} aktif</span>
                        · {{ $stats['sponsored_packages'] }} sponsor
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.packages.index') }}" class="mt-3 text-xs text-blue-600 hover:underline block">Paketleri yönet →</a>
        </div>

        {{-- Operatörler --}}
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Operatörler</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_operators'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <span class="text-green-600 font-medium">{{ $stats['active_operators'] }} aktif</span>
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-green-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.operators.index') }}" class="mt-3 text-xs text-green-600 hover:underline block">Operatörleri yönet →</a>
        </div>

        {{-- Başvurular --}}
        <a href="{{ route('admin.infrastructure-leads.index') }}"
           class="bg-white rounded-lg shadow p-5 border-l-4 border-orange-500 block hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Başvurular</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_leads'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        @if($stats['new_leads'] > 0)
                            <span class="text-orange-600 font-semibold">{{ $stats['new_leads'] }} yeni</span>
                        @else
                            <span class="text-gray-400">Yeni yok</span>
                        @endif
                        · bugün {{ $stats['leads_today'] }}
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-orange-50 flex items-center justify-center relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($stats['new_leads'] > 0)
                        <span class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">
                            {{ $stats['new_leads'] > 9 ? '9+' : $stats['new_leads'] }}
                        </span>
                    @endif
                </div>
            </div>
            <span class="mt-3 text-xs text-orange-600 hover:underline block">Başvuruları gör →</span>
        </a>

        {{-- Yorumlar --}}
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Yorumlar</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_reviews'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        @if($stats['pending_reviews'] > 0)
                            <span class="text-red-600 font-semibold">{{ $stats['pending_reviews'] }} onay bekliyor</span>
                        @else
                            <span class="text-green-600">Tümü onaylı</span>
                        @endif
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-purple-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-400">Taahhüt hatırlatıcı: {{ $stats['commitment_reminders'] }}</p>
        </div>
    </div>

    {{-- ── İkinci satır: Grafik + Top iller ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Başvuru grafiği (son 7 gün) --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-700">Son 7 Günlük Başvurular</h3>
                <span class="text-xs text-gray-400">Bu hafta: {{ $stats['leads_this_week'] }}</span>
            </div>

            @php
                $maxCount = $leadChart->max('count') ?: 1;
            @endphp

            <div class="flex items-end gap-2 h-32">
                @foreach($leadChart as $day)
                    @php $pct = $maxCount > 0 ? ($day['count'] / $maxCount) * 100 : 0; @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-[10px] font-semibold text-gray-600">
                            {{ $day['count'] > 0 ? $day['count'] : '' }}
                        </span>
                        <div class="w-full rounded-t-sm transition-all"
                             style="height: {{ max(4, $pct * 0.9) }}px; background: {{ $pct > 0 ? '#3b82f6' : '#e5e7eb' }};">
                        </div>
                        <span class="text-[9px] text-gray-400 whitespace-nowrap">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- En çok başvuru alan iller --}}
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">En Çok Başvuru Alan İller</h3>
            @if($topCities->isEmpty())
                <p class="text-xs text-gray-400">Henüz başvuru yok.</p>
            @else
                <div class="space-y-3">
                    @foreach($topCities as $city)
                        @php $pct = $stats['total_leads'] > 0 ? round($city->count / $stats['total_leads'] * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-gray-700">{{ $city->city_name }}</span>
                                <span class="text-gray-400">{{ $city->count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Üçüncü satır: Son başvurular + Onay bekleyen yorumlar ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Son başvurular --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-700">Son Başvurular</h3>
                <a href="{{ route('admin.infrastructure-leads.index') }}" class="text-xs text-blue-600 hover:underline">Tümünü gör →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentLeads as $lead)
                    <div class="px-4 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $lead->full_name }}</p>
                            <p class="text-xs text-gray-400 truncate">
                                {{ $lead->city_name }}{{ $lead->district_name ? ' / ' . $lead->district_name : '' }}
                                · {{ $lead->phone }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium
                                {{ $lead->status === 'new' ? 'bg-orange-100 text-orange-700' :
                                   ($lead->status === 'contacted' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                {{ $lead->status === 'new' ? 'Yeni' : ($lead->status === 'contacted' ? 'İletişimde' : 'Dönüştü') }}
                            </span>
                            <span class="text-[10px] text-gray-400">{{ $lead->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-xs text-gray-400">Henüz başvuru yok.</div>
                @endforelse
            </div>
        </div>

        {{-- Onay bekleyen yorumlar --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-700">Onay Bekleyen Yorumlar</h3>
                @if($stats['pending_reviews'] > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                        {{ $stats['pending_reviews'] }} bekliyor
                    </span>
                @endif
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($pendingReviews as $review)
                    <div class="px-4 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-700">
                                    {{ $review->name }}
                                    @if($review->rating)
                                        <span class="text-yellow-500 ml-1">{{ str_repeat('★', $review->rating) }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5 truncate">
                                    {{ $review->internetPackage?->name ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $review->comment }}</p>
                            </div>
                            <span class="text-[10px] text-gray-400 shrink-0">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-xs text-gray-400">
                        Onay bekleyen yorum yok. 🎉
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Hızlı erişim ── --}}
    <div class="bg-white rounded-lg shadow p-5">
        <h3 class="text-sm font-bold text-gray-700 mb-4">Hızlı Erişim</h3>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.packages.create') }}"
               class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition">
                + Yeni Paket
            </a>
            <a href="{{ route('admin.operators.create') }}"
               class="inline-flex items-center gap-1.5 bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 transition">
                + Yeni Operatör
            </a>
            <a href="{{ route('admin.blogs.create') }}"
               class="inline-flex items-center gap-1.5 bg-purple-600 text-white px-3 py-2 rounded text-sm hover:bg-purple-700 transition">
                + Blog Yazısı
            </a>
            <a href="{{ route('admin.faqs.create') }}"
               class="inline-flex items-center gap-1.5 bg-teal-600 text-white px-3 py-2 rounded text-sm hover:bg-teal-700 transition">
                + SSS Ekle
            </a>
            <a href="{{ route('admin.infrastructure-leads.index') }}"
               class="inline-flex items-center gap-1.5 bg-orange-600 text-white px-3 py-2 rounded text-sm hover:bg-orange-700 transition">
                Başvurular
                @if($stats['new_leads'] > 0)
                    <span class="bg-white text-orange-600 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $stats['new_leads'] }}</span>
                @endif
            </a>
            <a href="{{ route('admin.custom-code.index') }}"
               class="inline-flex items-center gap-1.5 bg-gray-700 text-white px-3 py-2 rounded text-sm hover:bg-gray-800 transition">
                CSS / JS Editörü
            </a>
            <a href="{{ route('admin.tariff-seo.index') }}"
               class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-3 py-2 rounded text-sm hover:bg-indigo-700 transition">
                Tarife SEO
            </a>
        </div>
    </div>

</div>
@endsection
