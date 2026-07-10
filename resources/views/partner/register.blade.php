<x-guest-layout>
    <x-partner.auth-shell
        title="Daftar Partner"
        subtitle="Lengkapi data berikut. Pastikan email dan nomor yang kamu isi aktif."
    >
        <div class="mb-5">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Form Pendaftaran</h2>
            <p class="mt-1 text-sm text-slate-600">
                Isi data dengan benar. Dokumen dibutuhkan untuk validasi.
            </p>
        </div>

        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form x-data="partnerUploadUI()" method="POST" action="{{ route('partner.register.store') }}"
              enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid lg:grid-cols-12 gap-6">

                {{-- MAIN FORM --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- DATA UTAMA (1 block, rapi) --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-extrabold text-slate-900">Data Utama</div>
                            <span class="text-xs font-extrabold text-slate-500">1 / 2</span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-label for="name" value="Nama Lengkap" />
                                <x-input id="name" class="w-full rounded-xl" type="text" name="name"
                                         value="{{ old('name') }}" required />
                            </div>

                            <div>
                                <x-label for="email" value="Email" />
                                <x-input id="email" class="w-full rounded-xl" type="email" name="email"
                                         value="{{ old('email') }}" required />
                            </div>

                            <div>
                                <x-label for="phone" value="No HP (WhatsApp)" />
                                <x-input id="phone" class="w-full rounded-xl" type="text" name="phone"
                                         value="{{ old('phone') }}" required />
                                <p class="mt-1 text-xs text-slate-500">Contoh: 08xxxxxxxxxx</p>
                            </div>

                            <div class="sm:col-span-2">
                                <x-label for="address" value="Alamat Lengkap" />
                                <textarea id="address" name="address" rows="3"
                                          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-slate-300 focus:ring-0"
                                          required>{{ old('address') }}</textarea>
                            </div>
{{-- TIPE PARTNER + DATA REKENING (ATAS-BAWAH, INPUT GEDE) --}}
<div class="sm:col-span-2">
    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
        <div class="text-sm font-extrabold text-slate-900">Data Partner & Rekening</div>
        <p class="mt-1 text-xs text-slate-500">Pilih tipe partner lalu isi data rekening untuk pembayaran.</p>

        {{-- Tipe Partner (ATAS) --}}
        <div class="mt-4">
            <x-label for="partner_type" value="Tipe Partner" />
            <select id="partner_type" name="partner_type"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-slate-300 focus:ring-0"
                    required>
                <option value="">-- Pilih tipe --</option>
                <option value="agency_paket_tour" {{ old('partner_type')==='agency_paket_tour' ? 'selected' : '' }}>
                    Agency Paket Tour
                </option>
               <option value="agency_rental_mobil" {{ old('partner_type')==='agency_rental_mobil' ? 'selected' : '' }}>
                    Agency Rental Mobil
                </option>
                <option value="agency_restoran" {{ old('partner_type')==='agency_restoran' ? 'selected' : '' }}>
                    Agency Restoran
                </option>
                <option value="agency_hotel_vila" {{ old('partner_type')==='agency_hotel_vila' ? 'selected' : '' }}>
                    Agency Hotel/Vila
                </option>

            </select>
        </div>

        {{-- Data Rekening (BAWAH, 1 baris 1 input, GEDE) --}}
        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
            <div class="text-sm font-extrabold text-slate-900">Data Rekening</div>
            <p class="mt-1 text-xs text-slate-500">Gunakan rekening milik sendiri/yang terdaftar.</p>

            <div class="mt-4 space-y-4">
                <div>
                    <x-label for="bank_name" value="Nama Bank" />
                    <x-input id="bank_name"
                             class="w-full rounded-xl"
                             name="bank_name"
                             value="{{ old('bank_name') }}"
                             placeholder="Contoh: BCA / BRI / Mandiri"
                             required />
                </div>

                <div>
                    <x-label for="bank_account_number" value="No Rekening" />
                    <x-input id="bank_account_number"
                             class="w-full rounded-xl"
                             name="bank_account_number"
                             value="{{ old('bank_account_number') }}"
                             placeholder="Contoh: 1234567890"
                             required />
                </div>

                <div>
                    <x-label for="bank_account_holder" value="Atas Nama" />
                    <x-input id="bank_account_holder"
                             class="w-full rounded-xl"
                             name="bank_account_holder"
                             value="{{ old('bank_account_holder') }}"
                             placeholder="Nama sesuai rekening"
                             required />
                </div>
            </div>

            <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                Pastikan data rekening benar
            </div>
        </div>
    </div>
</div>


                            <div class="sm:col-span-2">
                                <x-label for="reason" value="Alasan bergabung" />
                                <textarea id="reason" name="reason" rows="4"
                                          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-slate-300 focus:ring-0"
                                          required>{{ old('reason') }}</textarea>
                                <p class="mt-1 text-xs text-slate-500">
                                    Jelaskan singkat: pengalaman/kapasitas, area layanan, nilai tambah.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- PASSWORD (1 block) --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-extrabold text-slate-900">Keamanan Akun</div>
                            <span class="text-xs font-extrabold text-slate-500">2 / 2</span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-label for="password" value="Password" />
                                <x-input id="password" class="w-full rounded-xl" type="password" name="password" required />
                            </div>
                            <div>
                                <x-label for="password_confirmation" value="Konfirmasi Password" />
                                <x-input id="password_confirmation" class="w-full rounded-xl" type="password" name="password_confirmation" required />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SIDEBAR: DOKUMEN + ACTION (sticky desktop) --}}
                <div class="lg:col-span-4 space-y-4 lg:sticky lg:top-6 h-fit">

                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="text-sm font-extrabold text-slate-900">Dokumen</div>
                        <p class="mt-1 text-xs text-slate-500">Upload agar admin bisa memeriksa data.</p>

                        <div class="mt-4">
                            <label class="block text-sm font-bold text-slate-800 mb-1">Jenis Identitas</label>
                            @php $idv = old('identity_type'); @endphp
                            <select name="identity_type" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-800 focus:border-slate-300 focus:ring-0">
                                <option value="">-- Pilih --</option>
                                <option value="KTP" {{ $idv==='KTP'?'selected':'' }}>KTP</option>
                                <option value="SIM" {{ $idv==='SIM'?'selected':'' }}>SIM</option>
                                <option value="PASPOR" {{ $idv==='PASPOR'?'selected':'' }}>PASPOR</option>
                                <option value="KK" {{ $idv==='KK'?'selected':'' }}>KK</option>
                            </select>
                        </div>

                        {{-- Upload Identity --}}
                        <div class="mt-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-extrabold text-slate-900">Foto Identitas</div>
                                <span x-show="identityOk" x-cloak class="text-xs font-extrabold text-green-700 bg-green-100 px-2 py-1 rounded-full">
                                    ✓ OK
                                </span>
                            </div>
                            <div class="mt-2">
                                <label class="block rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 cursor-pointer hover:bg-slate-100 transition">
                                    <input type="file" name="identity_file" required class="hidden"
                                           accept=".jpg,.jpeg,.png,.pdf"
                                           @change="pick($event, 'identity')">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-2xl bg-white border border-slate-200 grid place-items-center font-extrabold"
                                             style="color:#0194F3;">↑</div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-extrabold text-slate-900">Pilih file</div>
                                            <div class="text-xs text-slate-600 truncate" x-text="identityName || 'Belum ada file'"></div>
                                        </div>
                                    </div>
                                </label>
                                <p class="mt-2 text-[11px] text-slate-500">JPG/PNG/PDF, max 5MB.</p>
                            </div>
                        </div>

                       <div class="mt-4">
    <div class="flex items-center justify-between">
        <div class="text-sm font-extrabold text-slate-900">Dokumen Legalitas NIB (PDF)</div>
        <span x-show="legalOk" x-cloak class="text-xs font-extrabold text-green-700 bg-green-100 px-2 py-1 rounded-full">
            ✓ OK
        </span>
    </div>

    <div class="mt-2">
        <label class="block rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 cursor-pointer hover:bg-slate-100 transition">
            <input type="file" name="legal_document" required class="hidden"
                   accept=".pdf"
                   @change="pick($event, 'legal')">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-2xl bg-white border border-slate-200 grid place-items-center font-extrabold"
                     style="color:#0194F3;">↑</div>
                <div class="min-w-0">
                    <div class="text-sm font-extrabold text-slate-900">Pilih file</div>
                    <div class="text-xs text-slate-600 truncate" x-text="legalName || 'Belum ada file'"></div>
                </div>
            </div>
        </label>
        <p class="mt-2 text-[11px] text-slate-500">PDF, max 10MB.</p>
    </div>
