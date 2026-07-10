@extends('emails.tabungan_umrah.layout')
@section('email_title', 'Registrasi Tabungan Umrah Ditolak')
@section('title', 'Registrasi Tabungan Umrah Ditolak')

@section('content')
<p>Halo <b>{{ $account->full_name }}</b>,</p>
<p>Mohon maaf, registrasi Tabungan Umrah Anda <b>belum dapat kami verifikasi</b>.</p>

@if($account->rejected_reason)
    <div style="margin-top:12px;padding:12px;border:1px solid #fecdd3;background:#fff1f2;border-radius:12px;">
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:800;color:#9f1239;margin-bottom:6px;">
            Alasan
        </div>
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.6;color:#9f1239;">
            {{ $account->rejected_reason }}
        </div>
    </div>
@endif

<p style="margin-top:14px;">Silakan lakukan registrasi ulang dengan data yang lebih lengkap/benar.</p>
@endsection

@section('cta')
<a href="{{ route('user.tabungan-umrah.index') }}"
   style="display:inline-block;background:#0194F3;color:#ffffff;text-decoration:none;padding:10px 14px;border-radius:12px;font-family:Arial,Helvetica,sans-serif;font-weight:800;font-size:13px;">
    Buka Tabungan Umrah
</a>
@endsection
