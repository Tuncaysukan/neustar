@extends('layouts.admin')
@section('header') {{ isset($user) ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı' }} @endsection

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-posta <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Şifre {{ isset($user) ? '(boş bırakırsanız değişmez)' : '' }}
                    @if(!isset($user)) <span class="text-red-500">*</span> @endif
                </label>
                <input type="password" name="password" {{ !isset($user) ? 'required' : '' }}
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Şifre Tekrar</label>
                <input type="password" name="password_confirmation"
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="rounded-lg border-2 border-red-200 bg-red-50 p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="is_admin" value="0">
                    <input type="checkbox" name="is_admin" value="1"
                           {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}
                           class="mt-1 rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                    <div>
                        <span class="block text-sm font-semibold text-red-900">Admin Yetkisi</span>
                        <span class="block text-xs text-red-700/80 mt-0.5">
                            Admin kullanıcılar tüm yönetim paneline erişebilir.
                        </span>
                    </div>
                </label>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">İptal</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ isset($user) ? 'Güncelle' : 'Oluştur' }}
            </button>
        </div>
    </form>
</div>
@endsection
