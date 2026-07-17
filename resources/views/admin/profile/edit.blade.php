@extends('layouts.admin')

@section('title', 'Profil Admin')
@section('page-title', 'Profil Admin')

@section('content')

@if(session('success'))
    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
        <div class="font-extrabold text-emerald-700">Sukses</div>
        <div class="text-sm mt-1 text-emerald-700">{{ session('success') }}</div>
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4">
        <div class="font-extrabold text-red-700 mb-2">Ada error validasi:</div>
        <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-5">

    {{-- Update Profil & Password --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 max-w-3xl">
        <div class="font-extrabold text-slate-900">Edit Profil & Password</div>
        <div class="text-sm text-slate-500 mt-1">Ubah nama, email, foto profil, dan password admin.</div>

        <form class="mt-6 space-y-6" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            @csrf

            {{-- Photo Profile --}}
            <div x-data="{ imageUrl: '{{ $user->avatar ? asset($user->avatar) : '' }}', fileChosen(event) { if(event.target.files.length > 0){ this.imageUrl = URL.createObjectURL(event.target.files[0]) } } }" class="flex flex-col mb-6 pb-6 border-b border-slate-100">
                <label for="avatar" class="relative group cursor-pointer block w-24 h-24">
                    <div class="w-24 h-24 rounded-2xl bg-slate-200 overflow-hidden border-4 border-slate-50 relative">
                        <template x-if="imageUrl">
                            <img :src="imageUrl" alt="Avatar" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!imageUrl">
                            <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                                <i data-lucide="user" class="w-8 h-8"></i>
                            </div>
                        </template>
                        <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center transition">
                            <i data-lucide="camera" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                    <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" @change="fileChosen">
                </label>
                <div class="text-xs text-slate-400 font-medium mt-2">Klik gambar untuk mengubah foto profil (Max 2MB)</div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Nama Lengkap</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Alamat Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20"
                           required>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 mt-2">
                <h3 class="text-sm font-extrabold text-slate-800 mb-4">Ubah Password (Opsional)</h3>
                <p class="text-xs text-slate-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-extrabold text-slate-800 mb-1">Password Baru</label>
                        <input type="password"
                               name="password"
                               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>

                    <div>
                        <label class="block text-sm font-extrabold text-slate-800 mb-1">Konfirmasi Password Baru</label>
                        <input type="password"
                               name="password_confirmation"
                               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-2.5 text-sm font-extrabold text-white transition shadow-lg shadow-blue-500/30"
                        style="background:#0194F3"
                        onmouseover="this.style.background='#0186DB'"
                        onmouseout="this.style.background='#0194F3'">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
