<x-guest-layout>
    @php $isEn = app()->getLocale() === 'en'; @endphp
    <x-auth.auth-wrapper>

        <h2 class="text-xl font-bold mb-6">{{ $isEn ? 'Create Account' : 'Daftar Akun' }}</h2>


        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="name" :value="$isEn ? 'Name' : 'Nama'" />
                <x-input id="name" class="w-full rounded-xl"
                    type="text" name="name"
                    value="{{ old('name') }}" required />
            </div>

            <div>
                <x-label for="email" value="Email" />
                <x-input id="email" class="w-full rounded-xl"
                    type="email" name="email"
                    value="{{ old('email') }}" required />
            </div>

            <div>
                <x-label for="password" value="Password" />
                <x-input id="password" class="w-full rounded-xl"
                    type="password" name="password" required />
            </div>

            <div>
                <x-label for="password_confirmation" :value="$isEn ? 'Confirm Password' : 'Konfirmasi Password'" />
                <x-input id="password_confirmation" class="w-full rounded-xl"
                    type="password" name="password_confirmation" required />
            </div>

            <div class="flex items-center justify-between text-sm">
                <a href="{{ route('login') }}" class="text-[#0194F3] hover:underline">
                    {{ $isEn ? 'Already have an account? Sign in' : 'Sudah punya akun? Login' }}
                </a>

                <button class="px-5 py-2 rounded-xl bg-[#0194F3] text-white font-semibold">
                    {{ $isEn ? 'Register' : 'Daftar' }}
                </button>

            </div>
        </form>

    </x-auth.auth-wrapper>
</x-guest-layout>