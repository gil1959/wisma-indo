@component('mail::message')
# Pendaftaran Partner Ditolak

Halo **{{ $app->name }}**,

Mohon maaf, pendaftaran partner kamu **ditolak**.

**Catatan admin:**
{{ $app->review_note }}

Jika ingin mendaftar ulang, silakan pastikan data & dokumen sudah sesuai.

Terima kasih,  
{{ config('app.name') }}
@endcomponent
