@extends('layouts.admin')
@section('header') Site Ayarları @endsection

@section('content')
<form action="{{ route('admin.site-settings.update') }}" method="POST">
    @csrf @method('PUT')

    <div class="space-y-6" x-data="{ active: 'general' }">

        {{-- Tab bar --}}
        <div class="bg-white rounded-lg shadow p-1 flex gap-1 w-fit">
            @foreach($groups as $key => $label)
            @if(isset($settings[$key]))
            <button type="button"
                    @click="active = '{{ $key }}'"
                    :class="active === '{{ $key }}' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                {{ $label }}
            </button>
            @endif
            @endforeach
        </div>

        @foreach($groups as $groupKey => $groupLabel)
        @if(isset($settings[$groupKey]))
        <div x-show="active === '{{ $groupKey }}'" x-cloak>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-base font-bold text-gray-800 mb-5">{{ $groupLabel }} Ayarları</h3>
                <div class="space-y-5">
                    @foreach($settings[$groupKey] as $setting)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $setting->label }}</label>
                        @if($setting->type === 'textarea')
                            <textarea name="settings[{{ $setting->key }}]" rows="3"
                                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('settings.' . $setting->key, $setting->value) }}</textarea>
                        @else
                            <input type="{{ $setting->type }}" name="settings[{{ $setting->key }}]"
                                   value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-medium">
                Ayarları Kaydet
            </button>
        </div>
    </div>
</form>
@endsection
