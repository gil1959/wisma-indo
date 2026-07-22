<x-guest-layout>
    <x-partner.auth-shell
        title="Pendaftaran Terkirim"
        subtitle="Permintaan pendaftaran partner kamu sudah kami terima."
        :center="true"
    >
        @php
            $rawWa = (string) ($siteSettings['footer_whatsapp'] ?? '');
            $wa = preg_replace('/\D+/', '', $rawWa);

            if (!empty($wa) && str_starts_with($wa, '0')) {
                $wa = '62' . substr($wa, 1);
            }

            $waMsg = "Halo Admin Wisma Indo, saya sudah mendaftar Partner dan ingin menanyakan status pendaftaran.";
            $waLink = !empty($wa) ? ("https://wa.me/{$wa}?text=" . urlencode($waMsg)) : null;
        @endphp

        <div class="w-full max-w-4xl mx-auto">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-2xl border border-slate-200 bg-slate-50 grid place-items-center font-extrabold"
                     style="color:#0194F3;">
                    ✓
                </div>

                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Pendaftaran kamu sedang diproses
                    </h2>
                    <p class="mt-1 text-sm text-slate-600 leading-relaxed">
                        Kami akan mengirim pembaruan ke email yang kamu daftarkan. Kamu bisa kembali ke beranda atau hubungi admin jika dibutuhkan.
                    </p>
                </div>
            </div>

            <div class="mt-6 grid lg:grid-cols-12 gap-6">
                <div class="lg:col-span-7 rounded-3xl border border-slate-200 bg-white p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-extrabold text-slate-600 uppercase">Status</div>
                            <div class="mt-1 text-lg font-extrabold text-slate-900">Menunggu pemeriksaan</div>
                        </div>

                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl font-extrabold text-sm"
                              style="background: rgba(1,148,243,.10); color:#0194F3; border:1px solid rgba(1,148,243,.20);">
                            <span class="h-2.5 w-2.5 rounded-full" style="background:#0194F3;"></span>
                            Pending
                        </span>
                    </div>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-sm font-extrabold text-slate-900">Catatan singkat</div>
                        <ul class="mt-2 space-y-2 text-sm text-slate-700">
                            <li class="flex gap-2">
                                <span class="font-extrabold" style="color:#0194F3;">•</span>
                                Pastikan email aktif dan cek folder Spam/Promotions.
                            </li>
                            <li class="flex gap-2">
                                <span class="font-extrabold" style="color:#0194F3;">•</span>
                                Dokumen harus jelas agar pemeriksaan cepat.
                            </li>
                        </ul>
                    </div>

                    <div class="mt-5 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('home') }}"
                           class="inline-flex justify-center items-center px-6 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 font-extrabold hover:bg-slate-50 transition">
                            Kembali ke Beranda
                        </a>

                        <a href="{{ route('login') }}"
                           class="inline-flex justify-center items-center px-6 py-3 rounded-2xl text-white font-extrabold hover:opacity-95 transition"
                           style="background:#0194F3;">
                            Ke Halaman Login
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-4">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs font-extrabold text-slate-600 uppercase">Informasi</div>

                            
                        </div>

                        <div class="mt-4 space-y-3 text-sm text-slate-700">
                            <div class="flex items-start justify-between gap-3">
                                <div class="font-extrabold text-slate-900">Bantuan</div>
                                <div class="text-slate-600 text-right">
                                    Klik tombol “Hubungi Admin” untuk mendapatkan konfirmasi lebih lanjut.
                                </div>
                            </div>

                            <div class="h-px bg-slate-200"></div>

                            <div class="flex items-start justify-between gap-3">
                                <div class="font-extrabold text-slate-900">Catatan</div>
                                <div class="text-slate-600 text-right">
                                    Pastikan dokumen yang diunggah jelas dan sesuai.
                                </div>
                            </div>

                            @if($waLink)
                                <div class="mt-4">
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener"
                                       class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 rounded-2xl border border-slate-200 bg-[#0194F3] text-white font-extrabold hover:bg-[#66b4e7] transition">
                                        Hubungi Admin
                                    </a>
                                    
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6">
                        <div class="text-sm font-extrabold text-amber-900">Perhatian</div>
                        <p class="mt-1 text-sm text-amber-800 leading-relaxed">
                            Jika kamu tidak menerima email pembaruan, periksa folder Spam/Promotions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-partner.auth-shell>
</x-guest-layout>
