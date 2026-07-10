@extends('layouts.admin')
@section('title', 'Detail Setoran Tabungan Umrah')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-extrabold text-slate-900">Detail Setoran</div>
            <div class="mt-1 text-sm text-slate-600 font-bold">
                Atas nama: <span class="text-slate-900">{{ $deposit->account->full_name }}</span>
                <span class="mx-2 text-slate-300">•</span>
                Nominal: <span class="text-slate-900">Rp {{ number_format((int)$deposit->amount,0,',','.') }}</span>
            </div>
        </div>

        <a href="{{ route('admin.tabungan-umrah.deposits.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <div class="font-extrabold text-emerald-700">Sukses</div>
            <div class="text-sm mt-1 text-emerald-700">{{ session('success') }}</div>
        </div>
    @endif

    @php
        $badge = match($deposit->status){
            'waiting_verification' => ['bg-amber-50 text-amber-700 border-amber-100','Waiting Verification'],
            'approved' => ['bg-emerald-50 text-emerald-700 border-emerald-100','Approved'],
            'rejected' => ['bg-rose-50 text-rose-700 border-rose-100','Rejected'],
            default => ['bg-slate-100 text-slate-700 border-slate-200', ucfirst($deposit->status)]
        };
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500 font-extrabold">Status</div>
            <div class="mt-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-extrabold border {{ $badge[0] }}">
                    {{ $badge[1] }}
                </span>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500 font-extrabold">Tanggal Submit</div>
            <div class="mt-2 text-slate-900 font-extrabold">
                {{ optional($deposit->submitted_at)->format('Y-m-d H:i') ?? $deposit->created_at->format('Y-m-d H:i') }}
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500 font-extrabold">Rekening Tujuan</div>
            <div class="mt-2 text-slate-900 font-extrabold">
                {{ optional($deposit->paymentMethod)->bank_name ?? '-' }}
            </div>
            <div class="text-xs text-slate-500 font-bold mt-1">
                {{ optional($deposit->paymentMethod)->account_number ?? '-' }} ({{ optional($deposit->paymentMethod)->account_holder ?? '-' }})
            </div>
        </div>
    </div>

    {{-- Proof --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between gap-4">
            <div class="font-extrabold text-slate-900">Bukti Pembayaran</div>

            @if($deposit->proof_image)
                <a href="{{ asset('storage/'.$deposit->proof_image) }}" target="_blank" rel="noreferrer"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Buka / Download
                </a>
            @endif
        </div>

        @if($deposit->proof_image)
            <div class="mt-4">
                <img src="{{ asset('storage/'.$deposit->proof_image) }}"
                     class="w-full rounded-2xl border border-slate-200 bg-slate-50"
                     alt="Bukti Pembayaran">
                <div class="text-xs text-slate-500 font-bold mt-2">Pastikan nama, nominal, tanggal, dan rekening tujuan sesuai.</div>
            </div>
        @else
            <div class="mt-3 text-sm font-bold text-slate-600">Tidak ada bukti pembayaran terunggah.</div>
        @endif
    </div>

    {{-- Actions --}}
    @if($deposit->status === 'waiting_verification')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <form method="POST" action="{{ route('admin.tabungan-umrah.deposits.approve', $deposit->id) }}"
              class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            @csrf
            <div class="font-extrabold text-slate-900">Approve</div>
            <div class="text-sm font-bold text-slate-600 mt-1">Setoran akan disetujui dan masuk ke saldo approved.</div>
            <button class="mt-4 w-full rounded-2xl py-3 font-extrabold text-white" style="background:#059669;">
                Approve Setoran
            </button>
        </form>

        <form method="POST" action="{{ route('admin.tabungan-umrah.deposits.reject', $deposit->id) }}"
              class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-3">
            @csrf
            <div class="font-extrabold text-slate-900">Reject</div>
            <div class="text-sm font-bold text-slate-600">Wajib isi alasan untuk audit.</div>
            <textarea name="note" rows="4"
                      class="w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white"
                      placeholder="Masukkan alasan / catatan reject" required></textarea>
            <button class="w-full rounded-2xl py-3 font-extrabold text-white" style="background:#ef4444;">
                Reject Setoran
            </button>
        </form>
    </div>

@elseif($deposit->status === 'approved')
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-3">
        <div class="font-extrabold text-slate-900">Ubah Status</div>
        <div class="text-sm font-bold text-slate-600">
            Setoran ini sudah <span class="text-emerald-700 font-extrabold">APPROVED</span>. Jika ternyata salah (nominal/rekening/bukti), Anda bisa ubah menjadi <span class="text-rose-700 font-extrabold">REJECTED</span>.
        </div>

        <form method="POST" action="{{ route('admin.tabungan-umrah.deposits.reject', $deposit->id) }}" class="space-y-3">
            @csrf
            <textarea name="note" rows="4"
                      class="w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white"
                      placeholder="Wajib isi alasan perubahan status (contoh: bukti tidak valid / salah rekening / double input)" required></textarea>

            <button class="w-full rounded-2xl py-3 font-extrabold text-white" style="background:#ef4444;">
                Ubah Jadi Rejected
            </button>
        </form>

        @if($deposit->note)
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-700">
                <div class="font-extrabold">Catatan Sebelumnya:</div>
                <div>{{ $deposit->note }}</div>
            </div>
        @endif
    </div>

@else
    @if($deposit->note)
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-700">
            <div class="font-extrabold">Catatan:</div>
            <div>{{ $deposit->note }}</div>
        </div>
    @endif
@endif


</div>
@endsection
