@extends('layouts.admin')

@section('content')
<div class="max-w-5xl space-y-5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Edit User Partner</h1>
            <p class="mt-1 text-sm text-slate-600">Edit semua data partner + role + tipe agency.</p>
        </div>

        <a href="{{ route('admin.partners.users.show', $user->id) }}"
           class="px-4 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50">
            Batal
        </a>
    </div>

    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 font-bold">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.partners.users.update', $user->id) }}"
          class="rounded-3xl border border-slate-200 bg-white p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Nama</label>
                <input name="name" value="{{ old('name', $user->name) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">
                @error('name') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Email</label>
                <input name="email" type="email" value="{{ old('email', $user->email) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">
                @error('email') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">No HP</label>
                <input name="phone" value="{{ old('phone', $user->phone) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">
                @error('phone') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Role</label>
                <select name="role"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-extrabold text-slate-900 bg-white">
                    @php $current = $user->getRoleNames()->first() ?? 'partner'; @endphp
                  
  @foreach($roles as $roleName)
    <option value="{{ $roleName }}" @selected(old('role', $currentRole) == $roleName)>
      {{ strtoupper($roleName) }}
    </option>
  @endforeach


                </select>
                @error('role') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="text-xs font-extrabold text-slate-600 uppercase">Alamat (address)</label>
                <textarea name="address" rows="3"
                          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">{{ old('address', $user->address) }}</textarea>
                @error('address') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="text-xs font-extrabold text-slate-600 uppercase">Alamat Lengkap (full_address)</label>
                <textarea name="full_address" rows="3"
                          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">{{ old('full_address', $user->full_address) }}</textarea>
                @error('full_address') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Sub District</label>
                <input name="sub_district" value="{{ old('sub_district', $user->sub_district) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">
                @error('sub_district') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                
                <div>
    <label class="text-xs font-extrabold text-slate-600 uppercase">Tipe Agency (partner_type)</label>

    @php
        $pt = old('partner_type', $user->partner_type);
    @endphp

    <select name="partner_type"
            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-extrabold text-slate-900 bg-white">
        <option value="" {{ $pt ? '' : 'selected' }}>-</option>
        <option value="agency_paket_tour" {{ $pt == 'agency_paket_tour' ? 'selected' : '' }}>
            AGENCY PAKET TOUR
        </option>
        <option value="agency_rental_mobil" {{ $pt == 'agency_rental_mobil' ? 'selected' : '' }}>
            AGENCY RENTAL MOBIL
        </option>
        <option value="agency_restoran" {{ $pt == 'agency_restoran' ? 'selected' : '' }}>
            AGENCY RESTORAN
        </option>
        <option value="agency_hotel_vila" {{ $pt == 'agency_hotel_vila' ? 'selected' : '' }}>
            AGENCY HOTEL/VILA
        </option>

    </select>

    @error('partner_type') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

                @error('partner_type') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Pajak Partner (%)</label>
                <input name="partner_tax_percent" type="number" step="0.01" min="0" max="100"
                       value="{{ old('partner_tax_percent', $user->partner_tax_percent) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">
                @error('partner_tax_percent') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Nama Bank</label>
                <input name="partner_bank_name" value="{{ old('partner_bank_name', $user->partner_bank_name) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">
                @error('partner_bank_name') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">No Rekening</label>
                <input name="partner_bank_account_number" value="{{ old('partner_bank_account_number', $user->partner_bank_account_number) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">
                @error('partner_bank_account_number') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Atas Nama</label>
                <input name="partner_bank_account_holder" value="{{ old('partner_bank_account_holder', $user->partner_bank_account_holder) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">
                @error('partner_bank_account_holder') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Password (opsional)</label>
                <input name="password" type="password" placeholder="Isi kalau mau ganti"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-900">
                @error('password') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <button class="px-5 py-3 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
