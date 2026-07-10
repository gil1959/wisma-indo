<x-guest-layout>
    @php $isEn = app()->getLocale() === 'en'; @endphp
    <x-auth.auth-wrapper>

        <h2 class="text-xl font-bold mb-4">{{ $isEn ? 'Forgot Password' : 'Lupa Password' }}</h2>

        <p class="text-sm text-gray-600 mb-6">
            {{ $isEn
        ? 'Enter your email to receive a password reset link.'
        : 'Masukkan email Anda untuk menerima link reset password.'
    }}
        </p>

        <x-auth-session-status class="mb-4" :status="session('status')" />
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="email" :value="$isEn ? 'Email' : 'Email'" />
                <x-input id="email" class="w-full rounded-xl"
                    type="email" name="email"
                    value="{{ old('email') }}" required />
            </div>

            <button class="w-full py-3 rounded-xl bg-[#0194F3] text-white font-semibold">
                {{ $isEn ? 'Send Reset Link' : 'Kirim Link Reset' }}
            </button>
        </form>

    </x-auth.auth-wrapper>
</x-guest-layout>