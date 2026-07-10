@extends('layouts.admin')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Detail User</h2>
            <p class="mt-1 text-sm text-slate-600">Informasi lengkap user</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user) }}"
               class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
               style="background:#0194F3;">
                Edit
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2.5 rounded-2xl font-extrabold border border-slate-200 text-slate-700 hover:bg-slate-50">
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-xs font-extrabold text-slate-500 uppercase">Nama</div>
                <div class="mt-1 text-slate-900 font-semibold whitespace-pre-line">{{ $user->name }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-500 uppercase">Email</div>
                <div class="mt-1 text-slate-900 font-semibold whitespace-pre-line">{{ $user->email }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-500 uppercase">No HP</div>
                <div class="mt-1 text-slate-900 font-semibold whitespace-pre-line">{{ $user->phone ?? '-' }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-500 uppercase">Email Verified</div>
                <div class="mt-1 text-slate-900 font-semibold ">
                    {{ $user->email_verified_at ? $user->email_verified_at->format('d M Y H:i') : 'Belum' }}
                </div>
            </div>
            <div>
  <div class="text-xs font-extrabold text-slate-500 uppercase">Role</div>
  <div class=" text-slate-900 font-semibold mt-1">
      {{ strtoupper($user->roles->pluck('name')->first() ?? '-') }}
  </div>
</div>

        </div>

        <div>
            <div class="text-xs font-extrabold text-slate-500 uppercase">Alamat</div>
            <div class="mt-1 text-slate-900 font-semibold ">
                {{ $user->address ?? $user->full_address ?? '-' }}
            </div>
        </div>

        

        <div class="pt-3 border-t border-slate-200 text-xs text-slate-500">
            Dibuat: {{ optional($user->created_at)->format('d M Y H:i') }}  Update: {{ optional($user->updated_at)->format('d M Y H:i') }}
        </div>
    </div>

</div>
@endsection
