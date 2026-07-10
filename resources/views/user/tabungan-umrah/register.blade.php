@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $isEn ? 'Umrah Savings' : 'Tabungan Umrah')
@section('page-title', $isEn ? 'Umrah Savings' : 'Tabungan Umrah')
@section('page-subtitle', $isEn ? 'Umrah savings registration' : 'Registrasi tabungan umrah')


@section('content')
@if(session('success'))
<div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
    <div class="font-extrabold text-emerald-700">{{ $isEn ? 'Success' : 'Sukses' }}</div>
    <div class="text-sm mt-1 text-emerald-700">{{ session('success') }}</div>
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

<div class="mx-auto max-w-2xl">
    <div class="card p-5 relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(1,148,243,0.16) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between gap-3 relative">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'New Pilgrim Registration' : 'Registrasi Jamaah Baru' }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $isEn ? 'Fill in the form below to apply for Umrah savings.' : 'Isi data di bawah untuk mengajukan tabungan umrah.' }}</p>
            </div>
            <span class="pill pill-azure shrink-0">
                <i data-lucide="wallet" class="w-4 h-4"></i>
                {{ $isEn ? 'Savings' : 'Tabungan' }}
            </span>
        </div>

        <form method="POST" action="{{ route('user.tabungan-umrah.register') }}" class="mt-5 space-y-4 relative">
            @csrf

            <div>
                <label class="label">{{ $isEn ? 'Full Name' : 'Nama Lengkap' }}</label>
                <input name="full_name" class="input" value="{{ old('full_name') }}" placeholder="Nama lengkap" required>
            </div>

            <div>
                <label class="label">WhatsApp</label>
                <input name="whatsapp" class="input" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx" required>
            </div>

            <div>
                <label class="label">{{ $isEn ? 'Type of Savings' : 'Jenis Tabungan' }}</label>
                <select name="saving_type" class="input" required>
                    <option value="umroh_reguler" {{ old('saving_type','umroh_reguler')==='umroh_reguler' ? 'selected' : '' }}>Umroh Reguler</option>
                    <option value="haji_furoda" {{ old('saving_type')==='haji_furoda' ? 'selected' : '' }}>Haji Furoda</option>
                </select>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between pt-2">
                <a href="{{ route('user.dashboard') }}" class="btn btn-ghost">
                    <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
                    {{ $isEn ? 'Return' : 'Kembali' }}
                </a>

                <button type="submit" class="btn btn-primary">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    {{ $isEn ? 'Register Now' : 'Daftar Sekarang' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection