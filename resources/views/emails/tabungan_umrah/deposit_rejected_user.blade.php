@extends('emails.tabungan_umrah.layout')
@section('email_title', 'Setoran Tabungan Umrah Ditolak')
@section('title', 'Setoran Ditolak')

@section('content')
<p>Halo <b>{{ $deposit->account->full_name }}</b>,</p>
<p>Setoran Tabungan Umrah Anda <b>ditolak</b>.</p>

@if($deposit->note)
    <div style="margin-top:12px;padding:12px;border:1px solid #fecdd3;background:#fff1f2;border-radius:12px;">
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:800;color:#9f1239;margin-bottom:6px;">
            Catatan Admin
        </div>
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.6;color:#9f1239;">
            {{ $deposit->note }}
        </div>
    </div>
@endif

<p style="margin-top:14px;">
    Silakan lakukan setoran ulang sesuai rekening tujuan yang tersedia dan unggah bukti pembayaran yang jelas.
</p>
@endsection

@section('cta')
<a href="{{ route('user.tabungan-umrah.deposits.create') }}"
   style="display:inline-block;background:#0194F3;color:#ffffff;text-decoration:none;padding:10px 14px;border-radius:12px;font-family:Arial,Helvetica,sans-serif;font-weight:800;font-size:13px;">
    Buat Setoran Baru
</a>
@endsection
