@extends('emails.tabungan_umrah.layout')
@section('email_title', 'Akun Tabungan Umrah Terverifikasi')
@section('title', 'Akun Tabungan Umrah Anda Terverifikasi')

@section('content')
<p>Halo <b>{{ $account->full_name }}</b>,</p>
<p>Akun Tabungan Umrah Anda telah <b>terverifikasi</b>. Anda sekarang dapat mulai melakukan setoran.</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Target</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            Rp {{ number_format((int)$account->target_amount, 0, ',', '.') }}
        </td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Target Keberangkatan</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            {{ $account->target_departure_date ? $account->target_departure_date->format('Y-m-d') : '-' }}
        </td>
    </tr>
</table>

<p style="margin-top:14px;">Pastikan setiap setoran dilakukan ke rekening tujuan yang tersedia di sistem, lalu unggah bukti pembayaran untuk verifikasi.</p>
@endsection

@section('cta')
<a href="{{ route('user.tabungan-umrah.index') }}"
   style="display:inline-block;background:#0194F3;color:#ffffff;text-decoration:none;padding:10px 14px;border-radius:12px;font-family:Arial,Helvetica,sans-serif;font-weight:800;font-size:13px;">
    Mulai Setor Sekarang
</a>
@endsection
