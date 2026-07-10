@props([
  'title' => 'Partner',
  'subtitle' => '',
  'center' => false,
])

{{-- 
  FIX KERAS:
  - fixed inset-0 => nutup semua wrapper/padding dari luar
  - overflow-auto => tetap bisa scroll
--}}
<div class="fixed inset-0 w-screen h-screen bg-slate-50 overflow-auto">
    <div class="w-full min-h-screen grid lg:grid-cols-12">

        {{-- LEFT RAIL (FULL) --}}
        <aside class="lg:col-span-4 xl:col-span-3 text-white w-full min-h-screen relative overflow-hidden"
               style="background: linear-gradient(135deg, #0194F3 0%, #027DD1 55%, #0167B4 100%);">

            <div class="absolute inset-0 opacity-20 pointer-events-none"
                 style="background-image:
                    radial-gradient(circle at 20% 20%, rgba(255,255,255,.35) 0, rgba(255,255,255,0) 55%),
                    radial-gradient(circle at 80% 10%, rgba(255,255,255,.25) 0, rgba(255,255,255,0) 55%);">
            </div>

            {{-- isi rail biar gak kosong: justify-between --}}
            <div class="relative h-full min-h-screen flex flex-col justify-between px-7 py-10 lg:px-9 lg:py-12">

    {{-- TOP: Brand + Title --}}
    <div>
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Bintang Wisata" class="h-9 w-auto object-contain">
            <div class="min-w-0">
                <div class="text-sm font-extrabold tracking-tight truncate">Bintang Wisata</div>
                <div class="text-xs text-white/90 truncate">Partner Program</div>
            </div>
        </div>

        <div class="mt-10">
            <h1 class="text-3xl font-extrabold tracking-tight leading-tight">
                {{ $title }}
            </h1>

            @if($subtitle)
                <p class="mt-3 text-sm text-white/90 leading-relaxed max-w-sm">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        {{-- Benefit cards (professional, bukan checklist receh) --}}
        <div class="mt-8 grid gap-4">
            <div class="rounded-3xl border border-white/15 bg-white/10 p-5">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-white/15 border border-white/20 grid place-items-center font-extrabold">
                        ★
                    </div>
                    <div>
                        <div class="text-sm font-extrabold">Akses Panel Partner</div>
                        <div class="mt-1 text-xs text-white/85 leading-relaxed">
                            Kelola data akun, status, dan kebutuhan operasional melalui dashboard partner.
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-white/15 bg-white/10 p-5">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-white/15 border border-white/20 grid place-items-center font-extrabold">
                        ⛨
                    </div>
                    <div>
                        <div class="text-sm font-extrabold">Verifikasi & Keamanan</div>
                        <div class="mt-1 text-xs text-white/85 leading-relaxed">
                            Data partner diverifikasi admin untuk memastikan keamanan dan kualitas layanan.
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-white/15 bg-white/10 p-5">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-white/15 border border-white/20 grid place-items-center font-extrabold">
                        ₿
                    </div>
                    <div>
                        <div class="text-sm font-extrabold">Pengaturan Pajak & Status</div>
                        <div class="mt-1 text-xs text-white/85 leading-relaxed">
                            Admin dapat menetapkan pajak (persen) dan status akun (aktif/suspend) sesuai kebijakan.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick help box --}}
        <div class="mt-6 rounded-3xl border border-white/15 bg-white/10 p-5">
            <div class="text-xs font-extrabold uppercase text-white/90">Butuh bantuan cepat?</div>
            <div class="mt-2 text-sm text-white/90 leading-relaxed">
                Gunakan tombol <span class="font-extrabold">“Hubungi Admin”</span> di sisi kanan untuk chat WhatsApp.
            </div>
            <div class="mt-2 text-xs text-white/80">
                Pastikan email yang kamu isi aktif & bisa menerima email.
            </div>
        </div>
    </div>

    {{-- BOTTOM: footer (nempel bawah karena justify-between) --}}
    <div class="pt-10">
       

        <div class="mt-4 text-[11px] text-white/70 leading-relaxed">
            Dengan melanjutkan, kamu menyetujui kebijakan dan proses verifikasi Partner Bintang Wisata.
        </div>
    </div>

</div>

        </aside>

        {{-- RIGHT CONTENT --}}
        <main class="lg:col-span-8 xl:col-span-9 w-full">
            {{-- KUNCI: center vertical beneran tanpa py besar --}}
            <div class="w-full min-h-screen px-4 sm:px-6 lg:px-10
                        {{ $center ? 'flex items-center justify-center py-0' : 'py-8 lg:py-10' }}">

                <div class="w-full bg-white border border-slate-200 rounded-3xl shadow-xl
                            {{ $center ? 'p-6 sm:p-8 lg:p-10' : 'p-5 sm:p-6 lg:p-8' }}
                            max-w-none 2xl:max-w-6xl 2xl:mx-auto">
                    {{ $slot }}
                </div>
            </div>
        </main>

    </div>
</div>
