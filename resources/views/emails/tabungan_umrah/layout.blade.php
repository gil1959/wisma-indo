<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('email_title', 'Bintang Wisata')</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="width:640px;max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;">
                <tr>
                    <td style="padding:18px 22px;background:#0194F3;">
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:800;color:#ffffff;letter-spacing:0.2px;">
                            {{ config('app.name', 'Bintang Wisata') }}
                        </div>
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;color:rgba(255,255,255,0.9);margin-top:4px;">
                            Tabungan Umrah
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:22px;">
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:800;color:#0f172a;margin:0 0 10px;">
                            @yield('title')
                        </div>

                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6;color:#334155;">
                            @yield('content')
                        </div>

                        @hasSection('cta')
                            <div style="margin-top:18px;">
                                @yield('cta')
                            </div>
                        @endif

                        <div style="margin-top:20px;padding-top:16px;border-top:1px solid #e2e8f0;">
                            <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#64748b;">
                                Jika Anda tidak merasa melakukan aktivitas ini, abaikan email ini atau hubungi admin.
                            </div>
                            <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#64748b;margin-top:8px;">
                                © {{ date('Y') }} {{ config('app.name', 'Bintang Wisata') }}. Semua hak dilindungi.
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <div style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#94a3b8;margin-top:10px;">
                Email ini dikirim otomatis, mohon tidak membalas.
            </div>
        </td>
    </tr>
</table>
</body>
</html>
