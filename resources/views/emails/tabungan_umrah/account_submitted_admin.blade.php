@extends('emails.tabungan_umrah.layout')
@section('email_title', 'Registrasi Tabungan Umrah Baru')
@section('title', 'Registrasi Tabungan Umrah Baru (Perlu Verifikasi)')

@section('content')
<p>Halo Admin,</p>
<p>Terdapat registrasi <b>Tabungan Umrah</b> baru yang membutuhkan verifikasi.</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
    <tr>
        <td style="padding:10px 12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#334155;background:#f8fafc;"><b>Nama</b></td>
        <td style="padding:10px 12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#0f172a;background:#ffffff;">{{ $account->full_name }}</td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#334155;background:#f8fafc;"><b>User ID</b></td>
        <td style="padding:10px 12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#0f172a;background:#ffffff;">{{ $account->user_id }}</td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#334155;background:#f8fafc;"><b>WhatsApp</b></td>
        <td style="padding:10px 12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#0f172a;background:#ffffff;">{{ $account->whatsapp }}</td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#334155;background:#f8fafc;"><b>Jenis</b></td>
        <td style="padding:10px 12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#0f172a;background:#ffffff;">
            {{ $account->saving_type === 'haji_furoda' ? 'Haji Furoda' : 'Umroh Reguler' }}
        </td>
    </tr>
    <tr>
        <td style="padding:10px 12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#334155;background:#f8fafc;"><b>Tanggal Registrasi</b></td>
        <td style="padding:10px 12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#0f172a;background:#ffffff;">
            {{ optional($account->created_at)->format('Y-m-d H:i') }}
        </td>
    </tr>
</table>

<p style="margin-top:14px;">Silakan verifikasi melalui Admin Panel.</p>
@endsection

@section('cta')
<a href="{{ route('admin.tabungan-umrah.accounts.show', $account->id) }}"
   style="display:inline-block;background:#0194F3;color:#ffffff;text-decoration:none;padding:10px 14px;border-radius:12px;font-family:Arial,Helvetica,sans-serif;font-weight:800;font-size:13px;">
    Buka Detail Akun
</a>
@endsection
