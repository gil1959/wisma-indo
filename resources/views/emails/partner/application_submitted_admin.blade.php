@component('mail::message')
# Pendaftaran Partner Baru

Ada pendaftaran partner baru:

- Nama: {{ $app->name }}
- Email: {{ $app->email }}
- No HP: {{ $app->phone }}
- Status: {{ $app->status }}

Silakan review di Admin Panel: **Partners → Applications**.

@endcomponent
