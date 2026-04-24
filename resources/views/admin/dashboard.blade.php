@extends('layouts.admin')

@section('header')
    Dashboard
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold text-gray-700">Toplam Paket</h3>
            <p class="text-3xl font-bold text-blue-600">{{ \App\Models\InternetPackage::count() }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold text-gray-700">Aktif Operatör</h3>
            <p class="text-3xl font-bold text-green-600">{{ \App\Models\Operator::where('is_active', true)->count() }}</p>
        </div>
        <a href="{{ route('admin.infrastructure-leads.index', ['status' => 'new']) }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
            <h3 class="text-lg font-bold text-gray-700">Bekleyen Başvuru</h3>
            <p class="text-3xl font-bold text-orange-600">{{ \App\Models\InfrastructureLead::where('status', 'new')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Son 24 saat: {{ \App\Models\InfrastructureLead::where('created_at', '>=', now()->subDay())->count() }}</p>
        </a>
    </div>

    <div class="mt-8 bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-bold text-gray-700 mb-4">Hızlı Erişim</h3>
        <div class="flex space-x-4">
            <a href="{{ route('admin.packages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Yeni Paket Ekle</a>
            <a href="{{ route('admin.blogs.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Yeni Blog Yazısı</a>
            <a href="{{ route('admin.sponsors.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Sponsorları Yönet</a>
            <a href="{{ route('admin.infrastructure.index') }}" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">Altyapı Durumu</a>
            <a href="{{ route('admin.infrastructure-leads.index') }}" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">Başvurular</a>
        </div>
    </div>
@endsection
