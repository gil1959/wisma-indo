<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - {{ config('app.name') }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px 0; font-size: 24px; }
        .header p { margin: 0; color: #666; }
        table { w-full; border-collapse: collapse; margin-bottom: 20px; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .summary-box { border: 1px solid #ddd; padding: 15px; width: 48%; border-radius: 5px; }
        .summary-title { font-size: 12px; font-weight: bold; color: #666; text-transform: uppercase; margin-bottom: 5px; }
        .summary-value { font-size: 20px; font-weight: bold; }
        .success { color: #10b981; }
        .pending { color: #f59e0b; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>Laporan Transaksi</h1>
        <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="summary-title">Total Pemasukan (Sukses)</div>
            <div class="summary-value success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">Total Menunggu Pembayaran (Pending)</div>
            <div class="summary-value pending">Rp {{ number_format($totalPending, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>ID Transaksi</th>
                <th>User / Partner</th>
                <th>Jenis Transaksi</th>
                <th class="text-right">Nominal</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $tx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $tx->created_at->format('d M Y, H:i') }}</td>
                <td>{{ $tx->id }}</td>
                <td>{{ $tx->user }}</td>
                <td>{{ $tx->type }}</td>
                <td class="text-right">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($tx->status == 'success')
                        <span class="success">Sukses</span>
                    @elseif($tx->status == 'pending')
                        <span class="pending">Pending</span>
                    @else
                        <span>Gagal / Expired</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 30px;">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print Dokumen</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">Tutup Tab</button>
    </div>

</body>
</html>
