@extends('frontend.layouts.app')

@section('title', 'Erişim Reddedildi — Neustar')

@section('content')
<section class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-lg text-center">

        <div class="relative inline-block">
            <span class="text-[120px] sm:text-[160px] font-black leading-none select-none"
                  style="color: transparent; -webkit-text-stroke: 2px #f97316; opacity: 0.12;">
                403
            </span>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-warning" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>
        </div>

        <h1 class="mt-4 text-2xl sm:text-3xl font-bold tracking-tight">
            Erişim reddedildi
        </h1>
        <p class="mt-3 text-sm sm:text-base text-base-content/60 leading-relaxed max-w-sm mx-auto">
            Bu sayfayı görüntüleme yetkiniz bulunmuyor.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-primary">Ana Sayfaya Dön</a>
            <a href="javascript:history.back()" class="btn btn-outline">Geri Dön</a>
        </div>
    </div>
</section>
@endsection
