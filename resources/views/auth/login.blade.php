<x-guest-layout>

    <div class="ns-surface rounded-2xl p-8 shadow-sm">

        {{-- Başlık --}}
        <div class="text-center mb-8">
            <div class="ns-section-eyebrow mb-2">Giriş Yap</div>
            <h1 class="text-2xl font-bold tracking-tight">Hesabınıza giriş yapın</h1>
            <p class="mt-2 text-sm text-base-content/60">
                Yönetim paneline erişmek için bilgilerinizi girin.
            </p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-4 rounded-lg bg-success/10 border border-success/30 px-4 py-3 text-sm text-success font-medium">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- E-posta --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-base-content/80 mb-1.5">
                    E-posta adresi
                </label>
                <input id="email" type="email" name="email"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       placeholder="ornek@mail.com"
                       class="input input-bordered w-full rounded-lg @error('email') input-error @enderror">
                @error('email')
                    <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Şifre --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-semibold text-base-content/80">
                        Şifre
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-xs text-primary hover:underline">
                            Şifremi unuttum
                        </a>
                    @endif
                </div>
                <input id="password" type="password" name="password"
                       required autocomplete="current-password"
                       placeholder="••••••••"
                       class="input input-bordered w-full rounded-lg @error('password') input-error @enderror">
                @error('password')
                    <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Beni hatırla --}}
            <div>
                <label for="remember_me" class="inline-flex items-center gap-2.5 cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="checkbox checkbox-sm checkbox-primary">
                    <span class="text-sm text-base-content/70">Beni hatırla</span>
                </label>
            </div>

            {{-- Giriş butonu --}}
            <button type="submit" class="btn btn-primary w-full btn-lg">
                Giriş Yap
            </button>
        </form>

    </div>

</x-guest-layout>
