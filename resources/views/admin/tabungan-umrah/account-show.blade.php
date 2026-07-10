@extends('layouts.admin')
@section('title', 'Detail Akun Tabungan Umrah')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $account->full_name }}</div>
            <div class="mt-1 text-sm text-slate-600 font-bold">
                WhatsApp: <span class="text-slate-900">{{ $account->whatsapp }}</span>
                <span class="mx-2 text-slate-300">•</span>
                Jenis: <span class="text-slate-900">{{ $account->saving_type === 'haji_furoda' ? 'Haji Furoda' : 'Umroh Reguler' }}</span>
            </div>
        </div>
<a href="{{ route('admin.tabungan-umrah.accounts.statement.print', $account) }}" target="_blank"
   class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-[#0194F3] text-white font-extrabold hover:opacity-90">
    <i data-lucide="printer" class="w-4 h-4"></i>
    Cetak Rekening Koran
</a>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tabungan-umrah.accounts.pending') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <div class="font-extrabold text-emerald-700">Sukses</div>
            <div class="text-sm mt-1 text-emerald-700">{{ session('success') }}</div>
        </div>
    @endif

    @php
        $statusBadge = match($account->status){
            'pending' => ['bg-amber-50 text-amber-700 border-amber-100','Pending'],
            'verified' => ['bg-emerald-50 text-emerald-700 border-emerald-100','Terverifikasi'],
            'rejected' => ['bg-rose-50 text-rose-700 border-rose-100','Rejected'],
            'suspended' => ['bg-slate-100 text-slate-700 border-slate-200','Suspended'],
            default => ['bg-slate-100 text-slate-700 border-slate-200', ucfirst($account->status)]
        };
    @endphp

    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500 font-extrabold">Status Akun</div>
            <div class="mt-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-extrabold border {{ $statusBadge[0] }}">
                    {{ $statusBadge[1] }}
                </span>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500 font-extrabold">Saldo Approved</div>
            <div class="mt-2 text-xl font-extrabold text-slate-900">
                Rp {{ number_format((int)$approvedTotal,0,',','.') }}
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500 font-extrabold">Target</div>
            <div class="mt-2 text-slate-900 font-extrabold">
                {{ $account->target_amount ? 'Rp '.number_format((int)$account->target_amount,0,',','.') : '-' }}
            </div>
            <div class="text-xs text-slate-500 font-bold mt-1">
                Keberangkatan: {{ $account->target_departure_date ? $account->target_departure_date->format('Y-m-d') : '-' }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Admin Actions --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div class="font-extrabold text-slate-900">Aksi Admin</div>
                <div class="text-xs text-slate-500 font-bold">Account ID: {{ $account->id }}</div>
            </div>

            @if($account->status === 'pending')
                <form method="POST" action="{{ route('admin.tabungan-umrah.accounts.verify', $account->id) }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <div class="text-sm font-extrabold text-slate-700">Target (Rp)</div>
                        <input type="number" name="target_amount" min="1000"
                               class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white"
                               required>
                    </div>

                    <div>
                        <div class="text-sm font-extrabold text-slate-700">Tanggal Keberangkatan</div>
                        <input type="date" name="target_departure_date"
                               class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white"
                               required>
                    </div>

                    <button class="w-full inline-flex items-center justify-center gap-2 rounded-2xl py-3 font-extrabold text-white bg-brand-500 hover:bg-brand-600">
                        <i data-lucide="badge-check" class="w-4 h-4"></i>
                        Verifikasi Akun
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.tabungan-umrah.accounts.reject', $account->id) }}" class="mt-4 space-y-3">
                    @csrf
                    <div class="text-sm font-extrabold text-slate-700">Alasan Reject</div>
                    <textarea name="rejected_reason" rows="4"
                              class="w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50 focus:bg-white"
                              placeholder="Contoh: data tidak lengkap / nomor WA tidak valid" required></textarea>

                    <button class="w-full inline-flex items-center justify-center gap-2 rounded-2xl py-3 font-extrabold text-white bg-rose-500 hover:bg-rose-600">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        Reject Akun
                    </button>
                </form>
            @endif

            @if($account->status === 'verified')
                <form method="POST" action="{{ route('admin.tabungan-umrah.accounts.suspend', $account->id) }}" class="mt-5">
                    @csrf
                    <button class="w-full inline-flex items-center justify-center gap-2 rounded-2xl py-3 font-extrabold text-white bg-[#ff0404] hover:bg-[#500202]">
                        <i data-lucide="pause-circle" class="w-4 h-4"></i>
                        Suspend Akun
                    </button>
                </form>
            @endif

            @if($account->status === 'suspended')
                <form method="POST" action="{{ route('admin.tabungan-umrah.accounts.unsuspend', $account->id) }}" class="mt-5">
                    @csrf
                    <button class="w-full inline-flex items-center justify-center gap-2 rounded-2xl py-3 font-extrabold text-white bg-emerald-600 hover:bg-emerald-700">
                        <i data-lucide="play-circle" class="w-4 h-4"></i>
                        Aktifkan Kembali
                    </button>
                </form>
            @endif

            @if($account->rejected_reason)
                <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4">
                    <div class="font-extrabold text-rose-700">Alasan Reject</div>
                    <div class="text-sm mt-1 text-rose-700">{{ $account->rejected_reason }}</div>
                </div>
            @endif
        </div>

        {{-- Deposits --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div class="font-extrabold text-slate-900">Riwayat Setoran</div>
                <div class="text-sm text-slate-500 font-bold">Total: {{ $account->deposits->count() }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                    <tr class="text-slate-400 font-extrabold">
                        <th class="text-left px-6 py-4">Tanggal</th>
                        <th class="text-left px-6 py-4">Nominal</th>
                        <th class="text-left px-6 py-4">Status</th>
                        <th class="text-right px-6 py-4">Aksi</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                    @forelse($account->deposits as $d)
                        @php
                            $dBadge = match($d->status){
                                'waiting_verification' => ['bg-amber-50 text-amber-700 border-amber-100','Waiting'],
                                'approved' => ['bg-emerald-50 text-emerald-700 border-emerald-100','Approved'],
                                'rejected' => ['bg-rose-50 text-rose-700 border-rose-100','Rejected'],
                                default => ['bg-slate-100 text-slate-700 border-slate-200', ucfirst($d->status)]
                            };
                        @endphp

                        <tr class="hover:bg-slate-50/60">
                            <td class="px-6 py-4 text-slate-700 font-bold">
                                {{ optional($d->submitted_at)->format('Y-m-d') ?? $d->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 font-extrabold text-slate-900">
                                Rp {{ number_format((int)$d->amount,0,',','.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold border {{ $dBadge[0] }}">
                                    {{ $dBadge[1] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.tabungan-umrah.deposits.show', $d->id) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
                                    Detail
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center">
                                <div class="mx-auto w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-5 h-5 text-slate-500"></i>
                                </div>
                                <div class="mt-3 font-extrabold text-slate-900">Belum ada setoran</div>
                                <div class="text-sm text-slate-600 font-bold mt-1">Setoran user akan muncul di sini setelah dibuat.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
