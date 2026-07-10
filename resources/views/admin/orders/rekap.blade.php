@extends('layouts.admin')

@section('title', 'Rekap Orders')
@section('page-title', 'Rekap Orders')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Rekap Orders</h2>
            <p class="mt-1 text-sm text-slate-600">Pilih rentang tanggal untuk pembukuan dan print.</p>
        </div>

        <a href="{{ route('admin.orders.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
            Kembali
        </a>
    </div>

    {{-- Filter --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.orders.rekap') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-4">
                <label class="block text-sm font-bold text-slate-800 mb-2">Dari Tanggal</label>
                <input type="date"
                       name="from"
                       value="{{ $from }}"
                       class="w-full rounded-xl border-slate-200">
            </div>

            <div class="sm:col-span-4">
                <label class="block text-sm font-bold text-slate-800 mb-2">Sampai Tanggal</label>
                <input type="date"
                       name="to"
                       value="{{ $to }}"
                       class="w-full rounded-xl border-slate-200">
            </div>

            <div class="sm:col-span-4 flex gap-2">
                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                        style="background:#0194F3;"
                        onmouseover="this.style.background='#0186DB'"
                        onmouseout="this.style.background='#0194F3'">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Tampilkan
                </button>

                @if($from && $to)
    <a href="{{ route('admin.orders.rekap.print', ['from'=>$from, 'to'=>$to]) }}"
       target="_blank"
       class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
        <i data-lucide="printer" class="w-4 h-4" style="color:#0194F3;"></i>
        Print
    </a>

    <a href="{{ route('admin.orders.rekap.printPaid', ['from'=>$from, 'to'=>$to]) }}"
       target="_blank"
       class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
        <i data-lucide="printer" class="w-4 h-4" style="color:#0194F3;"></i>
        Print Paid
    </a>
@endif

            </div>
        </form>
    </div>

    {{-- Summary --}}
    @if($from && $to)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-slate-500 font-bold">Total Order</div>
                    <div class="mt-1 text-xl font-extrabold text-slate-900">{{ $summary['total_orders'] }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 font-bold">Total Pendapatan</div>
                    <div class="mt-1 text-xl font-extrabold text-slate-900">
                        Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <form method="POST"
      action="{{ route('admin.orders.rekap.printSelected') }}"
      target="_blank"
      id="printSelectedForm">
    @csrf
    <input type="hidden" name="from" value="{{ $from }}">
    <input type="hidden" name="to" value="{{ $to }}">

    <div class="flex items-center justify-between gap-3 mb-3">
       <div class="text-sm font-bold text-slate-700">
    Pilih order mana saja untuk di-print (status bebas)
</div>

<button type="submit"
        id="btnPrintSelected"
        disabled
        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
    <i data-lucide="printer" class="w-4 h-4" style="color:#0194F3;"></i>
    Print Selected
</button>

    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-slate-700">
                    <th class="text-left px-4 py-3 font-extrabold w-12">
                        <input type="checkbox" id="checkAll" class="rounded border-slate-300">
                    </th>
                    <th class="text-left px-4 py-3 font-extrabold">Tanggal</th>
                    <th class="text-left px-4 py-3 font-extrabold">Invoice</th>
                    <th class="text-left px-4 py-3 font-extrabold">Customer</th>
                    <th class="text-left px-4 py-3 font-extrabold">Produk</th>
                    <th class="text-left px-4 py-3 font-extrabold">Payment</th>
                    <th class="text-left px-4 py-3 font-extrabold">Order</th>
                    <th class="text-right px-4 py-3 font-extrabold">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $o)
                  <tr class="hover:bg-slate-50/60">
    <td class="px-4 py-3">
        <input type="checkbox"
               class="order-check rounded border-slate-300"
               name="order_ids[]"
               value="{{ $o->id }}">
    </td>

                        <td class="px-4 py-3">{{ $o->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-bold text-slate-900">{{ $o->invoice_number }}</td>
                        <td class="px-4 py-3">{{ $o->customer_name }}</td>
                        <td class="px-4 py-3">{{ $o->product_name }}</td>
                        <td class="px-4 py-3">{{ $o->payment_status }}</td>
                        <td class="px-4 py-3">{{ $o->order_status }}</td>
                        <td class="px-4 py-3 text-right font-extrabold">
                            Rp {{ number_format($o->final_price, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-500 font-bold">
                            Tidak ada data untuk rentang tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    const checks = Array.from(document.querySelectorAll('.order-check'));
    const btn = document.getElementById('btnPrintSelected');

    function selectedChecks() {
        return checks.filter(c => c.checked);
    }

    function syncButton() {
        btn.disabled = selectedChecks().length === 0;
    }

    function syncCheckAllState() {
        const selected = selectedChecks();

        if (checks.length === 0) {
            checkAll.checked = false;
            checkAll.indeterminate = false;
            checkAll.disabled = true;
            return;
        }

        checkAll.disabled = false;

        if (selected.length === 0) {
            checkAll.checked = false;
            checkAll.indeterminate = false;
        } else if (selected.length === checks.length) {
            checkAll.checked = true;
            checkAll.indeterminate = false;
        } else {
            checkAll.checked = false;
            checkAll.indeterminate = true;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checks.forEach(c => c.checked = checkAll.checked);
            syncButton();
            syncCheckAllState();
        });
    }

    checks.forEach(c => {
        c.addEventListener('change', function () {
            syncButton();
            syncCheckAllState();
        });
    });

    syncButton();
    syncCheckAllState();
});
</script>

@endpush

@endsection
