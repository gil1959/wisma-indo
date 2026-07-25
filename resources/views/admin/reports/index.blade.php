@extends('layouts.admin')

@section('title', 'Laporan Transaksi')
@section('page-title', 'Laporan Transaksi')

@section('content')
<div class="card p-0 bg-white shadow rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-[#0194F3]">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-[#0194F3]">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                <select name="status" class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-[#0194F3]">
                    <option value="">Semua Status</option>
                    <option value="success" {{ $status == 'success' ? 'selected' : '' }}>Sukses</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Gagal / Expired</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-[#0194F3] text-white rounded-xl font-bold hover:bg-blue-600 transition">Filter</button>
                <button type="submit" name="print" value="1" formtarget="_blank" class="px-4 py-2 bg-emerald-500 text-white rounded-xl font-bold hover:bg-emerald-600 transition flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i> Print
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 p-5 border-b border-slate-100 gap-4 bg-slate-50/50">
        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-emerald-800 uppercase">Total Pemasukan (Sukses)</div>
                <div class="text-2xl font-black text-emerald-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-amber-800 uppercase">Total Pending</div>
                <div class="text-2xl font-black text-amber-600">Rp {{ number_format($totalPending, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b text-xs font-extrabold text-slate-700">
                <tr>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">ID Transaksi</th>
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Jenis Transaksi</th>
                    <th class="px-5 py-3 text-right">Nominal</th>
                    <th class="px-5 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($paginatedTransactions as $tx)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3 whitespace-nowrap">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-5 py-3 font-mono text-xs">{{ $tx->id }}</td>
                    <td class="px-5 py-3 font-medium">{{ $tx->user }}</td>
                    <td class="px-5 py-3">{{ $tx->type }}</td>
                    <td class="px-5 py-3 text-right font-bold text-slate-800">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-center">
                        @if($tx->status == 'success')
                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Sukses</span>
                        @elseif($tx->status == 'pending')
                            <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">Pending</span>
                        @else
                            <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-xs font-bold">Gagal</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($paginatedTransactions->hasPages())
    <div class="p-5 border-t">
        {{ $paginatedTransactions->links() }}
    </div>
    @endif
</div>
@endsection
