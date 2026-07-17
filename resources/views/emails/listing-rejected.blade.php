<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revisi Iklan Diperlukan</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .header { background-color: #ef4444; padding: 30px 20px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
        .content { padding: 30px 40px; }
        .content p { font-size: 16px; line-height: 1.6; margin-bottom: 20px; }
        .btn { display: inline-block; background-color: #0194F3; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; font-size: 16px; margin-top: 10px; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 14px; color: #64748b; }
        .listing-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .listing-title { font-size: 18px; font-weight: bold; color: #0f172a; margin: 0 0 10px 0; }
        .note-card { background: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #ef4444; border-radius: 8px; padding: 15px 20px; margin-bottom: 20px; }
        .note-title { font-size: 14px; font-weight: bold; color: #b91c1c; margin: 0 0 5px 0; text-transform: uppercase; }
        .note-content { font-size: 15px; color: #7f1d1d; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Revisi Iklan Diperlukan ⚠️</h1>
        </div>
        <div class="content">
            <p>Halo, <strong>{{ $listing->user->name ?? 'User' }}</strong>,</p>
            <p>Terima kasih telah mengajukan iklan di Wisma Indo. Setelah kami me-review iklan Anda, kami menemukan beberapa hal yang perlu diperbaiki sebelum iklan dapat tayang.</p>
            
            <div class="listing-card">
                <h3 class="listing-title">{{ $listing->title }}</h3>
                <p style="margin: 0; font-size: 14px; color: #64748b;">Kategori: {{ ucfirst($listing->category) }}</p>
            </div>
            
            <div class="note-card">
                <p class="note-title">Catatan dari Tim Review:</p>
                <p class="note-content">{{ $note }}</p>
            </div>
            
            <p>Anda dapat mengedit iklan Anda kembali untuk memperbaiki hal tersebut. Setelah Anda menyimpannya, iklan akan kami review ulang secepatnya!</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('iklan.saya') }}" class="btn">Perbaiki Iklan Saya</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Wisma Indo. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
