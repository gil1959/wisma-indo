@component('mail::message')
# Akun Partner Sudah Diverifikasi

Halo **{{ $user->name }}**,

Akun partner kamu sudah **disetujui dan aktif**.

**Login:**
- URL: {{ url('/login') }}
- Email: {{ $user->email }}
- Password: {{ $plainPassword }}

Setelah login, kamu akan masuk ke dashboard partner: {{ url('/partner/dashboard') }}

Terima kasih,  
{{ config('app.name') }}
@endcomponent
