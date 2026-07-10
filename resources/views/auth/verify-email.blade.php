<x-guest-layout>
    @php $isEn = app()->getLocale() === 'en'; @endphp
    <x-auth.auth-wrapper>

        <h2 class="text-xl font-bold text-gray-900 mb-2">
            {{ $isEn ? 'Verify Email' : 'Verifikasi Email' }}
        </h2>

        <p class="text-sm text-gray-600 mb-6">
            {{ $isEn
    ? 'We have sent a verification link to your email. Please click the link to activate your account.'
    : 'Kami telah mengirimkan link verifikasi ke email Anda. Silakan klik link tersebut untuk mengaktifkan akun.'
}}
        </p>

        @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm text-emerald-600 font-medium">
            {{ $isEn ? 'A new verification link has been sent.' : 'Link verifikasi baru telah dikirim.' }}

        </div>
        @endif

        <div class="flex items-center justify-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-[#0194F3] text-white font-semibold hover:opacity-90">
                    {{ $isEn ? 'Resend Email' : 'Kirim Ulang Email' }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout.to.login') }}">
                @csrf
                <button type="submit"
                    class="px-4 py-2 rounded-xl border border-[#0194F3] text-[#0194F3] font-semibold hover:bg-[#0194F3]/10">
                    {{ $isEn ? 'Sign In' : 'Login' }}
                </button>
            </form>
        </div>



    </x-auth.auth-wrapper>
</x-guest-layout>