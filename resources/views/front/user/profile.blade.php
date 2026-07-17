@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Edit Profil</h1>
                <p class="text-slate-500 text-sm sm:text-base">Perbarui informasi profil dan kontak Anda.</p>
            </div>
            <a href="{{ route('akun') }}" class="flex items-center gap-2 text-[#0194F3] font-bold text-sm bg-[#0194F3]/10 hover:bg-[#0194F3]/20 px-4 py-2 rounded-xl transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Dashboard
            </a>
        </div>



        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Photo Profile --}}
                <div x-data="{ imageUrl: '{{ $user->avatar ? asset($user->avatar) : '' }}', fileChosen(event) { if(event.target.files.length > 0){ this.imageUrl = URL.createObjectURL(event.target.files[0]) } } }" class="flex flex-col items-center mb-8 pb-8 border-b border-slate-100">
                    <label for="avatar" class="relative group cursor-pointer block">
                        <div class="w-24 h-24 rounded-full bg-slate-200 mb-2 overflow-hidden border-4 border-slate-50 relative">
                            <template x-if="imageUrl">
                                <img :src="imageUrl" alt="Avatar" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!imageUrl">
                                <svg class="w-full h-full text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </template>
                            <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center transition">
                                <i data-lucide="camera" class="w-6 h-6 text-white"></i>
                            </div>
                        </div>
                        <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" @change="fileChosen">
                    </label>
                    <div class="text-xs text-slate-400 font-medium">Klik untuk mengubah foto profil (Max 2MB)</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] transition text-sm">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] transition text-sm">
                        <div class="mt-2 p-2 rounded-lg bg-orange-50 border border-orange-100 text-xs text-orange-600 font-medium flex items-start gap-1.5">
                            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                            Jika diubah, Anda akan logout otomatis dan wajib verifikasi via link di email baru Anda.
                        </div>
                    </div>

                    {{-- No HP --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Whatsapp</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] transition text-sm">
                    </div>

                    {{-- Lokasi / Kecamatan --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi (Kecamatan / Kota)</label>
                        <input type="text" name="sub_district" value="{{ old('sub_district', $user->sub_district) }}" placeholder="Contoh: Klojen, Malang"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] transition text-sm">
                    </div>

                    {{-- Alamat --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Lengkap</label>
                        <textarea name="full_address" rows="3" placeholder="Masukkan alamat lengkap..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] transition text-sm">{{ old('full_address', $user->full_address) }}</textarea>
                    </div>

                    {{-- Bio --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Bio Singkat</label>
                        <textarea name="bio" rows="3" placeholder="Ceritakan sedikit tentang Anda..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] transition text-sm">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <div class="md:col-span-2 pt-4 border-t border-slate-100 mt-2">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Ubah Password (Opsional)</h3>
                        <p class="text-xs text-slate-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Password Baru</label>
                                <input type="password" name="password" placeholder="Minimal 8 karakter"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] transition text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" placeholder="Ketik ulang password baru"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] transition text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 mt-6 border-t border-slate-100">
                    <button type="submit" class="bg-[#0194F3] hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-blue-500/30 flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
