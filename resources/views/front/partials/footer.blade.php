<footer class="relative overflow-hidden bg-slate-950 text-slate-100">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full blur-3xl opacity-35" style="background: radial-gradient(circle, #0194F3 0%, transparent 60%);"></div>
    </div>

    <div class="relative container mx-auto px-4 py-14">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10">

            {{-- Brand --}}
            <div class="lg:col-span-5">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Rumaindo" class="h-10 w-auto object-contain">
                    <span class="font-bold text-xl text-white">Rumaindo</span>
                </div>
                <p class="mt-5 text-slate-300 leading-relaxed max-w-md">
                    Portal properti terpercaya untuk jual beli dan sewa rumah, apartemen, ruko, tanah kavling, serta menemukan penyedia barang dan jasa kebutuhan properti di seluruh Indonesia.
                </p>
            </div>

            {{-- Quick Links --}}
            <div class="lg:col-span-3">
                <h3 class="text-sm font-semibold tracking-wider text-white/90 mb-4">Tautan Cepat</h3>
                <ul class="space-y-3 text-slate-300">
                    <li><a class="hover:text-white hover:underline decoration-[#0194F3]" href="{{ route('home') }}">Beranda</a></li>
                    <li><a class="hover:text-white hover:underline decoration-[#0194F3]" href="{{ route('properti') }}">Cari Properti</a></li>
                    <li><a class="hover:text-white hover:underline decoration-[#0194F3]" href="{{ route('barangjasa') }}">Barang & Jasa</a></li>
                    <li><a class="hover:text-white hover:underline decoration-[#0194F3]" href="{{ route('articles') }}">Artikel</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div class="lg:col-span-4">
                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md p-6 shadow-sm">
                    <h3 class="text-sm font-semibold tracking-wider text-white/90 mb-5">Hubungi Kami</h3>
                    <div class="space-y-4 text-slate-200">
                        <div class="flex gap-3">
                            <i data-lucide="map-pin" class="w-5 h-5 text-[#0194F3] mt-0.5 shrink-0"></i>
                            <div class="text-sm leading-snug">Jl. Sudirman No. 1, Jakarta</div>
                        </div>
                        <div class="flex gap-3">
                            <i data-lucide="phone" class="w-5 h-5 text-[#0194F3] shrink-0"></i>
                            <div class="text-sm leading-snug">021-12345678</div>
                        </div>
                        <div class="flex gap-3">
                            <i data-lucide="mail" class="w-5 h-5 text-[#0194F3] shrink-0"></i>
                            <div class="text-sm leading-snug">halo@rumaindo.com</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 border-t border-white/10 pt-6 text-center text-sm text-slate-400">
            © {{ date('Y') }} Rumaindo. All rights reserved.
        </div>
    </div>
</footer>