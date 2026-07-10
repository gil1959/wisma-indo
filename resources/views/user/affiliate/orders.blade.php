@extends('user.layouts.app')

@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $isEn ? 'Affiliate Orders' : 'Order Affiliate')
@section('page-title', $isEn ? 'Affiliate Orders' : 'Order Affiliate')
@section('page-subtitle', $isEn ? 'Orders generated from your affiliate links' : 'Daftar order yang masuk dari link affiliate kamu')


@section('content')
<div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm mb-5">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-sm font-extrabold text-slate-900">Orders</h2>
        <span class="text-xs font-extrabold px-3 py-1 rounded-full border shrink-0"
            style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
            Affiliate
        </span>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left">
                    <th class="px-5 py-3 text-xs font-extrabold uppercase text-slate-600">Invoice</th>
                    <th class="px-5 py-3 text-xs font-extrabold uppercase text-slate-600">Type</th>
                    <th class="px-5 py-3 text-xs font-extrabold uppercase text-slate-600">Product</th>
                    <th class="px-5 py-3 text-xs font-extrabold uppercase text-slate-600">Final</th>
                    <th class="px-5 py-3 text-xs font-extrabold uppercase text-slate-600">Order Status</th>
                    <th class="px-5 py-3 text-xs font-extrabold uppercase text-slate-600">Commission</th>
                    <th class="px-5 py-3 text-xs font-extrabold uppercase text-slate-600">Created</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse($orders as $order)
                @php
                // label type biar rapi
                $typeLabel = match($order->type) {
                'tour' => 'Tour',
                'rent_car' => 'Rent Car',
                default => strtoupper((string) $order->type),
                };

                // badge status order (admin order_status: pending/approved/rejected)
                $orderStatus = (string) ($order->order_status ?? 'pending');
                $orderBadge = [
                'bg' => 'rgba(1,148,243,0.08)',
                'bd' => 'rgba(1,148,243,0.22)',
                'tx' => '#055a93',
                'label' => strtoupper($orderStatus),
                ];

                if ($orderStatus === 'approved') {
                $orderBadge = ['bg'=>'rgba(16,185,129,0.10)','bd'=>'rgba(16,185,129,0.25)','tx'=>'#065f46','label'=>'APPROVED'];
                } elseif ($orderStatus === 'rejected') {
                $orderBadge = ['bg'=>'rgba(239,68,68,0.10)','bd'=>'rgba(239,68,68,0.25)','tx'=>'#7f1d1d','label'=>'REJECTED'];
                }

                // badge status komisi (pending/approved/paid/cancelled)
                $commStatus = (string) ($order->affiliate_commission_status ?? 'pending');
                $commBadge = [
                'bg' => 'rgba(1,148,243,0.08)',
                'bd' => 'rgba(1,148,243,0.22)',
                'tx' => '#055a93',
                'label' => strtoupper($commStatus),
                ];

                if ($commStatus === 'approved') {
                $commBadge = ['bg'=>'rgba(16,185,129,0.10)','bd'=>'rgba(16,185,129,0.25)','tx'=>'#065f46','label'=>'APPROVED'];
                } elseif ($commStatus === 'paid') {
                $commBadge = ['bg'=>'rgba(168,85,247,0.10)','bd'=>'rgba(168,85,247,0.25)','tx'=>'#5b21b6','label'=>'PAID'];
                } elseif ($commStatus === 'cancelled') {
                $commBadge = ['bg'=>'rgba(239,68,68,0.10)','bd'=>'rgba(239,68,68,0.25)','tx'=>'#7f1d1d','label'=>'CANCELLED'];
                }
                @endphp

                <tr>
                    <td class="px-5 py-4">
                        <div class="font-extrabold text-slate-900">{{ $order->invoice_number }}</div>
                    </td>

                    <td class="px-5 py-4">
                        <div class="font-semibold text-slate-800">{{ $typeLabel }}</div>
                    </td>

                    <td class="px-5 py-4">
                        <div class="font-semibold text-slate-800">{{ $order->product_name }}</div>
                        <div class="text-xs text-slate-500">#{{ $order->product_id }}</div>
                    </td>

                    <td class="px-5 py-4">
                        <div class="font-extrabold text-slate-900">
                            Rp {{ number_format((int) $order->final_price, 0, ',', '.') }}
                        </div>
                    </td>

                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full border text-xs font-extrabold"
                            style="background: {{ $orderBadge['bg'] }}; border-color: {{ $orderBadge['bd'] }}; color: {{ $orderBadge['tx'] }};">
                            {{ $orderBadge['label'] }}
                        </span>
                    </td>

                    <td class="px-5 py-4">
                        <div class="font-extrabold text-slate-900">
                            Rp {{ number_format((int) ($order->affiliate_commission_amount ?? 0), 0, ',', '.') }}
                        </div>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full border text-xs font-extrabold"
                                style="background: {{ $commBadge['bg'] }}; border-color: {{ $commBadge['bd'] }}; color: {{ $commBadge['tx'] }};">
                                {{ $commBadge['label'] }}
                            </span>
                        </div>
                    </td>

                    <td class="px-5 py-4">
                        <div class="text-sm font-semibold text-slate-800">
                            {{ optional($order->created_at)->format('d M Y') }}
                        </div>
                        <div class="text-xs text-slate-500">
                            {{ optional($order->created_at)->format('H:i') }}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                        {{ $isEn ? 'No affiliate orders yet.' : 'Belum ada order affiliate.' }}

                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection