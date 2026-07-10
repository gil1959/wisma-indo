<x-guest-layout>
    @php $isEn = app()->getLocale() === 'en'; @endphp
    <div class="min-h-dvh bg-slate-50">

        {{-- GRID WRAPPER --}}
        <div class="min-h-dvh lg:grid lg:grid-cols-2">

            {{-- LEFT / BRAND (jadi header di mobile, jadi panel di desktop) --}}
            <section class="relative overflow-hidden bg-gradient-to-br from-[#0194F3] to-[#027DD1] text-white
                            lg:min-h-dvh">

                {{-- pattern --}}
                <div class="absolute inset-0 opacity-20"
                    style="background-image: radial-gradient(circle at top left, white 1px, transparent 1px); background-size: 28px 28px;">
                </div>

                {{-- glow --}}
                <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full blur-3xl opacity-25"
                    style="background: radial-gradient(circle, rgba(255,255,255,0.65) 0%, transparent 60%);"></div>

                {{-- content: full height desktop, compact mobile --}}
                <div class="relative z-10 h-full lg:min-h-dvh flex flex-col">

                    {{-- top --}}
                    <div class="px-6 py-6 lg:px-12 lg:py-10">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/logo.png') }}"
                                alt="Bintang Wisata"
                                class="h-10 lg:h-12 w-auto object-contain">
                        </div>
                    </div>

                    {{-- middle --}}
                    <div class="px-6 pb-8 lg:px-12 lg:pb-0 lg:flex-1 lg:flex lg:items-center">
                        <div class="max-w-lg">
                            <h1 class="text-3xl lg:text-5xl font-extrabold leading-tight mb-4">
                                {{ $isEn ? 'Welcome Back' : 'Selamat Datang Kembali' }}
                            </h1>
                            <p class="text-white/85 text-base lg:text-lg leading-relaxed">
                                {{ $isEn
    ? 'Manage trips, articles, and travel services with Bintang Wisata’s professional dashboard.'
    : 'Kelola perjalanan, artikel, dan layanan wisata dengan dashboard profesional Bintang Wisata.'
}}

                            </p>
                        </div>
                    </div>

                    {{-- bottom (desktop only) --}}
                    <div class="hidden lg:block px-12 py-10 text-sm text-white/75">
                        © {{ date('Y') }} Bintang Wisata Indonesia
                    </div>
                </div>
            </section>

            {{-- RIGHT / FORM --}}
            <section class="lg:min-h-dvh flex items-center justify-center px-4 py-10 sm:px-6 lg:px-12 bg-slate-50">
                <div class="w-full max-w-md">

                    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-7 sm:p-8">
                        <h2 class="text-2xl font-bold text-slate-900 mb-1">
                            {{ $isEn ? 'Sign In' : 'Login' }}
                        </h2>
                        <p class="text-sm text-slate-500 mb-6">
                            {{ $isEn ? 'Sign in to the admin dashboard' : 'Masuk ke dashboard admin' }}
                        </p>


                        <x-auth-session-status class="mb-4" :status="session('status')" />
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <div>
                                <x-label for="email" :value="$isEn ? 'Email' : 'Email'" />
                                <x-input
                                    id="email"
                                    class="block mt-1 w-full rounded-xl"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autofocus />
                            </div>

                            <div>
                                <x-label for="password" :value="$isEn ? 'Password' : 'Password'" />
                                <x-input
                                    id="password"
                                    class="block mt-1 w-full rounded-xl"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password" />
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        class="rounded border-gray-300 text-[#0194F3] focus:ring-[#0194F3]" />
                                    <span class="ml-2 text-slate-600">
                                        {{ $isEn ? 'Remember me' : 'Ingat saya' }}
                                    </span>
                                </label>

                                @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-[#0194F3] hover:underline font-medium">
                                    {{ $isEn ? 'Forgot password?' : 'Lupa password?' }}
                                </a>
                                @endif
                            </div>

                            <button
                                type="submit"
                                class="w-full inline-flex justify-center items-center rounded-xl px-4 py-3 text-white font-semibold shadow-lg transition hover:opacity-95"
                                style="background: linear-gradient(90deg, #0194F3 0%, #027DD1 100%);">
                                {{ $isEn ? 'Sign In' : 'Masuk' }}
                            </button>
                            <div class="mt-4 text-center text-sm text-slate-600">
                                {{ $isEn ? "Don't have an account?" : 'Belum punya akun?' }}
                                <a href="{{ route('register') }}" class="font-semibold text-[#0194F3] hover:underline">
                                    {{ $isEn ? 'Register' : 'Daftar' }}
                                </a>
                            </div>

                        </form>
                    </div>

                    {{-- mobile copyright --}}
                    <div class="lg:hidden text-center text-xs text-slate-400 mt-6">
                        © {{ date('Y') }} Bintang Wisata Indonesia
                    </div>

                </div>
            </section>

        </div>
    </div>
</x-guest-layout>