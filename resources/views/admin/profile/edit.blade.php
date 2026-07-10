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

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Update Profil --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="font-extrabold text-slate-900">Edit Profil</div>
        <div class="text-sm text-slate-500 mt-1">Ubah nama dan email admin.</div>

        <form class="mt-4 space-y-4" method="POST" action="{{ route('admin.profile.update') }}">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Nama</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       required>
            </div>

            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       required>
            </div>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                    style="background:#0194F3"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'">
                Simpan Profil
            </button>
        </form>
    </div>

    {{-- Update Password --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="font-extrabold text-slate-900">Ubah Password</div>
        <div class="text-sm text-slate-500 mt-1">Wajib isi password saat ini.</div>

        <form class="mt-4 space-y-4" method="POST" action="{{ route('admin.profile.password') }}">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Password Saat Ini</label>
                <input type="password"
                       name="current_password"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       required>
            </div>

            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Password Baru</label>
                <input type="password"
                       name="password"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       required>
            </div>

            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Konfirmasi Password Baru</label>
                <input type="password"
                       name="password_confirmation"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       required>
            </div>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                    style="background:#0194F3"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'">
                Simpan Password
            </button>
        </form>
    </div>

</div>
@endsection
