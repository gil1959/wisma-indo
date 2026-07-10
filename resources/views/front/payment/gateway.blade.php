@extends('layouts.front')

@php
$isEn = app()->getLocale() === 'en';
@endphp

@section('title', ($isEn ? 'Online Payment ' : 'Pembayaran Online ') . $order->invoice_number)


@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 space-y-6">

    @if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 text-red-800 p-3 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow rounded-xl p-5 space-y-2">
        <h1 class="text-xl font-bold mb-1">{{ $isEn ? 'Order Summary' : 'Ringkasan Pesanan' }}</h1>
        <p class="text-sm text-gray-600">
            {{ $isEn ? 'Invoice' : 'Invoice' }}: <b>{{ $order->invoice_number }}</b>
        </p>
        <p class="text-sm">{{ strtoupper($order->type) }} - {{ $order->product_name }}</p>
        <p class="text-sm">
            {{ $isEn ? 'On behalf of' : 'Atas nama' }}: <b>{{ $order->customer_name }}</b> ({{ $order->customer_email }})
        </p>

        @php
        $totalBayar = $order->payable_amount ?? $order->final_price;
        @endphp

        <div class="mt-3 border-t pt-2">
            <div class="flex justify-between">
                <span class="font-semibold">{{ $isEn ? 'Total to be paid' : 'Total yang harus dibayar' }}</span>
                <span class="font-bold text-lg">Rp {{ number_format($totalBayar,0,',','.') }}</span>
            </div>

            @if(!is_null($order->unique_code) && !is_null($order->payable_amount))
            <p class="text-xs text-gray-500 mt-1">
                Termasuk kode unik: <b>{{ $order->unique_code }}</b>
            </p>
            @endif
        </div>
    </div>

    <div class="bg-white shadow rounded-xl p-5 space-y-4">
        <h2 class="text-lg font-semibold">{{ $isEn ? 'Online Payment' : 'Pembayaran Online' }} ({{ $gateway->label ?? ucfirst($gateway->name) }})</h2>

        <p class="text-sm text-gray-600">
            {{ $isEn ? 'Method selected' : 'Metode dipilih' }}: <b>{{ $gatewayMethodLabel }}</b>
        </p>

        <form method="POST" action="{{ route('payment.gateway.start', $order) }}">
            @csrf
            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                {{ $isEn ? 'Continue Paying' : 'Lanjut Bayar' }}
            </button>
        </form>

        @if(session('show_change_method'))
        <a href="{{ route('checkout.show', $order->id) }}#payment-method"
            class="block w-full text-center border border-slate-300 hover:border-slate-400 text-slate-700 py-3 rounded-lg font-semibold">
            {{ $isEn ? 'Select another payment method' : 'Pilih metode pembayaran yang lain' }}
        </a>
        @endif

    </div>

</div>
@endsection