<x-guest-layout>
    @php $isEn = app()->getLocale() === 'en'; @endphp
    <x-auth.auth-wrapper>

        <h2 class="text-xl font-bold mb-6">{{ $isEn ? 'Reset Password' : 'Reset Password' }}</h2>


        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-label for="email" value="Email" />
                <x-input id="email" class="w-full rounded-xl"
                    type="email" name="email"
                    value="{{ old('email', $request->email) }}" required />
            </div>

            <div>
                <x-label for="password" :value="$isEn ? 'New Password' : 'Password Baru'" />

                <x-input id="password" class="w-full rounded-xl"
                    type="password" name="password" required />
            </div>

            <div>
                <x-label for="password_confirmation" value="Konfirmasi Password" />
                <x-input id="password_confirmation" class="w-full rounded-xl"
                    type="password" name="password_confirmation" required />
            </div>

            <button class="w-full py-3 rounded-xl bg-[#0194F3] text-white font-semibold">
                {{ $isEn ? 'Reset Password' : 'Reset Password' }}

            </button>
        </form>

    </x-auth.auth-wrapper>
</x-guest-layout>