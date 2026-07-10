@component('mail::message')
# Pendaftaran Partner Ditolak

Aplikasi partner ditolak:

- Nama: {{ $app->name }}
- Email: {{ $app->email }}

Catatan:
{{ $app->review_note }}

@endcomponent
