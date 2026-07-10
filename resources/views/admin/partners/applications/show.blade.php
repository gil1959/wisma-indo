@extends('layouts.admin')

@section('content')
<div class="max-w-5xl space-y-5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Detail Pengajuan Partner</h1>
            <p class="mt-1 text-sm text-slate-600">Review data pendaftar dan lakukan approve / decline.</p>
        </div>

        <a href="{{ route('admin.partners.applications.index', ['status' => $application->status]) }}"
           class="px-4 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50">
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800">
            <div class="font-extrabold">Sukses</div>
            <div class="mt-1 text-sm">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="font-extrabold">Gagal</div>
            <div class="mt-1 text-sm">{{ session('error') }}</div>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <div class="flex items-center justify-between gap-4">
            <div class="text-xs font-extrabold text-slate-600 uppercase">Status</div>
            <div class="px-3 py-1 rounded-full text-xs font-extrabold
                @if($application->status==='pending') bg-amber-100 text-amber-800
                @elseif($application->status==='approved') bg-green-100 text-green-800
                @else bg-red-100 text-red-800 @endif">
                {{ strtoupper($application->status) }}
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Nama</div>
                <div class="mt-1 font-bold text-slate-900">{{ $application->name }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Kontak</div>
                <div class="mt-1 font-semibold text-slate-800">{{ $application->email }}</div>
                <div class="text-sm text-slate-600">{{ $application->phone ?? '-' }}</div>
            </div>

            <div class="md:col-span-2">
                <div class="text-xs font-extrabold text-slate-600 uppercase">Alamat</div>
                <div class="mt-2 whitespace-pre-line text-sm text-slate-800 border border-slate-200 rounded-2xl p-4 bg-slate-50">
                    {{ $application->address ?? '-' }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-xs font-extrabold text-slate-600 uppercase">Alasan Bergabung</div>
                <div class="mt-2 whitespace-pre-line text-sm text-slate-800 border border-slate-200 rounded-2xl p-4 bg-slate-50">
                    {{ $application->reason ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Jenis Identitas</div>
                <div class="mt-1 font-semibold text-slate-800">{{ $application->identity_type ?? '-' }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Tanggal Pengajuan</div>
                <div class="mt-1 font-semibold text-slate-800">{{ optional($application->submitted_at)->format('d M Y H:i') ?? '-' }}</div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="rounded-2xl border border-slate-200 p-5 bg-white">
                <div class="text-xs font-extrabold text-slate-600 uppercase">File Identitas</div>
                <p class="mt-1 text-sm text-slate-600">Klik untuk melihat dokumen identitas.</p>
                <a class="inline-flex mt-3 px-4 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50"
                   target="_blank" href="{{ asset('storage/'.$application->identity_file_path) }}">
                    Buka Dokumen
                </a>
            </div>

            <div class="rounded-2xl border border-slate-200 p-5 bg-white">
        <div class="text-xs font-extrabold text-slate-600 uppercase">Dokumen Legalitas (PDF)</div>
        <p class="mt-1 text-sm text-slate-600">Klik untuk melihat dokumen legalitas.</p>

        @if($application->legal_document_path)
            <a class="inline-flex mt-3 px-4 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50"
               target="_blank" href="{{ asset('storage/'.$application->legal_document_path) }}">
                Buka Dokumen
            </a>
        @else
            {{-- fallback untuk aplikasi lama --}}
            <div class="mt-3 text-sm text-slate-500">
                Dokumen legalitas belum tersedia (data lama).
            </div>
        @endif
    </div>
        </div>

        @if($application->review_note)
            <div class="mt-6">
                <div class="text-xs font-extrabold text-slate-600 uppercase">Catatan Review</div>
                <div class="mt-2 whitespace-pre-line text-sm text-slate-800 border border-slate-200 rounded-2xl p-4 bg-slate-50">
                    {{ $application->review_note }}
                </div>
            </div>
        @endif
    </div>

    @if($application->status === 'pending')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- APPROVE --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h2 class="font-extrabold text-slate-900">Approve</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Approve akan membuat akun user dengan role <b>partner</b>. Pajak dapat diatur per partner.
                </p>

                <form method="POST" action="{{ route('admin.partners.applications.approve', $application->id) }}" class="mt-4 space-y-3">
                    @csrf

                    <div>
                        <label class="text-xs font-extrabold text-slate-600 uppercase">Pajak Partner (%)</label>
                        <input type="number" step="0.01" name="tax_percent" value="0"
                               class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold bg-white">
                    </div>

                    <div>
                        <label class="text-xs font-extrabold text-slate-600 uppercase">Note (opsional)</label>
                        <textarea name="note" rows="4"
                                  class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold bg-white"></textarea>
                    </div>

                    <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#16a34a;">
                        Approve
                    </button>
                </form>
            </div>

            {{-- DECLINE --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h2 class="font-extrabold text-slate-900">Decline</h2>
                <p class="mt-1 text-sm text-slate-600">Decline wajib disertai alasan/catatan.</p>

                <form method="POST" action="{{ route('admin.partners.applications.reject', $application->id) }}" class="mt-4 space-y-3">
                    @csrf

                    <div>
                        <label class="text-xs font-extrabold text-slate-600 uppercase">Note (wajib)</label>
                        <textarea name="note" rows="5" required
                                  class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold bg-white"></textarea>
                    </div>

                    <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#dc2626;">
                        Decline
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
