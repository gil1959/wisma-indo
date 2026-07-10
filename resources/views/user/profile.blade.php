@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $isEn ? 'Profile' : 'Profil')
@section('page-title', $isEn ? 'Profile' : 'Profil')
@section('page-subtitle', $isEn ? 'Manage your account information' : 'Kelola informasi akun kamu')


@section('content')
{{-- Alerts --}}
@if(session('status'))
<div class="mb-4 alert-success">
    {{ session('status') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 alert-error">
    <div class="font-semibold mb-1">{{ $isEn ? 'There are errors:' : 'Terdapat error:' }}</div>
    <ul class="list-disc pl-5 space-y-1">
        @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('user.profile.update') }}" class="space-y-5">
    @csrf

    {{-- Profile Info --}}
    <div class="card p-5 relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(1,148,243,0.16) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between gap-3 relative">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Profile Information' : 'Informasi Profil' }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $isEn ? 'Update basic data for transactions.' : 'Update data dasar untuk kebutuhan transaksi.' }}</p>
            </div>
            <span class="pill pill-azure shrink-0">
                <i data-lucide="user" class="w-4 h-4"></i>
                Profile
            </span>
        </div>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4 relative">
            <div>
                <label class="label">Name</label>
                <input name="name" class="input" value="{{ old('name', $user->name) }}" required>
            </div>

            <div>
                <label class="label">Email</label>
                <input name="email" type="email" class="input" value="{{ old('email', $user->email) }}" required>
            </div>

            <div>
                <label class="label">{{ $isEn ? 'Phone' : 'No HP' }}</label>
                <input name="phone" class="input" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
            </div>

            <div>
                <label class="label">{{ $isEn ? 'Sub District' : 'Kecamatan' }}</label>
                <input name="sub_district" class="input" value="{{ old('sub_district', $user->sub_district) }}" placeholder="{{ $isEn ? 'Sub district' : 'Kecamatan' }}">>
            </div>

            <div class="md:col-span-2">
                <label class="label">{{ $isEn ? 'Full Address' : 'Alamat Lengkap' }}</label>
                <textarea name="full_address" class="input" rows="3" placeholder="{{ $isEn ? 'Full address' : 'Alamat lengkap' }}">{{ old('full_address', $user->full_address) }}</textarea>
                <div class="text-xs text-slate-500 mt-1">
                    {{ $isEn ? 'Use a complete address to help billing/admin confirmation.' : 'Gunakan alamat lengkap untuk memudahkan penagihan/konfirmasi admin.' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Account Info --}}
    <div class="card p-5">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Account Information' : 'Informasi Akun' }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $isEn ? 'Account data stored in the system.' : 'Informasi akun yang tersimpan di sistem.' }}</p>
            </div>
            <span class="pill pill-azure shrink-0">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                Account
            </span>
        </div>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Registered At' : 'Terdaftar Pada' }}</div>
                <div class="mt-1 text-sm font-semibold text-slate-900">
                    {{ optional($user->created_at)->format('d M Y H:i') }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                <div class="text-xs font-semibold text-slate-500">Role</div>
                <div class="mt-1 text-sm font-semibold text-slate-900">
                    {{ method_exists($user, 'getRoleNames') ? $user->getRoleNames()->implode(', ') : '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Password --}}
    <div class="card p-5">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Change Password' : 'Ganti Password' }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $isEn ? 'Leave blank if you do not want to change the password.' : 'Kosongkan jika tidak ingin mengganti password.' }}</p>
            </div>
            <span class="pill pill-azure shrink-0">
                <i data-lucide="key-round" class="w-4 h-4"></i>
                Security
            </span>
        </div>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label">{{ $isEn ? 'New Password' : 'Password Baru' }}</label>
                <input name="password" type="password" class="input" autocomplete="new-password" placeholder="{{ $isEn ? 'Minimum 8 characters' : 'Minimal 8 karakter' }}">

            </div>

            <div>
                <label class="label">{{ $isEn ? 'Confirm Password' : 'Konfirmasi Password' }}</label>
                <input name="password_confirmation" type="password" class="input" autocomplete="new-password" placeholder="{{ $isEn ? 'Repeat new password' : 'Ulangi password baru' }}">
            </div>

            <div class="md:col-span-2 text-xs text-slate-500">
                {{ $isEn ? 'Tip: use a mix of uppercase, lowercase, numbers, and symbols.' : 'Tips: gunakan kombinasi huruf besar, kecil, angka, dan simbol.' }}
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
        <a href="{{ route('user.dashboard') }}" class="btn btn-ghost px-5 py-3">
            <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
            {{ $isEn ? 'Back to Dashboard' : 'Kembali ke Dashboard' }}
        </a>

        <button type="submit" class="btn btn-primary px-6 py-3">
            <i data-lucide="save" class="w-4 h-4"></i>
            {{ $isEn ? 'Save Changes' : 'Simpan Perubahan' }}
        </button>
    </div>
</form>
@endsection