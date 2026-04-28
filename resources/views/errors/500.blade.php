@extends('frontend.layouts.app')

@section('title', 'Sunucu Hatası — Neustar')

@section('content')
<section class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-lg text-center">

        <div class="relative inline-block">
            <span class="text-[120px] sm:text-[160px] font-black leading-none select-none"
                  style="color: transparent; -webkit-text-stroke: 2px #ef4444; opacity: 0.12;">
                500
            </span>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-error" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
        </div>

        <h1 class="mt-4 text-2xl sm:text-3xl font-bold tracking-tight">
            Bir şeyler ters gitti
        </h1>
        <p class="mt-3 text-sm sm:text-base text-base-content/60 leading-relaxed max-w-sm mx-auto">
            Sunucuda beklenmedik bir hata oluştu. Ekibimiz bilgilendirildi.
            Lütfen birkaç dakika sonra tekrar deneyin.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-primary">Ana Sayfaya Dön</a>
            <button onclick="window.location.reload()" class="btn btn-outline">Sayfayı Yenile</button>
        </div>
    </div>
</section>
@endsection
