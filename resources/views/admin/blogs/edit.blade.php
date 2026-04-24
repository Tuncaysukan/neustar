@extends('layouts.admin')

@section('header')
    {{ isset($blog) ? 'Blog Yazısını Düzenle' : 'Yeni Blog Yazısı' }}
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form action="{{ isset($blog) ? route('admin.blogs.update', $blog) : route('admin.blogs.store') }}" method="POST">
            @csrf
            @if(isset($blog))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Başlık</label>
                    <input type="text" name="title" value="{{ old('title', $blog->title ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">İçerik</label>
                    <textarea name="content" rows="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>{{ old('content', $blog->content ?? '') }}</textarea>
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $blog->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.blogs.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2 hover:bg-gray-300">İptal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kaydet</button>
            </div>
        </form>
    </div>
@endsection
