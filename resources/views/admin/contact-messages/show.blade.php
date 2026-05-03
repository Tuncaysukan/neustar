@extends('layouts.admin')

@section('header')
    İletişim mesajı #{{ $message->id }}
@endsection

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.contact-messages.index') }}" class="text-sm text-blue-600 hover:underline">← Listeye dön</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="text-xs uppercase tracking-wider text-gray-500">Gönderen</div>
                <h2 class="text-xl font-bold text-gray-900 mt-1">{{ $message->name }}</h2>
                <div class="mt-2 text-sm">
                    <a href="mailto:{{ $message->email }}" class="text-blue-600 hover:underline">{{ $message->email }}</a>
                </div>
            </div>
            <div class="text-xs text-gray-500 text-right">
                <div>{{ $message->created_at->format('d.m.Y H:i') }}</div>
                @if($message->ip)
                    <div class="mt-1">IP: {{ $message->ip }}</div>
                @endif
            </div>
        </div>

        @if($message->subject)
            <div>
                <div class="text-xs uppercase tracking-wider text-gray-500">Konu</div>
                <p class="mt-1 font-medium text-gray-900">{{ $message->subject }}</p>
            </div>
        @endif

        <div>
            <div class="text-xs uppercase tracking-wider text-gray-500">Mesaj</div>
            <div class="mt-2 p-4 bg-gray-50 rounded-md text-gray-800 whitespace-pre-wrap text-sm leading-relaxed">{{ $message->message }}</div>
        </div>

        <div class="pt-4 border-t flex justify-end">
            <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Bu mesajı silmek istediğinize emin misiniz?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">Sil</button>
            </form>
        </div>
    </div>
@endsection
