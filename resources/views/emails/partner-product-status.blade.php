<!doctype html>
<html><body style="font-family:Arial;line-height:1.5">
<p>Halo <b>{{ $partnerName }}</b>,</p>
<p>Produk ({{ strtoupper($productType) }}) <b>{{ $productTitle }}</b> statusnya: <b>{{ $status }}</b>.</p>
@if($note)
<p><b>Catatan Admin:</b><br>{!! nl2br(e($note)) !!}</p>
@endif
<p>Terima kasih.</p>
</body></html>
