@extends('layouts.front')
@php
$isEn = app()->getLocale() === 'en';
@endphp
@section('title', ($isEn ? 'Payment ' : 'Pembayaran ') . $order->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 space-y-6">

    @if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 text-red-800 p-3 rounded">
        {{ session('error') }}
    </div>
    @endif

    {{-- RINGKASAN PESANAN --}}
    <div class="bg-white shadow rounded-xl p-5 space-y-2">
        <h1 class="text-xl font-bold mb-1">{{ $isEn ? 'Order Summary' : 'Ringkasan Pesanan' }}</h1>
        <p class="text-sm text-gray-600">Invoice: <b>{{ $order->invoice_number }}</b></p>
        <p class="text-sm">{{ strtoupper($order->type) }} - {{ $order->product_name }}</p>
        <p class="text-sm">
            Atas nama: <b>{{ $order->customer_name }}</b> ({{ $order->customer_email }})
        </p>
        <div class="flex justify-between mt-3 border-t pt-2">
            <span class="font-semibold">{{ $isEn ? 'Total to be paid' : 'Total yang harus dibayar' }}</span>
            <span class="font-bold text-lg">
                Rp {{ number_format($order->final_price,0,',','.') }}
            </span>
        </div>
    </div>

    @if($type === 'manual')
    {{-- TRANSFER MANUAL --}}
    <div class="bg-white shadow rounded-xl p-5 space-y-4">
        <h2 class="text-lg font-semibold">Transfer Bank (Manual)</h2>

        <p class="text-sm text-gray-600">
            {{ $isEn ? 'Please transfer amount' : 'Silakan transfer sebesar' }} <b>Rp {{ number_format($order->final_price,0,',','.') }}</b>
            {{ $isEn ? 'to the following account, then upload proof of transfer.' : 'ke rekening berikut, lalu upload bukti transfer.' }}
        </p>

        <div class="border rounded-lg p-3 bg-gray-50">
            <p><b>{{ $manualMethod->bank_name }}</b></p>
            <p> {{ $isEn ? 'account number' : 'No. Rekening' }}: <b>{{ $manualMethod->account_number }}</b></p>
            <p>{{ $isEn ? 'On behalf of' : 'Atas nama' }} {{ $manualMethod->account_holder }}</p>
        </div>

        <form method="POST"
            action="{{ route('payment.manual.submit', $order) }}"
            enctype="multipart/form-data"
            class="space-y-3">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1">
                    {{ $isEn ? 'Format: JPG/JPEG/PNG Max: 2MB' : 'Format: JPG/JPEG/PNG  Maks: 2MB' }}
                </label>
                <input type="file" name="proof" class="form-control" required>
                @error('proof')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold">
                Upload & Selesaikan
            </button>
        </form>
    </div>
    @elseif($type === 'gateway' && $gateway)
    {{-- PAYMENT GATEWAY --}}
    <div class="bg-white shadow rounded-xl p-5 space-y-4">
        <h2 class="text-lg font-semibold">Pembayaran Online ({{ ucfirst($gateway->name) }})</h2>

        <p class="text-sm text-gray-600">
            Kamu akan diarahkan ke halaman pembayaran {{ ucfirst($gateway->name) }}.
            Setelah pembayaran berhasil, sistem akan mengupdate status order secara otomatis
            (setelah integrasi gateway selesai dikonfigurasi).
        </p>

        <form method="POST" action="{{ route('payment.gateway.start', $order) }}">
            @csrf
            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                Bayar via {{ ucfirst($gateway->name) }}
            </button>
        </form>

        <p class="text-xs text-gray-500 mt-2">
            * Pastikan jangan menutup browser sampai proses pembayaran selesai.
        </p>
    </div>
    @endif

</div>
@endsection