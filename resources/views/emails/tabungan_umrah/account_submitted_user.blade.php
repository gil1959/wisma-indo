@extends('emails.tabungan_umrah.layout')
@section('email_title', 'Registrasi Tabungan Umrah Diterima')
@section('title', 'Registrasi Tabungan Umrah Diterima')

@section('content')
<p>Halo <b>{{ $account->full_name }}</b>,</p>
<p>Registrasi Tabungan Umrah Anda telah kami terima dan sedang menunggu verifikasi admin.</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Status</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">Pending Verifikasi</td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Jenis</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            {{ $account->saving_type === 'haji_furoda' ? 'Haji Furoda' : 'Umroh Reguler' }}
        </td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-size:13px;color:#334155;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;"><b>Tanggal Registrasi</b></td>
        <td style="padding:10px 12px;font-size:13px;color:#0f172a;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">
            {{ optional($account->created_at)->format('Y-m-d H:i') }}
        </td>
    </tr>
</table>

<p style="margin-top:14px;">Langkah selanjutnya: setelah akun Anda diverifikasi, Anda dapat mulai melakukan setoran dan mengunggah bukti pembayaran.</p>
@endsection

@section('cta')
<a href="{{ route('user.tabungan-umrah.index') }}"
   style="display:inline-block;background:#0194F3;color:#ffffff;text-decoration:none;padding:10px 14px;border-radius:12px;font-family:Arial,Helvetica,sans-serif;font-weight:800;font-size:13px;">
    Buka Tabungan Umrah
</a>
@endsection
