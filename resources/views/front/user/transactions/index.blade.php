@extends('layouts.front')

@section('content')
@php
    $adminWa = \App\Models\Setting::where('key', 'footer_whatsapp')->value('value') ?? '';
    // Format to 62...
    if(str_starts_with($adminWa, '0')) $adminWa = '62' . substr($adminWa, 1);
    $adminWa = preg_replace('/[^0-9]/', '', $adminWa);
@endphp
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-slate-800 mb-8">Riwayat Transaksi</h1>
        
        @if($transactions->isEmpty() && $listingTransactions->isEmpty())
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
        
        <!-- Table Top Up -->
        @if(!$transactions->isEmpty())
        <div class="mb-8">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Transaksi Top Up</h2>
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
                                <td class="p-4 text-right flex flex-col items-end gap-2">
                                    @if($tx->status == 'pending' && $tx->payment_method == 'offline' && !$tx->payment_proof)
                                        <a href="{{ route('topup.upload_proof', $tx->id) }}" class="text-sm text-[#0194F3] font-bold hover:underline">Upload Bukti</a>
                                        @if($adminWa)
                                        <a href="https://wa.me/{{ $adminWa }}?text=Halo%20Admin,%20saya%20ingin%20konfirmasi%20pembayaran%20Top%20Up%20Kuota%20Iklan%20sebesar%20Rp{{ number_format($tx->total_amount ?? $tx->price, 0, '', '') }}" target="_blank" class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full hover:bg-green-600 transition">
                                            <i class="fab fa-whatsapp"></i> Konfirmasi ke WA
                                        </a>
                                        @endif
                                    @elseif($tx->status == 'pending' && $tx->payment_url)
                                        <a href="{{ $tx->payment_url }}" target="_blank" class="text-sm text-[#0194F3] font-bold hover:underline">Bayar Sekarang</a>
                                    @elseif($tx->status == 'pending' && $tx->payment_proof)
                                        <span class="text-sm text-slate-500 italic">Menunggu Verifikasi</span>
                                        @if($adminWa)
                                        <a href="https://wa.me/{{ $adminWa }}?text=Halo%20Admin,%20saya%20sudah%20upload%20bukti%20pembayaran%20Top%20Up%20Kuota%20Iklan.%20Mohon%20segera%20diproses." target="_blank" class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full mt-1 hover:bg-green-600 transition">
                                            <i class="fab fa-whatsapp"></i> Konfirmasi ke WA
                                        </a>
                                        @endif
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
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
        @endif

        <!-- Table Promosi Iklan -->
        @if(!$listingTransactions->isEmpty())
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-4">Transaksi Promosi Iklan</h2>
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4">Paket</th>
                                <th class="p-4">Total Pembayaran</th>
                                <th class="p-4">Metode</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($listingTransactions as $ltx)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4 font-medium text-slate-800">{{ $ltx->created_at->format('d M Y, H:i') }}</td>
                                <td class="p-4">
                                    <span class="font-bold text-slate-900">{{ $ltx->listingPackage->name ?? 'Paket Iklan' }}</span>
                                </td>
                                <td class="p-4 font-bold text-emerald-600">
                                    Rp {{ number_format($ltx->amount, 0, ',', '.') }}
                                </td>
                                <td class="p-4">
                                    <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded text-xs font-semibold uppercase">{{ $ltx->payment_method }}</span>
                                </td>
                                <td class="p-4">
                                    @if($ltx->status == 'success')
                                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Sukses</span>
                                    @elseif($ltx->status == 'pending')
                                        <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">Pending</span>
                                    @else
                                        <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-xs font-bold">Gagal</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right flex flex-col items-end gap-2">
                                    @if($ltx->status == 'pending' && $ltx->payment_method == 'offline' && !$ltx->payment_proof)
                                        <a href="{{ route('listing_promotions.upload_proof', $ltx->id) }}" class="text-sm text-[#0194F3] font-bold hover:underline">Upload Bukti</a>
                                        @if($adminWa)
                                        <a href="https://wa.me/{{ $adminWa }}?text=Halo%20Admin,%20saya%20ingin%20konfirmasi%20pembayaran%20{{ rawurlencode($ltx->listingPackage->name ?? 'Paket Promosi Iklan') }}%20untuk%20iklan%20'{{ rawurlencode($ltx->listing->title ?? '') }}'%20sebesar%20Rp{{ number_format($ltx->amount, 0, '', '') }}" target="_blank" class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full hover:bg-green-600 transition">
                                            <i class="fab fa-whatsapp"></i> Konfirmasi ke WA
                                        </a>
                                        @endif
                                    @elseif($ltx->status == 'pending' && $ltx->payment_proof)
                                        <span class="text-sm text-slate-500 italic">Menunggu Verifikasi</span>
                                        @if($adminWa)
                                        <a href="https://wa.me/{{ $adminWa }}?text=Halo%20Admin,%20saya%20sudah%20upload%20bukti%20pembayaran%20{{ rawurlencode($ltx->listingPackage->name ?? 'Paket Promosi Iklan') }}%20untuk%20iklan%20'{{ rawurlencode($ltx->listing->title ?? '') }}'.%20Mohon%20segera%20diproses." target="_blank" class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full mt-1 hover:bg-green-600 transition">
                                            <i class="fab fa-whatsapp"></i> Konfirmasi ke WA
                                        </a>
                                        @endif
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
            @if($listingTransactions->hasPages())
            <div class="mt-4">
                {{ $listingTransactions->links() }}
            </div>
            @endif
        </div>
        @endif

        @endif
    </div>
</div>
@endsection
