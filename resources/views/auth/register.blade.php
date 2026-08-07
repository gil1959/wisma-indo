<x-guest-layout>
    @php $isEn = app()->getLocale() === 'en'; @endphp
    <x-auth.auth-wrapper>

        <h2 class="text-xl font-bold mb-6">{{ $isEn ? 'Create Account' : 'Daftar Akun' }}</h2>

        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <div class="space-y-4">
            {{-- Google Login Button (if enabled) --}}
            @if (!empty($siteSettings['google_login_active']) && $siteSettings['google_login_active'] == '1')
                <a href="{{ route('google.login') }}" class="w-full inline-flex justify-center items-center gap-2 rounded-xl px-4 py-3 bg-white border border-slate-200 text-slate-700 font-semibold shadow-sm hover:bg-slate-50 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    {{ $isEn ? 'Sign up with Google' : 'Daftar dengan Google' }}
                </a>

                <div class="relative flex items-center gap-3 my-2">
                    <div class="flex-1 border-t border-slate-200"></div>
                    <span class="text-xs text-slate-400 font-medium">{{ $isEn ? 'or register manually' : 'atau daftar manual' }}</span>
                    <div class="flex-1 border-t border-slate-200"></div>
                </div>
            @endif

            {{-- Manual Registration Form --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">
                        {{ $isEn ? 'Full Name' : 'Nama Lengkap' }}
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0194F3]/40 focus:border-[#0194F3] transition"
                        placeholder="{{ $isEn ? 'Your full name' : 'Nama lengkap Anda' }}">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">
                        {{ $isEn ? 'Email Address' : 'Alamat Email' }}
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0194F3]/40 focus:border-[#0194F3] transition"
                        placeholder="nama@email.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">
                        {{ $isEn ? 'Password' : 'Kata Sandi' }}
                    </label>
                    <input id="password" type="password" name="password" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0194F3]/40 focus:border-[#0194F3] transition"
                        placeholder="{{ $isEn ? 'Min. 8 characters' : 'Min. 8 karakter' }}">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">
                        {{ $isEn ? 'Confirm Password' : 'Konfirmasi Kata Sandi' }}
                    </label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0194F3]/40 focus:border-[#0194F3] transition"
                        placeholder="{{ $isEn ? 'Repeat your password' : 'Ulangi kata sandi Anda' }}">
                </div>

                <button type="submit"
                    class="w-full inline-flex justify-center items-center gap-2 rounded-xl px-4 py-3 bg-[#0194F3] text-white font-bold shadow hover:bg-[#027dd1] transition">
                    {{ $isEn ? 'Create Account' : 'Buat Akun' }}
                </button>
            </form>

            <div class="text-center text-sm mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('login') }}" class="text-[#0194F3] hover:underline font-medium">
                    {{ $isEn ? 'Already have an account? Sign in' : 'Sudah punya akun? Login di sini' }}
                </a>
            </div>
        </div>

    </x-auth.auth-wrapper>
</x-guest-layout>