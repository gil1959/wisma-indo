@extends('layouts.front')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Pembayaran Midtrans</h3>

    <p>Silakan klik tombol di bawah untuk melanjutkan pembayaran.</p>

    <button id="payBtn" class="btn btn-primary">Bayar Sekarang</button>
</div>
@endsection

@push('scripts')
<script src="{{ $snapJs }}" data-client-key="{{ $clientKey }}"></script>
<script>
document.getElementById('payBtn').addEventListener('click', function () {
    window.snap.pay(@json($snapToken), {
        onSuccess: function(result) {
            window.location.href = @json(route('payment.success'));
        },
        onPending: function(result) {
            window.location.href = @json(route('payment.pending'));
        },
        onError: function(result) {
            window.location.href = @json(route('payment.failed'));
        },
        onClose: function() {
            // user nutup popup
        }
    });
});
</script>
@endpush
