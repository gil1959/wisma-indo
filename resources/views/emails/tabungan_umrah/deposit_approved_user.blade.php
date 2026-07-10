@extends('emails.tabungan_umrah.layout')
@section('email_title', 'Setoran Tabungan Umrah Disetujui')
@section('title', 'Setoran Disetujui')

@section('content')
<p>Halo <b>{{ $deposit->account->full_name }}</b>,</p>
<p>Setoran Tabungan Umrah Anda telah <b>disetujui</b>.</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Nominal</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            Rp {{ number_format((int)$deposit->amount, 0, ',', '.') }}
        </td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Status</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">APPROVED</td>
    </tr>
</table>

<p style="margin-top:14px;">Terima kasih. Anda dapat melihat riwayat setoran dan progres tabungan melalui dashboard.</p>
@endsection

@section('cta')
<a href="{{ route('user.tabungan-umrah.index') }}"
   style="display:inline-block;background:#0194F3;color:#ffffff;text-decoration:none;padding:10px 14px;border-radius:12px;font-family:Arial,Helvetica,sans-serif;font-weight:800;font-size:13px;">
    Lihat Tabungan Umrah
</a>
@endsection
