@extends('layouts.admin')
@section('title', 'Edit Akun Tabungan Umrah')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-extrabold text-slate-900">Edit Akun Tabungan Umrah</div>
            <div class="mt-1 text-sm text-slate-600 font-bold">
                Edit data user + data tabungan. Password opsional (kosongkan jika tidak diganti).
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tabungan-umrah.accounts.show', $account->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <div class="font-extrabold text-rose-700">Validasi gagal</div>
            <ul class="mt-2 text-sm text-rose-700 font-bold list-disc pl-5 space-y-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.tabungan-umrah.accounts.update', $account->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- USER --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div class="font-extrabold text-slate-900">Data User</div>
                <div class="text-xs text-slate-500 font-bold">User ID: {{ $account->user_id }}</div>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm font-extrabold text-slate-700">Nama</div>
                    <input name="user_name" value="{{ old('user_name', $account->user->name) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white" required>
                </div>

                <div>
                    <div class="text-sm font-extrabold text-slate-700">Email</div>
                    <input type="email" name="user_email" value="{{ old('user_email', $account->user->email) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white" required>
                </div>

                <div>
                    <div class="text-sm font-extrabold text-slate-700">Password Baru (opsional)</div>
                    <input type="password" name="user_password"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white"
                           placeholder="Kosongkan jika tidak diganti">
                </div>

                <div>
                    <div class="text-sm font-extrabold text-slate-700">Konfirmasi Password Baru</div>
                    <input type="password" name="user_password_confirmation"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white"
                           placeholder="Ulangi password baru">
                </div>

                <div>
                    <div class="text-sm font-extrabold text-slate-700">Phone</div>
                    <input name="user_phone" value="{{ old('user_phone', $account->user->phone) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white">
                </div>

                <div>
                    <div class="text-sm font-extrabold text-slate-700">Address</div>
                    <input name="user_address" value="{{ old('user_address', $account->user->address) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white">
                </div>

                <div class="md:col-span-2">
                    <div class="text-sm font-extrabold text-slate-700">Full Address</div>
                    <input name="user_full_address" value="{{ old('user_full_address', $account->user->full_address) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white">
                </div>

                <div class="md:col-span-2">
                    <div class="text-sm font-extrabold text-slate-700">Sub District</div>
                    <input name="user_sub_district" value="{{ old('user_sub_district', $account->user->sub_district) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white">
                </div>
            </div>
        </div>

        {{-- TABUNGAN --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div class="font-extrabold text-slate-900">Data Tabungan Umrah</div>
                <div class="text-xs text-slate-500 font-bold">Account ID: {{ $account->id }}</div>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm font-extrabold text-slate-700">Nama (Tabungan)</div>
                    <input name="account_full_name" value="{{ old('account_full_name', $account->full_name) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white" required>
                </div>

                <div>
                    <div class="text-sm font-extrabold text-slate-700">WhatsApp</div>
                    <input name="account_whatsapp" value="{{ old('account_whatsapp', $account->whatsapp) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white" required>
                </div>

                <div>
                    <div class="text-sm font-extrabold text-slate-700">Jenis</div>
                    <select name="account_saving_type"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-white font-bold">
                        <option value="umroh_reguler" {{ old('account_saving_type', $account->saving_type) === 'umroh_reguler' ? 'selected' : '' }}>Umroh Reguler</option>
                        <option value="haji_furoda" {{ old('account_saving_type', $account->saving_type) === 'haji_furoda' ? 'selected' : '' }}>Haji Furoda</option>
                    </select>
                </div>

                <div>
                    <div class="text-sm font-extrabold text-slate-700">Status</div>
                    <select name="account_status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-white font-bold">
                        @php($st = old('account_status', $account->status))
                        <option value="pending" {{ $st==='pending'?'selected':'' }}>pending</option>
                        <option value="verified" {{ $st==='verified'?'selected':'' }}>verified</option>
                        <option value="rejected" {{ $st==='rejected'?'selected':'' }}>rejected</option>
                        <option value="suspended" {{ $st==='suspended'?'selected':'' }}>suspended</option>
                    </select>
                </div>

                <div>
                    <div class="text-sm font-extrabold text-slate-700">Target (Rp)</div>
                    <input type="number" name="target_amount" min="1000"
                           value="{{ old('target_amount', $account->target_amount) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white">
                </div>

                <div>
                    <div class="text-sm font-extrabold text-slate-700">Tanggal Keberangkatan</div>
                    <input type="date" name="target_departure_date"
                           value="{{ old('target_departure_date', $account->target_departure_date ? $account->target_departure_date->format('Y-m-d') : '') }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white">
                </div>

                <div class="md:col-span-2">
                    <div class="text-sm font-extrabold text-slate-700">Alasan Reject (jika status rejected)</div>
                    <textarea name="rejected_reason" rows="4"
                              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white"
                              placeholder="Isi alasan jika status rejected">{{ old('rejected_reason', $account->rejected_reason) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.tabungan-umrah.accounts.show', $account->id) }}"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
                Batal
            </a>

            <button class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-brand-500 text-white font-extrabold hover:bg-brand-600">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection
