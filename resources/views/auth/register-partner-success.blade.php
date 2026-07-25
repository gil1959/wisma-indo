<x-guest-layout>
    @php $isEn = app()->getLocale() === 'en'; @endphp
    <div class="min-h-screen flex items-center justify-center p-4 py-12 bg-slate-50">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden text-center p-8 lg:p-10">
            
            <div class="mb-6 flex justify-center">
                <div class="h-20 w-20 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 mb-4">
                {{ $isEn ? 'Registration Successful!' : 'Pendaftaran Berhasil!' }}
            </h1>
            
            <p class="text-slate-500 text-sm lg:text-base leading-relaxed mb-8">
                {{ $isEn ? 'Your registration data has been successfully sent. Currently, your account status is ' : 'Data pendaftaran Anda telah berhasil dikirim. Saat ini status akun Anda sedang ' }} 
                <span class="font-bold text-amber-500">{{ $isEn ? 'Waiting for Verification' : 'Menunggu Verifikasi' }}</span>. 
                {{ $isEn ? 'Admin will review your data soon.' : 'Admin akan segera meninjau kelengkapan data Anda.' }}
            </p>

            <div class="bg-blue-50 text-blue-800 rounded-2xl p-5 mb-8 border border-blue-100 text-sm text-left">
                <p class="mb-2 font-bold flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Langkah Selanjutnya:
                </p>
                <ol class="list-decimal ml-5 space-y-1 text-blue-700">
                    <li>Klik tombol WhatsApp di bawah untuk menginfokan Admin secara langsung.</li>
                    <li>Admin akan mengecek dan memverifikasi dokumen Anda.</li>
                    <li>Pemberitahuan verifikasi akan dikirimkan ke Email Anda.</li>
                </ol>
            </div>

            <div class="flex flex-col gap-3">
                <a href="{{ $waUrl }}" target="_blank" class="w-full inline-flex justify-center items-center gap-2 px-6 py-3.5 bg-[#25D366] text-white font-bold rounded-xl shadow-lg shadow-green-500/30 hover:bg-[#1ebd59] hover:-translate-y-0.5 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    Verifikasi via WhatsApp
                </a>
                
                <a href="{{ route('home') }}" class="w-full px-6 py-3.5 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>
