@component('mail::message')
# Partner Disetujui

Aplikasi partner berikut sudah disetujui:

- Nama: {{ $user->name }}
- Email: {{ $user->email }}

Role: **partner** sudah di-assign.

@endcomponent
