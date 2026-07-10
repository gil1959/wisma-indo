@extends('layouts.front')

@section('content')

<div class="container py-5">
    <h2 class="fw-bold mb-3">Booking Summary</h2>

    <div class="p-4 shadow-sm rounded" style="background:#f8faff;">

        <h4 class="fw-semibold">{{ $package->title }}</h4>
        <p class="text-muted mb-2">Rp{{ number_format($package->price_per_hour) }} / Jam</p>

        <hr>

        <p><strong>Pickup:</strong> {{ $pickup }}</p>
        <p><strong>Return:</strong> {{ $return }}</p>

        <p><strong>Total Jam:</strong> {{ $hours }}</p>

        <p class="fw-bold">
            Total Harga: Rp{{ number_format($total, 0, ',', '.') }}
        </p>

        <p class="text-muted mt-3">
            *Ini halaman sementara. Nanti diganti halaman pembayaran final.
        </p>
    </div>
</div>

@endsection