<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Lead Baru dari WismaIndo</title>
    <style>
        body {
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            color: #334155;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #0194F3;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .content p {
            margin-top: 0;
            margin-bottom: 16px;
            font-size: 16px;
        }
        .highlight-box {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
        }
        .highlight-box h3 {
            margin-top: 0;
            margin-bottom: 16px;
            color: #0f172a;
            font-size: 18px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
        }
        .info-row {
            margin-bottom: 12px;
        }
        .info-label {
            font-weight: 600;
            color: #64748b;
            font-size: 14px;
            display: block;
            margin-bottom: 4px;
        }
        .info-value {
            font-weight: 700;
            color: #1e293b;
            font-size: 16px;
        }
        .message-box {
            background-color: #ffffff;
            padding: 16px;
            border-radius: 8px;
            border: 1px dashed #cbd5e1;
            margin-top: 8px;
            font-style: italic;
            color: #475569;
        }
        .btn-action {
            display: inline-block;
            background-color: #0194F3;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            text-align: center;
            margin-top: 10px;
            width: 100%;
            box-sizing: border-box;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #64748b;
        }
        .badge {
            display: inline-block;
            background-color: #fef3c7;
            color: #d97706;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>WismaIndo CRM</h1>
        </div>
        <div class="content">
            <div style="text-align: center;">
                <span class="badge">⭐ PRIORITAS: REFERRAL PUSAT</span>
            </div>
            
            <p>Halo, <strong>{{ $lead->partner->name }}</strong>,</p>
            <p>Selamat! Admin WismaIndo baru saja mendistribusikan satu calon pembeli (Lead) yang sedang mencari properti kepada Anda.</p>
            <p>Berikut adalah rincian informasi pembeli tersebut:</p>

            <div class="highlight-box">
                <h3>Detail Calon Pembeli</h3>
                
                <div class="info-row">
                    <span class="info-label">Nama Pembeli</span>
                    <span class="info-value">{{ $lead->name }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">No. WhatsApp</span>
                    <span class="info-value">{{ $lead->phone }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Properti yang Diminati</span>
                    <span class="info-value" style="color: #0194F3;">
                        {{ $lead->listing ? $lead->listing->title : 'Pencarian Umum (Belum ada listing spesifik)' }}
                    </span>
                </div>

                @if($lead->message)
                <div class="info-row" style="margin-top: 20px;">
                    <span class="info-label">Catatan dari Admin / Pesan Pembeli:</span>
                    <div class="message-box">
                        "{{ $lead->message }}"
                    </div>
                </div>
                @endif
            </div>

            <p style="text-align: center; margin-top: 30px; margin-bottom: 10px; font-weight: 600; color: #64748b;">
                Segera tindak lanjuti lead ini melalui Dasbor CRM Anda!
            </p>
            
            <a href="{{ route('partner.leads.show', $lead->id) }}" class="btn-action">Tindak Lanjuti di Dasbor CRM</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} WismaIndo. Hak Cipta Dilindungi.<br>
            Email otomatis, mohon tidak dibalas.
        </div>
    </div>
</body>
</html>
