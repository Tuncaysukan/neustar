@extends('frontend.layouts.app')

@section('title', 'İletişim — ' . config('app.name'))
@section('meta_description', 'Sorularınız ve iş birliği talepleri için bize yazın.')

@section('content')
<section class="py-10 sm:py-14">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        @if(!($pageSeo && $pageSeo->title))
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">İletişim</h1>
        @endif
        <p class="mt-3 text-sm sm:text-base text-base-content/65 leading-relaxed">
            Aşağıdaki formdan mesaj bırakabilirsiniz. Yönetim panelinden yanıtlanır.
        </p>

        @if($errors->any())
            <div class="alert alert-error mt-6 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-8 grid gap-8 sm:grid-cols-5">
            <div class="sm:col-span-2 space-y-4 text-sm text-base-content/70">
                @if(!empty($layoutSite['contact_email']))
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-base-content/50">E-posta</div>
                        <a class="link link-primary break-all" href="mailto:{{ $layoutSite['contact_email'] }}">{{ $layoutSite['contact_email'] }}</a>
                    </div>
                @endif
                @if(!empty($layoutSite['contact_phone']))
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-base-content/50">Telefon</div>
                        <a class="link link-primary" href="tel:{{ preg_replace('/\s+/', '', $layoutSite['contact_phone']) }}">{{ $layoutSite['contact_phone'] }}</a>
                    </div>
                @endif
                @if(!empty($layoutSite['contact_address']))
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-base-content/50">Adres</div>
                        <p class="whitespace-pre-line">{{ $layoutSite['contact_address'] }}</p>
                    </div>
                @endif
            </div>

            <form method="post" action="{{ route('contact.store') }}" class="sm:col-span-3 ns-surface rounded-xl p-5 sm:p-6 space-y-4">
                @csrf
                <div>
                    <label class="label"><span class="label-text">Ad Soyad</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="120"
                           class="input input-bordered w-full rounded-md" autocomplete="name">
                </div>
                <div>
                    <label class="label"><span class="label-text">E-posta</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="160"
                           class="input input-bordered w-full rounded-md" autocomplete="email">
                </div>
                <div>
                    <label class="label"><span class="label-text">Konu (isteğe bağlı)</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" maxlength="120"
                           class="input input-bordered w-full rounded-md">
                </div>
                <div>
                    <label class="label"><span class="label-text">Mesaj</span></label>
                    <textarea name="message" rows="6" required maxlength="5000"
                              class="textarea textarea-bordered w-full rounded-md">{{ old('message') }}</textarea>
                </div>
                <label class="label cursor-pointer justify-start gap-3 items-start">
                    <input type="checkbox" name="kvkk" value="1" class="checkbox checkbox-primary checkbox-sm mt-0.5" required>
                    <span class="label-text text-xs leading-relaxed">
                        <a href="#" class="link">KVKK Aydınlatma Metni</a>&rsquo;ni okudum, kişisel verilerimin işlenmesini kabul ediyorum.
                    </span>
                </label>
                <button type="submit" class="btn btn-primary w-full sm:w-auto">Gönder</button>
            </form>
        </div>
    </div>
</section>
@endsection
