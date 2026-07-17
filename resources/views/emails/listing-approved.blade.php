<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Iklan Anda Disetujui</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .header { background-color: #0194F3; padding: 30px 20px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
        .content { padding: 30px 40px; }
        .content p { font-size: 16px; line-height: 1.6; margin-bottom: 20px; }
        .btn { display: inline-block; background-color: #0194F3; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; font-size: 16px; margin-top: 10px; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 14px; color: #64748b; }
        .listing-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .listing-title { font-size: 18px; font-weight: bold; color: #0f172a; margin: 0 0 10px 0; }
        .listing-price { font-size: 16px; font-weight: bold; color: #0194F3; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Hore! Iklan Anda Telah Tayang 🎉</h1>
        </div>
        <div class="content">
            <p>Halo, <strong>{{ $listing->user->name ?? 'User' }}</strong>!</p>
            <p>Kabar gembira! Iklan yang Anda ajukan telah selesai kami review dan saat ini <strong>sudah tayang</strong> di Wisma Indo.</p>
            
            <div class="listing-card">
                <h3 class="listing-title">{{ $listing->title }}</h3>
                <p class="listing-price">Rp {{ number_format($listing->price, 0, ',', '.') }}</p>
                <p style="margin: 10px 0 0 0; font-size: 14px;">Kategori: {{ ucfirst($listing->category) }}</p>
            </div>
            
            <p>Iklan Anda kini dapat dilihat oleh calon pembeli atau penyewa yang mengunjungi platform kami. Anda bisa mengelola iklan Anda melalui dashboard akun.</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('home') }}" class="btn">Lihat Beranda</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Wisma Indo. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
