@extends('emails.tabungan_umrah.layout')
@section('email_title', 'Update Status Setoran Tabungan Umrah')
@section('title', 'Update Status Setoran')

@section('content')
<p>Halo Admin,</p>
<p>Berikut update status setoran Tabungan Umrah.</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Nama</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">{{ $deposit->account->full_name }}</td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Nominal</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            Rp {{ number_format((int)$deposit->amount, 0, ',', '.') }}
        </td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Status Baru</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            {{ strtoupper($deposit->status) }}
        </td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Catatan</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            {{ $deposit->note ?: '-' }}
        </td>
    </tr>
</table>

<p style="margin-top:14px;">
    Bukti pembayaran <b>dilampirkan</b> pada email ini (jika tersedia) untuk memudahkan audit.
</p>
@endsection

@section('cta')
<a href="{{ route('admin.tabungan-umrah.deposits.show', $deposit->id) }}"
   style="display:inline-block;background:#0194F3;color:#ffffff;text-decoration:none;padding:10px 14px;border-radius:12px;font-family:Arial,Helvetica,sans-serif;font-weight:800;font-size:13px;">
    Buka Detail Setoran
</a>
@endsection
