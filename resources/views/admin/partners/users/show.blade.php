@extends('layouts.admin')

@section('content')
<div class="max-w-5xl space-y-5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Detail User Partner</h1>
            <p class="mt-1 text-sm text-slate-600">Semua data user partner (profil, alamat, rekening, role, tipe agency).</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.partners.users.index') }}"
               class="px-4 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50">
                Kembali
            </a>

            <a href="{{ route('admin.partners.users.edit', $user->id) }}"
               class="px-4 py-2 rounded-2xl font-extrabold text-white"
               style="background:#0194F3;">
                Edit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800 font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-3xl border border-slate-200 bg-white p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Nama</div>
                <div class="mt-1 font-extrabold text-slate-900">{{ $user->name }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Email</div>
                <div class="mt-1 font-semibold text-slate-900">{{ $user->email }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">No HP</div>
                <div class="mt-1 font-semibold text-slate-900">{{ $user->phone ?? '-' }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Role</div>
                <div class="mt-1 font-extrabold text-slate-900">
                    {{ $roles->isNotEmpty() ? strtoupper($roles->implode(', ')) : '-' }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-xs font-extrabold text-slate-600 uppercase">Alamat (address)</div>
                <div class="mt-1 font-semibold text-slate-900 whitespace-pre-line">
                    {{ $user->address ?? '-' }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-xs font-extrabold text-slate-600 uppercase">Alamat Lengkap (full_address)</div>
                <div class="mt-1 font-semibold text-slate-900 whitespace-pre-line">
                    {{ $user->full_address ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Kelurahan/Kecamatan (sub_district)</div>
                <div class="mt-1 font-semibold text-slate-900">{{ $user->sub_district ?? '-' }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Tipe Agency (partner_type)</div>
                <div class="mt-1 font-extrabold text-slate-900">{{ $user->partner_type ?? '-' }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Pajak Partner</div>
                <div class="mt-1 font-semibold text-slate-900">
                    {{ $user->partner_tax_percent !== null ? number_format((float)$user->partner_tax_percent, 2) . '%' : '-' }}
                </div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Status</div>
                <div class="mt-1 font-extrabold">
                    @if($user->is_suspended)
                        <span class="text-red-600">SUSPENDED</span>
                    @else
                        <span class="text-green-600">AKTIF</span>
                    @endif
                </div>
            </div>

            <div class="md:col-span-2 mt-2">
                <div class="text-xs font-extrabold text-slate-600 uppercase">Data Rekening</div>
                <div class="mt-2 rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-1">
                    <div class="text-sm text-slate-800">
                        <span class="font-extrabold text-slate-900">Bank:</span>
                        {{ $user->partner_bank_name ?? '-' }}
                    </div>
                    <div class="text-sm text-slate-800">
                        <span class="font-extrabold text-slate-900">No Rekening:</span>
                        {{ $user->partner_bank_account_number ?? '-' }}
                    </div>
                    <div class="text-sm text-slate-800">
                        <span class="font-extrabold text-slate-900">Atas Nama:</span>
                        {{ $user->partner_bank_account_holder ?? '-' }}
                    </div>
                </div>
                <div class="md:col-span-2 mt-4">
    <div class="text-xs font-extrabold text-slate-600 uppercase">Dokumen Legalitas</div>

    @if($user->partner_legal_document_path)
        <a class="inline-flex mt-2 px-4 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50"
           target="_blank"
           href="{{ asset('storage/'.$user->partner_legal_document_path) }}">
            Lihat PDF Legalitas
        </a>
    @else
        <div class="mt-2 text-sm text-slate-500">Belum ada dokumen legalitas.</div>
    @endif
</div>

            </div>

        </div>
    </div>
</div>
@endsection
