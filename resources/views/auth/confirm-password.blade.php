<x-guest-layout>
    @php $isEn = app()->getLocale() === 'en'; @endphp
    <x-auth.auth-wrapper>

        <h2 class="text-xl font-bold mb-4">{{ $isEn ? 'Confirm Password' : 'Konfirmasi Password' }}</h2>

        <p class="text-sm text-gray-600 mb-6">
            {{ $isEn
        ? 'For security, please confirm your password.'
        : 'Demi keamanan, silakan konfirmasi password Anda.'
    }}
        </p>

        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="password" value="Password" />
                <x-input id="password" class="w-full rounded-xl"
                    type="password" name="password" required />
            </div>


            <button class="w-full py-3 rounded-xl bg-[#0194F3] text-white font-semibold">
                {{ $isEn ? 'Confirm' : 'Konfirmasi' }}
            </button>
        </form>

    </x-auth.auth-wrapper>
</x-guest-layout>