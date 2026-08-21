<x-guest-layout>
    @php $isEn = app()->getLocale() === 'en'; @endphp
    <div class="min-h-screen flex items-center justify-center p-4 py-12">

        {{-- CARD WRAPPER --}}
        <div class="w-full max-w-5xl lg:max-w-6xl flex flex-col lg:flex-row bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">

            {{-- LEFT / BRAND (Blue Box) --}}
            <section class="relative overflow-hidden bg-gradient-to-br from-[#0194F3] to-[#027DD1] text-white lg:w-5/12 flex flex-col p-8 lg:p-12">

                {{-- pattern --}}
                <div class="absolute inset-0 opacity-20"
                    style="background-image: radial-gradient(circle at top left, white 1px, transparent 1px); background-size: 28px 28px;">
                </div>

                {{-- glow --}}
                <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full blur-3xl opacity-25"
                    style="background: radial-gradient(circle, rgba(255,255,255,0.65) 0%, transparent 60%);"></div>

                {{-- content --}}
                <div class="relative z-10 flex flex-col h-full">
                    {{-- top --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3">
                            <img src="{{ isset($siteSettings['site_logo']) && $siteSettings['site_logo'] != '' ? asset($siteSettings['site_logo']) : asset('images/logo.png') }}"
                                alt="{{ $siteSettings['brand_name'] ?? 'Wismaindo' }}"
                                class="h-10 lg:h-12 w-auto object-contain">
                            <span class="text-2xl font-bold tracking-tight">{{ $siteSettings['brand_name'] ?? 'Wismaindo' }}<br><span class="text-lg font-medium opacity-90">Partner Program</span></span>
                        </div>
                    </div>

                    {{-- middle --}}
                    <div class="py-4 space-y-6">
                        <h1 class="text-3xl lg:text-4xl font-extrabold leading-tight mb-2">
                            {{ $isEn ? 'Register as Partner' : 'Daftar Partner' }}
                        </h1>
                        <p class="text-white/85 text-sm lg:text-base leading-relaxed">
                            {{ $isEn ? 'Complete the following data. Make sure the email and phone number are active.' : 'Lengkapi data berikut. Pastikan email dan nomor yang kamu isi aktif.' }}
                        </p>

                        <div class="space-y-4 mt-8">
                            <div class="bg-white/10 rounded-2xl p-4 border border-white/20 flex items-start gap-3 backdrop-blur-sm">
                                <div class="mt-1 h-8 w-8 rounded-full bg-white/20 flex items-center justify-center shrink-0 text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm">Akses Panel Partner</h3>
                                    <p class="text-xs text-white/80 mt-1">Kelola data akun, status, dan kebutuhan operasional melalui dashboard partner.</p>
                                </div>
                            </div>
                            
                            <div class="bg-white/10 rounded-2xl p-4 border border-white/20 flex items-start gap-3 backdrop-blur-sm">
                                <div class="mt-1 h-8 w-8 rounded-full bg-white/20 flex items-center justify-center shrink-0 text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm">Verifikasi & Keamanan</h3>
                                    <p class="text-xs text-white/80 mt-1">Data partner diverifikasi admin untuk memastikan keamanan dan kualitas layanan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- bottom (desktop only) --}}
                    <div class="hidden lg:block mt-auto pt-8 text-sm text-white/75">
                        © {{ date('Y') }} {{ $siteSettings['brand_name'] ?? 'Wismaindo' }}
                    </div>
                </div>
            </section>

            {{-- RIGHT / FORM (White Box) --}}
            <section class="lg:w-7/12 flex items-center justify-center p-6 lg:p-10 relative z-20 bg-slate-50">
                <div class="w-full bg-white p-6 lg:p-8 rounded-3xl shadow-sm border border-slate-100">

                    <div class="mb-6 border-b border-slate-100 pb-4">
                        <h2 class="text-2xl font-bold text-slate-900 mb-1">
                            {{ $isEn ? 'Registration Form' : 'Form Pendaftaran' }}
                        </h2>
                        <p class="text-sm text-slate-500">
                            {{ $isEn ? 'Fill in the data correctly. Documents are required for validation.' : 'Isi data dengan benar. Dokumen dibutuhkan untuk validasi.' }}
                        </p>
                    </div>

                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('partner.register.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="space-y-8">
                            {{-- Data Utama --}}
                            <div class="space-y-4">
                                <h3 class="text-sm font-bold text-slate-700 bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">1. Data Utama</h3>
                                
                                <div>
                                    <x-label for="name" :value="$isEn ? 'Full Name' : 'Nama Lengkap'" />
                                    <x-input id="name" class="block mt-1 w-full rounded-xl text-sm"
                                        type="text" name="name"
                                        value="{{ old('name') }}" required autofocus />
                                </div>

                                <div>
                                    <x-label for="email" value="Email" />
                                    <x-input id="email" class="block mt-1 w-full rounded-xl text-sm"
                                        type="email" name="email"
                                        value="{{ old('email') }}" required />
                                </div>

                                <div>
                                    <x-label for="phone" :value="$isEn ? 'Phone Number / WhatsApp' : 'Nomor HP / WhatsApp'" />
                                    <x-input id="phone" class="block mt-1 w-full rounded-xl text-sm"
                                        type="text" name="phone"
                                        value="{{ old('phone') }}" required />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <x-label for="password" value="Password" />
                                        <x-input id="password" class="block mt-1 w-full rounded-xl text-sm"
                                            type="password" name="password" required />
                                    </div>

                                    <div>
                                        <x-label for="password_confirmation" :value="$isEn ? 'Confirm Password' : 'Confirm'" />
                                        <x-input id="password_confirmation" class="block mt-1 w-full rounded-xl text-sm"
                                            type="password" name="password_confirmation" required />
                                    </div>
                                </div>
                            </div>

                            {{-- Dokumen --}}
                            <div class="space-y-4">
                                <h3 class="text-sm font-bold text-slate-700 bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">2. Dokumen Verifikasi</h3>
                                
                                <div>
                                    <x-label for="ktp_file" :value="$isEn ? 'Upload KTP' : 'Upload KTP'" />
                                    <div class="mt-1 relative border-2 border-dashed border-slate-200 rounded-xl hover:border-blue-400 bg-slate-50 transition cursor-pointer text-center group overflow-hidden min-h-[120px] flex items-center justify-center">
                                        <input id="ktp_file" name="ktp_file" type="file" required accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(this, 'ktp_preview', 'ktp_icon_container', 'ktp_filename')">
                                        <div id="ktp_icon_container" class="flex flex-col items-center justify-center pointer-events-none p-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 group-hover:text-[#0194F3] mb-2"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path><path d="M12 12v9"></path><path d="m16 16-4-4-4 4"></path></svg>
                                            <span class="text-sm font-bold text-slate-700 group-hover:text-[#0194F3]">Pilih file</span>
                                        </div>
                                        <div id="ktp_preview_container" class="hidden absolute inset-0 w-full h-full bg-slate-100 flex flex-col items-center justify-center p-2">
                                            <img id="ktp_preview" class="hidden max-h-[90%] max-w-full object-contain rounded" src="" alt="Preview">
                                            <div id="ktp_file_preview" class="hidden flex flex-col items-center text-slate-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                                <span id="ktp_filename" class="text-xs font-bold mt-2 truncate max-w-[200px]">filename.pdf</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, PDF (Max 5MB)</p>
                                </div>

                                <div>
                                    <x-label for="nib_file" :value="$isEn ? 'Upload NIB' : 'Upload NIB'" />
                                    <div class="mt-1 relative border-2 border-dashed border-slate-200 rounded-xl hover:border-blue-400 bg-slate-50 transition cursor-pointer text-center group overflow-hidden min-h-[120px] flex items-center justify-center">
                                        <input id="nib_file" name="nib_file" type="file" required accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(this, 'nib_preview', 'nib_icon_container', 'nib_filename')">
                                        <div id="nib_icon_container" class="flex flex-col items-center justify-center pointer-events-none p-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 group-hover:text-[#0194F3] mb-2"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path><path d="M12 12v9"></path><path d="m16 16-4-4-4 4"></path></svg>
                                            <span class="text-sm font-bold text-slate-700 group-hover:text-[#0194F3]">Pilih file</span>
                                        </div>
                                        <div id="nib_preview_container" class="hidden absolute inset-0 w-full h-full bg-slate-100 flex flex-col items-center justify-center p-2">
                                            <img id="nib_preview" class="hidden max-h-[90%] max-w-full object-contain rounded" src="" alt="Preview">
                                            <div id="nib_file_preview" class="hidden flex flex-col items-center text-slate-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                                <span id="nib_filename" class="text-xs font-bold mt-2 truncate max-w-[200px]">filename.pdf</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, PDF (Max 5MB)</p>
                                </div>



                                <div>
                                    <x-label for="npwp_file" :value="$isEn ? 'Upload NPWP (Optional)' : 'Upload NPWP (Opsional)'" />
                                    <input id="npwp_file" name="npwp_file" type="file" accept=".jpg,.jpeg,.png,.pdf" class="w-full mt-1 border border-slate-200 rounded-xl p-2 text-sm focus:ring focus:ring-blue-200 focus:border-blue-400 bg-slate-50">
                                </div>

                                <div>
                                    <x-label for="lisensi_file" :value="$isEn ? 'Upload Agent License (Optional)' : 'Upload Lisensi Agen (Opsional)'" />
                                    <input id="lisensi_file" name="lisensi_file" type="file" accept=".jpg,.jpeg,.png,.pdf" class="w-full mt-1 border border-slate-200 rounded-xl p-2 text-sm focus:ring focus:ring-blue-200 focus:border-blue-400 bg-slate-50">
                                </div>
                            </div>
                        </div>

                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 flex items-start gap-3 mt-4">
                            <i data-lucide="info" class="w-5 h-5 text-amber-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-amber-800">
                                <p class="font-bold mb-1">Catatan</p>
                                <ul class="list-disc ml-4 space-y-1">
                                    <li>Foto KTP dan Wajah harus jelas, tidak buram, dan tidak terpotong.</li>
                                    <li>Pastikan nomor HP/WhatsApp aktif agar dapat dihubungi oleh calon pembeli.</li>
                                    <li>Setelah mendaftar, akun Anda akan direview oleh Admin sebelum bisa menggunakan fitur partner.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
                            <a href="{{ route('login') }}" class="text-[#0194F3] hover:underline text-center sm:text-left text-sm font-medium order-2 sm:order-1">
                                {{ $isEn ? 'Already have an account? Sign in' : 'Sudah punya akun? Login' }}
                            </a>

                            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-[#0194F3] text-white font-bold rounded-xl shadow-lg hover:bg-blue-600 transition order-1 sm:order-2">
                                {{ $isEn ? 'Register as Partner' : 'Daftar Sekarang' }}
                            </button>
                        </div>
                    </form>
                </div>
            </section>

        </div>
    </div>

    <script>
        function previewImage(input, imgId, iconContainerId, filenameId) {
            const iconContainer = document.getElementById(iconContainerId);
            const previewContainer = document.getElementById(imgId).parentElement;
            const imgPreview = document.getElementById(imgId);
            const filePreview = document.getElementById(imgId.replace('preview', 'file_preview'));
            const filenameLabel = document.getElementById(filenameId);
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                iconContainer.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imgPreview.src = e.target.result;
                        imgPreview.classList.remove('hidden');
                        filePreview.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);
                } else {
                    imgPreview.classList.add('hidden');
                    filePreview.classList.remove('hidden');
                    filenameLabel.innerText = file.name;
                }
            } else {
                iconContainer.classList.remove('hidden');
                previewContainer.classList.add('hidden');
                imgPreview.src = "";
            }
        }
    </script>
</x-guest-layout>
