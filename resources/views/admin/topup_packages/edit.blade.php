@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.topup-packages.index') }}" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Paket Top Up</h1>
            <p class="text-slate-500 text-sm">Ubah detail paket kuota iklan.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('admin.topup-packages.update', $topupPackage->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Listing (Kuota)</label>
                    <input type="number" name="amount" value="{{ old('amount', $topupPackage->amount) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required placeholder="Contoh: 10">
                    @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Paket (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $topupPackage->price) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required placeholder="Contoh: 50000">
                    @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Bonus Iklan (Opsional)</label>
                    <input type="number" name="bonus" value="{{ old('bonus', $topupPackage->bonus) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: 2">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Label Diskon (Opsional)</label>
                    <input type="text" name="discount_label" value="{{ old('discount_label', $topupPackage->discount_label) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: Hemat 20%">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Asli (Harga Coret)</label>
                    <input type="number" name="original_price" value="{{ old('original_price', (int)$topupPackage->original_price) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: 100000">
                    <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ada diskon.</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Teks Tombol Pembelian</label>
                    <input type="text" name="button_text" value="{{ old('button_text', $topupPackage->button_text ?? 'Beli Paket Ini') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: Beli Paket Ini">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Daftar Keuntungan / Benefit Paket</label>
                <textarea name="benefits" rows="4" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Bisa untuk semua kategori iklan.&#10;Masa aktif selamanya.">{{ old('benefits', implode("\n", is_array($topupPackage->benefits) ? $topupPackage->benefits : [])) }}</textarea>
                <p class="text-xs text-slate-500 mt-1">Pisahkan setiap benefit dengan baris baru (Enter).</p>
            </div>

            <div x-data="{ isVoucher: {{ $topupPackage->is_voucher ? 'true' : 'false' }} }" class="mb-8 space-y-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 rounded border-slate-300 text-[#0194F3] focus:ring-[#0194F3]" {{ $topupPackage->is_active ? 'checked' : '' }}>
                    <span class="text-sm font-semibold text-slate-700">Aktifkan Paket Ini</span>
                </label>

                <div class="pt-4 border-t border-slate-100">
                    <label class="flex items-center gap-3 cursor-pointer mb-4">
                        <input type="checkbox" name="is_voucher" value="1" x-model="isVoucher" class="w-5 h-5 rounded border-slate-300 text-[#0194F3] focus:ring-[#0194F3]">
                        <span class="text-sm font-semibold text-slate-700">Jadikan sebagai Voucher Promo Spesial</span>
                    </label>

                    <div x-show="isVoucher" x-collapse>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Batas Waktu Voucher</label>
                                <input type="datetime-local" name="valid_until" value="{{ old('valid_until', $topupPackage->valid_until ? \Carbon\Carbon::parse($topupPackage->valid_until)->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition">
                                <p class="text-xs text-slate-500 mt-1">Kosongkan jika berlaku selamanya.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.topup-packages.index') }}" class="px-6 py-2.5 rounded-lg font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg font-semibold text-white bg-[#0194F3] hover:bg-blue-600 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
