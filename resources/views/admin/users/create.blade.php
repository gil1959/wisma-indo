@extends('layouts.admin')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('content')
<div class="space-y-5" x-data="userCreateUI()">

    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Tambah User</h2>
            <p class="mt-1 text-sm text-slate-600">Admin bisa membuat user baru dan memilih role.</p>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="px-4 py-2.5 rounded-2xl font-extrabold border border-slate-200 text-slate-700 hover:bg-slate-50">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
            <div class="font-extrabold mb-2">Periksa input:</div>
            <ul class="list-disc ml-5 text-sm font-semibold">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}"
          class="rounded-2xl border border-slate-200 bg-white p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800" required>
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800" required>
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">No HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Password</label>
                <input type="password" name="password"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800" required>
                <div class="mt-1 text-xs text-slate-500 font-semibold">Min 6 karakter.</div>
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Role</label>
                <select name="role" x-model="role"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800" required>
                    <option value="">-- pilih --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r }}" {{ old('role')===$r ? 'selected' : '' }}>
                            {{ strtoupper($r) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="is_verified" value="1"
                       {{ old('is_verified') ? 'checked' : '' }}
                       class="w-5 h-5">
                <div>
                    <div class="font-extrabold text-slate-900">Email Verified</div>
                    <div class="text-xs text-slate-500 font-semibold">Centang jika mau langsung verified.</div>
                </div>
            </div>
        </div>

        <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Alamat</label>
            <textarea name="address" rows="3"
                      class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">{{ old('address') }}</textarea>
        </div>

        <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Kelurahan/Kecamatan</label>
            <input type="text" name="sub_district" value="{{ old('sub_district') }}"
                   class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
        </div>

        {{-- Partner fields --}}
        <div x-show="role === 'partner'" x-cloak class="rounded-2xl border border-slate-200 bg-slate-50 p-5 space-y-4">
            <div class="text-sm font-extrabold text-slate-900">Data Partner</div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Tipe Partner</label>
                <select name="partner_type"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                    <option value="">-- pilih --</option>
                    <option value="agency_paket_tour" {{ old('partner_type')==='agency_paket_tour'?'selected':'' }}>Agency Paket Tour</option>
                    <option value="agency_rental_mobil" {{ old('partner_type')==='agency_rental_mobil'?'selected':'' }}>Agency Rental Mobil</option>
                    <option value="agency_restoran" {{ old('partner_type')==='agency_restoran'?'selected':'' }}>Agency Restoran</option>
                    <option value="agency_hotel_vila" {{ old('partner_type')==='agency_hotel_vila'?'selected':'' }}>Agency Hotel/Vila</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="text-xs font-extrabold text-slate-600 uppercase">Nama Bank</label>
                    <input type="text" name="partner_bank_name" value="{{ old('partner_bank_name') }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                </div>
                <div>
                    <label class="text-xs font-extrabold text-slate-600 uppercase">No Rekening</label>
                    <input type="text" name="partner_bank_account_number" value="{{ old('partner_bank_account_number') }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                </div>
                <div>
                    <label class="text-xs font-extrabold text-slate-600 uppercase">Atas Nama</label>
                    <input type="text" name="partner_bank_account_holder" value="{{ old('partner_bank_account_holder') }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                </div>
            </div>
        </div>

        {{-- Site moderator permissions --}}
        <div x-show="role === 'site_moderator'" x-cloak class="rounded-2xl border border-slate-200 bg-slate-50 p-5 space-y-3">
            <div class="text-sm font-extrabold text-slate-900">Akses Site Moderator</div>
            <div class="text-xs text-slate-600 font-semibold">Centang fitur admin yang boleh diakses.</div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                @foreach($permissions as $p)
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                        <input type="checkbox" name="permissions[]" value="{{ $p['name'] }}"
                               {{ in_array($p['name'], old('permissions', [])) ? 'checked' : '' }}
                               class="w-5 h-5">
                        <div class="min-w-0">
                            <div class="text-sm font-extrabold text-slate-900">{{ $p['label'] }}</div>
                            <div class="text-[11px] font-semibold text-slate-500">{{ $p['name'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2.5 rounded-2xl font-extrabold border border-slate-200 text-slate-700 hover:bg-slate-50">
                Batal
            </a>
            <button class="px-5 py-2.5 rounded-2xl font-extrabold text-white"
                    style="background:#0194F3;">
                Simpan
            </button>
        </div>
    </form>

</div>

<script>
function userCreateUI() {
    return {
        role: @json(old('role', '')),
    }
}
</script>
@endsection
