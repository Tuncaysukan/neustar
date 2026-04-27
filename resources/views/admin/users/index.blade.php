@extends('layouts.admin')

@section('header') Kullanıcı Yönetimi @endsection

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
        <h3 class="text-lg font-bold">Kullanıcılar</h3>
        <a href="{{ route('admin.users.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
            + Yeni Kullanıcı
        </a>
    </div>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-posta</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kayıt</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 {{ $user->id === auth()->id() ? 'bg-blue-50' : '' }}">
                <td class="px-5 py-3 font-medium">
                    {{ $user->name }}
                    @if($user->id === auth()->id())
                        <span class="ml-1 text-[10px] text-blue-600">(sen)</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>
                <td class="px-5 py-3">
                    @if($user->is_admin)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Admin</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Kullanıcı</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $user->created_at->format('d.m.Y') }}</td>
                <td class="px-5 py-3 whitespace-nowrap">
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="text-indigo-600 hover:text-indigo-900 mr-3">Düzenle</a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900"
                                onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">
                            Sil
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">Kullanıcı bulunamadı.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $users->links() }}</div>
</div>
@endsection
