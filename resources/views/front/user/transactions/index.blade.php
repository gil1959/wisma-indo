@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-slate-800 mb-8">Riwayat Transaksi</h1>
        @if($transactions->isEmpty())
        <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="receipt" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Transaksi</h3>
            <p class="text-slate-500 mb-6">Anda belum pernah melakukan top up atau transaksi berbayar lainnya.</p>
            <a href="{{ route('topup') }}" class="px-6 py-2 bg-[#0194F3] text-white font-bold rounded-xl hover:bg-blue-600 transition">
                Top Up Sekarang
            </a>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Paket/Nominal</th>
                            <th class="p-4">Total Pembayaran</th>
                            <th class="p-4">Metode</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transactions as $tx)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-medium text-slate-800">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900">{{ $tx->amount }} Listing</span>
                            </td>
                            <td class="p-4 font-bold text-emerald-600">
                                Rp {{ number_format($tx->total_amount ?? $tx->price, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded text-xs font-semibold uppercase">{{ $tx->payment_method }}</span>
                            </td>
                            <td class="p-4">
                                @if($tx->status == 'success')
                                    <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Sukses</span>
                                @elseif($tx->status == 'pending')
                                    <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">Pending</span>
                                @else
                                    <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-xs font-bold">Gagal</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                @if($tx->status == 'pending' && $tx->payment_method == 'offline' && !$tx->payment_proof)
                                    <a href="{{ route('topup.upload_proof', $tx->id) }}" class="text-sm text-[#0194F3] font-bold hover:underline">Upload Bukti</a>
                                @elseif($tx->status == 'pending' && $tx->payment_url)
                                    <a href="{{ $tx->payment_url }}" target="_blank" class="text-sm text-[#0194F3] font-bold hover:underline">Bayar Sekarang</a>
                                @elseif($tx->status == 'pending' && $tx->payment_proof)
                                    <span class="text-sm text-slate-500 italic">Menunggu Verifikasi</span>
                                @elseif($tx->status == 'failed' && $tx->note)
                                    <div class="text-xs text-rose-600 bg-rose-50 px-2 py-1 rounded inline-block text-left max-w-[200px]" title="{{ $tx->note }}">
                                        Catatan: {{ Str::limit($tx->note, 30) }}
                                    </div>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($transactions->hasPages())
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
