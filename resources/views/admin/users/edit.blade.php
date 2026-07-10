@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="space-y-5" x-data="userEditUI()">

    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Edit User</h2>
            <p class="mt-1 text-sm text-slate-600">Admin bisa edit data user termasuk password</p>
        </div>

        <a href="{{ route('admin.users.show', $user) }}"
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

    <form method="POST" action="{{ route('admin.users.update', $user) }}"
          class="rounded-2xl border border-slate-200 bg-white p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">No HP</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Password (opsional)</label>
                <input type="password" name="password" value=""
                       placeholder="Isi jika ingin ganti password"
                       class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                <div class="mt-1 text-xs text-slate-500 font-semibold">
                    Kalau dikosongkan, password tidak berubah.
                </div>
            </div>
            <div>
  <label class="text-xs font-extrabold text-slate-600 uppercase">Role</label>
  <select name="role" x-model="role"
          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
      @foreach($roles as $r)
          <option value="{{ $r }}" {{ old('role', $currentRole) === $r ? 'selected' : '' }}>
              {{ strtoupper($r) }}
          </option>
      @endforeach
  </select>
</div>

<div class="flex items-center gap-3 pt-6">
  <input type="checkbox" name="is_verified" value="1"
         {{ old('is_verified', $user->email_verified_at ? 1 : 0) ? 'checked' : '' }}
         class="w-5 h-5">
  <div>
    <div class="font-extrabold text-slate-900">Email Verified</div>
    <div class="text-xs text-slate-500 font-semibold">Centang untuk verify, lepas untuk unverifikasi</div>
  </div>
</div>

        </div>

        <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Alamat Lengkap</label>
           <textarea name="address" rows="4"
 class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">{{ old('address', $user->address ?? $user->full_address) }}</textarea>

        </div>

        <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Kelurahan/Kecamatan</label>
            <input type="text" name="sub_district" value="{{ old('sub_district', $user->sub_district) }}"
                   class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
        </div>
        <div x-show="role === 'site_moderator'" x-cloak class="rounded-2xl border border-slate-200 bg-slate-50 p-5 space-y-3">
    <div class="text-sm font-extrabold text-slate-900">Akses Site Moderator</div>
    <div class="text-xs text-slate-600 font-semibold">Centang fitur admin yang boleh diakses.</div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
        @foreach($permissions as $p)
            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                <input type="checkbox" name="permissions[]" value="{{ $p['name'] }}"
                       {{ in_array($p['name'], old('permissions', $currentPermissions)) ? 'checked' : '' }}
                       class="w-5 h-5">
                <div class="min-w-0">
                    <div class="text-sm font-extrabold text-slate-900">{{ $p['label'] }}</div>
                    <div class="text-[11px] font-semibold text-slate-500">{{ $p['name'] }}</div>
                </div>
            </label>
        @endforeach
    </div>
</div>

<script>
function userEditUI() {
    return {
        role: @json(old('role', $currentRole)),
    }
}
</script>


        <div class="flex items-center justify-end gap-2 pt-2">
            <a href="{{ route('admin.users.show', $user) }}"
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
@endsection
