@extends('layouts.front') {{-- atau layout apa pun yang lu pakai --}}

@section('title', 'Pembayaran Pesanan ' . $booking->code)

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-4">
        Pembayaran Pesanan {{ $booking->code }}
    </h1>

    <div class="bg-white shadow rounded-lg p-4 mb-4">
        <h2 class="font-semibold mb-2">Rincian Pesanan</h2>

        @foreach($items as $item)
            <div class="text-sm mb-2">
                <div>{{ $item->item_type === 'tour' ? 'Paket Tour' : 'Item' }} #{{ $item->item_id }}</div>
                <div>{{ $item->qty }} pax x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                <div class="font-semibold">
                    Subtotal: Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </div>
            </div>
        @endforeach

        @if($booking->discount_amount > 0)
            <div class="text-sm text-gray-600">
                Diskon: - Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}
            </div>
        @endif

        <div class="mt-3 text-lg font-bold">
            Total Bayar: Rp {{ number_format($booking->final_amount, 0, ',', '.') }}
        </div>

        @if($booking->with_flight && $booking->total_amount == $booking->final_amount && str_contains($booking->notes ?? '', 'KONFIRMASI HARGA TIKET'))
            <p class="text-xs text-orange-600 mt-2">
                * Harga di atas belum termasuk tiket pesawat. Admin akan menghubungi Anda via WhatsApp
                untuk konfirmasi harga tiket.
            </p>
        @endif
    </div>

    <div class="bg-white shadow rounded-lg p-4 mb-4">
        <h2 class="font-semibold mb-2">Metode Pembayaran</h2>

        <p class="text-sm text-gray-600 mb-3">
            Silakan transfer ke salah satu rekening berikut lalu upload bukti bayar di menu yang akan kita buat
            di panel user / admin.
        </p>

        <ul class="text-sm space-y-2">
            @foreach($bankAccounts as $bank)
                <li>
                    <strong>{{ $bank->bank_name }}</strong><br>
                    a.n {{ $bank->account_name }}<br>
                    No. Rekening: {{ $bank->account_number }}
                </li>
            @endforeach
        </ul>
    </div>

    {{-- nanti di sini bisa diintegrasi payment gateway --}}
</div>
@endsection
