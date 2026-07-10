@component('mail::message')
# Pendaftaran Partner Diterima

Halo **{{ $app->name }}**,

Pendaftaran partner kamu sudah kami terima dan saat ini **sedang diverifikasi oleh admin**.

**Ringkasan data:**
- Email: {{ $app->email }}
- No HP: {{ $app->phone }}
- Tanggal daftar: {{ optional($app->submitted_at)->format('d M Y H:i') }}

Kami akan mengirim email lagi setelah proses verifikasi selesai.

Terima kasih,  
{{ config('app.name') }}
@endcomponent
