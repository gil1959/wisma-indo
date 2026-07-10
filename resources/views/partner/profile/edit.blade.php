@extends('partner.layouts.app')

@section('title', 'Profile')
@section('page-subtitle', 'Settings')
@section('page-title', 'Profile Partner')

@section('content')
<div class="max-w-5xl space-y-5">

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="font-extrabold">Ada error:</div>
            <ul class="list-disc pl-5 mt-2 text-sm space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('partner.profile.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Informasi Akun</h2>
                    <p class="text-sm text-slate-500 mt-1">Data ini dipakai untuk transaksi dan komunikasi.</p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-extrabold"
                      style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <i data-lucide="user" class="w-4 h-4" style="color:#0194F3;"></i>
                    Profile
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-600 mb-1">Nama</label>
                    <input name="name" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           value="{{ old('name', $user->name) }}" required>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-600 mb-1">Email</label>
                    <input name="email" type="email" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           value="{{ old('email', $user->email) }}" required>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-600 mb-1">No HP (WhatsApp)</label>
                    <input name="phone" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                    <div class="text-xs text-slate-500 mt-1">Dipakai untuk tombol “Hubungi Partner” di order user.</div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-600 mb-1">Sub District</label>
                    <input name="sub_district" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           value="{{ old('sub_district', $user->sub_district) }}" placeholder="Kecamatan">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-extrabold text-slate-600 mb-1">Alamat</label>
                    <textarea name="full_address" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                              placeholder="Alamat lengkap">{{ old('full_address', $user->full_address) }}</textarea>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Data Rekening</h2>
                    <p class="text-sm text-slate-500 mt-1">Untuk kebutuhan payout/settlement (kalau ada).</p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-extrabold"
                      style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <i data-lucide="credit-card" class="w-4 h-4" style="color:#0194F3;"></i>
                    Bank
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-600 mb-1">Nama Bank</label>
                    <input name="partner_bank_name" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           value="{{ old('partner_bank_name', $user->partner_bank_name) }}" placeholder="BCA / Mandiri / BRI">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-600 mb-1">No Rekening</label>
                    <input name="partner_bank_account_number" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           value="{{ old('partner_bank_account_number', $user->partner_bank_account_number) }}" placeholder="1234567890">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-600 mb-1">Atas Nama</label>
                    <input name="partner_bank_account_holder" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           value="{{ old('partner_bank_account_holder', $user->partner_bank_account_holder) }}" placeholder="Nama pemilik rekening">
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-extrabold text-white transition"
                        style="background:#0194F3;"
                        onmouseover="this.style.background='#0186DB'"
                        onmouseout="this.style.background='#0194F3'">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Profile
                </button>
            </div>
        </div>
    </form>

    <form method="POST" action="{{ route('partner.profile.password') }}" class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 space-y-4">
        @csrf
        @method('PUT')

        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Keamanan</h2>
                <p class="text-sm text-slate-500 mt-1">Ubah password dengan verifikasi password lama.</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-extrabold"
                  style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                <i data-lucide="key-round" class="w-4 h-4" style="color:#0194F3;"></i>
                Security
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-extrabold text-slate-600 mb-1">Password Lama</label>
                <input name="current_password" type="password" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-extrabold text-slate-600 mb-1">Password Baru</label>
                <input name="password" type="password" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-extrabold text-slate-600 mb-1">Ulangi Password Baru</label>
                <input name="password_confirmation" type="password" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-extrabold text-white transition"
                    style="background:#111827;"
                    onmouseover="this.style.background='#0b1220'"
                    onmouseout="this.style.background='#111827'">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                Update Password
            </button>
        </div>
    </form>

</div>
@endsection