</div>


                        <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            <div class="font-extrabold">Catatan</div>
                            <ul class="list-disc pl-5 mt-2 space-y-1 text-xs">
                                <li>Foto tidak blur / tidak terpotong.</li>
                                <li>Wajah & identitas harus jelas.</li>
                            </ul>
                        </div>
                    </div>

                    {{-- ACTION --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <button type="submit"
                                class="w-full px-6 py-3 rounded-2xl text-white font-extrabold hover:opacity-95"
                                style="background:#0194F3;">
                            Kirim Pendaftaran
                        </button>

                        <a href="{{ route('login') }}"
                           class="mt-3 block text-center text-sm font-semibold text-[#0194F3] hover:underline">
                            Sudah punya akun? Login
                        </a>
                    </div>
                </div>

            </div>
        </form>

        <script>
    function partnerUploadUI() {
        return {
            identityName: '',
            identityOk: false,

            legalName: '',
            legalOk: false,

            pick(e, type) {
                const f = e.target?.files?.[0] || null;
                if (!f) return;

                if (type === 'identity') {
                    this.identityName = f.name;
                    this.identityOk = true;
                    return;
                }

                if (type === 'legal') {
                    this.legalName = f.name;
                    this.legalOk = true;
                    return;
                }
            }
        }
    }
</script>


    </x-partner.auth-shell>
</x-guest-layout>
