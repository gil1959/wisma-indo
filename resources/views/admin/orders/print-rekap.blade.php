<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #0f172a; margin: 0; background: #fff; }
        .wrap { padding: 18px; }
        h1 { font-size: 16px; margin: 0 0 6px 0; }
        .muted { color:#64748b; margin: 0 0 12px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #111827; padding: 6px 8px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; }
        .right { text-align: right; }
        .top { display:flex; justify-content: space-between; align-items:flex-start; gap: 12px; margin-bottom: 12px; }
        .summary { width: 320px; border: 1px solid #111827; padding: 10px; }
        .summary div { display:flex; justify-content: space-between; margin: 4px 0; }
        @media print {
            .no-print { display:none !important; }
        }
    </style>
</head>
<body>
<div class="wrap">

    <div class="top">
        <div>
            <h1>Rekap Orders (Pembukuan)</h1>
            <p class="muted">
                Periode: <b>{{ \Carbon\Carbon::parse($from)->format('d/m/Y') }}</b>
                s/d <b>{{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</b>
            </p>
            <p class="muted">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <div class="summary">
            <div><span><b>Total Order</b></span><span>{{ $summary['total_orders'] }}</span></div>
            <div><span><b>Total Pendapatan</b></span><span>Rp {{ number_format($summary['total_amount'],0,',','.') }}</span></div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th style="width:40px;">No</th>
            <th style="width:90px;">Tanggal</th>
            <th style="width:140px;">Invoice</th>
            <th>Customer</th>
            <th>Produk</th>
            <th style="width:90px;">Payment</th>
            <th style="width:90px;">Order</th>
            <th style="width:130px;" class="right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $i => $o)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $o->created_at->format('d/m/Y') }}</td>
                <td><b>{{ $o->invoice_number }}</b></td>
                <td>{{ $o->customer_name }}</td>
                <td>{{ $o->product_name }}</td>
                <td>{{ $o->payment_status }}</td>
                <td>{{ $o->order_status }}</td>
                <td class="right">Rp {{ number_format($o->final_price,0,',','.') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th colspan="7" class="right">TOTAL</th>
            <th class="right">Rp {{ number_format($summary['total_amount'],0,',','.') }}</th>
        </tr>
        </tfoot>
    </table>

</div>

<script>
    window.print();
</script>
</body>
</html>
