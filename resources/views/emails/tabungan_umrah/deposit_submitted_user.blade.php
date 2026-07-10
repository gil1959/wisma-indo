@extends('emails.tabungan_umrah.layout')
@section('email_title', 'Setoran Tabungan Umrah Diterima')
@section('title', 'Setoran Anda Kami Terima')

@section('content')
<p>Halo <b>{{ $deposit->account->full_name }}</b>,</p>
<p>Setoran Tabungan Umrah Anda telah kami terima dan saat ini menunggu verifikasi admin.</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Nominal</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            Rp {{ number_format((int)$deposit->amount, 0, ',', '.') }}
        </td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Rekening Tujuan</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            {{ optional($deposit->paymentMethod)->bank_name }} - {{ optional($deposit->paymentMethod)->account_number }} ({{ optional($deposit->paymentMethod)->account_holder }})
        </td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Status</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">Waiting Verification</td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Waktu Submit</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            {{ optional($deposit->submitted_at)->format('Y-m-d H:i') ?? optional($deposit->created_at)->format('Y-m-d H:i') }}
        </td>
    </tr>
</table>

<p style="margin-top:14px;">
    Untuk arsip Anda, bukti pembayaran juga <b>dilampirkan</b> pada email ini (jika tersedia).
</p>
@endsection

@section('cta')
<a href="{{ route('user.tabungan-umrah.index') }}"
   style="display:inline-block;background:#0194F3;color:#ffffff;text-decoration:none;padding:10px 14px;border-radius:12px;font-family:Arial,Helvetica,sans-serif;font-weight:800;font-size:13px;">
    Lihat Status Tabungan
</a>
@endsection
