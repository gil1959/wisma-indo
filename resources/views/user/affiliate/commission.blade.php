@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('content')
<div class="space-y-5">
  <div>
    <h1 class="text-2xl font-extrabold text-slate-900">Commission</h1>
    <p class="mt-1 text-sm text-slate-600">{{ $isEn ? 'Commission from orders generated via your links.' : 'Komisi dari order yang masuk lewat link kamu.' }}</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Pending</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp {{ number_format((int)$summary['pending'],0,',','.') }}</div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Approved</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp {{ number_format((int)$summary['approved'],0,',','.') }}</div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Paid</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp {{ number_format((int)$summary['paid'],0,',','.') }}</div>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200">
      <div class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Affiliate Orders' : 'Order Affiliate' }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Invoice</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Final</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Commission</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Status</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Created</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          @forelse($orders as $o)
          <tr>
            <td class="px-4 py-3 font-bold text-slate-800">{{ $o->invoice_number }}</td>
            <td class="px-4 py-3 font-semibold text-slate-800">Rp {{ number_format((int)$o->final_price,0,',','.') }}</td>
            <td class="px-4 py-3 font-semibold text-slate-800">Rp {{ number_format((int)$o->affiliate_commission_amount,0,',','.') }}</td>
            <td class="px-4 py-3 font-semibold text-slate-800">{{ strtoupper($o->affiliate_commission_status) }}</td>
            <td class="px-4 py-3 text-slate-700">{{ optional($o->created_at)->format('d M Y H:i') }}</td>
          </tr>
          @empty
          <tr>
            <td class="px-4 py-6 text-slate-600" colspan="5">{{ $isEn ? 'No affiliate orders yet.' : 'Belum ada order affiliate.' }}</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4">
      {{ $orders->links() }}
    </div>
  </div>
</div>
@endsection