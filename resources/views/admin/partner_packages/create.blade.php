@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.partner_packages.index') }}" class="p-2 bg-white text-slate-500 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Paket Partner</h1>
            <p class="text-slate-500 text-sm mt-1">Buat paket langganan berbayar baru untuk partner.</p>
        </div>
    </div>

    <form action="{{ route('admin.partner_packages.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-3xl">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Paket</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required placeholder="Contoh: Paket Premium Plus">
                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi Singkat</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: Paket terbaik untuk agen properti.">{{ old('description') }}</textarea>
                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Listing (Kuota)</label>
                <input type="number" name="listing_quota" value="{{ old('listing_quota') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required placeholder="Contoh: 10 (Isi -1 untuk Unlimited)">
                @error('listing_quota') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Masa Aktif (Hari)</label>
                <input type="number" name="duration_days" value="{{ old('duration_days', 30) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required placeholder="Contoh: 30">
                @error('duration_days') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Paket (Rp)</label>
                <input type="number" name="price" value="{{ old('price') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required placeholder="Contoh: 50000">
                @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Bonus Iklan (Opsional)</label>
                <input type="number" name="bonus" value="{{ old('bonus') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: 2">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Label Diskon (Opsional)</label>
                <input type="text" name="discount_label" value="{{ old('discount_label') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: Hemat 20%">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Asli (Harga Coret)</label>
                <input type="number" name="original_price" value="{{ old('original_price') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: 100000">
                <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ada diskon.</p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Teks Tombol Pembelian</label>
                <input type="text" name="button_text" value="{{ old('button_text', 'Beli Paket Ini') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: Beli Paket Ini">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Daftar Keuntungan / Benefit Paket</label>
            <textarea name="benefits" rows="4" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Bisa untuk semua kategori iklan.&#10;Masa aktif 30 hari.">{{ old('benefits') }}</textarea>
            <p class="text-xs text-slate-500 mt-1">Pisahkan setiap benefit dengan baris baru (Enter).</p>
        </div>

        <div x-data="{ isVoucher: false }" class="mb-8 space-y-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" class="w-5 h-5 rounded border-slate-300 text-[#0194F3] focus:ring-[#0194F3]" checked>
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
                            <input type="datetime-local" name="valid_until" value="{{ old('valid_until') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition">
                            <p class="text-xs text-slate-500 mt-1">Kosongkan jika berlaku selamanya.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="{{ route('admin.partner_packages.index') }}" class="px-6 py-2.5 rounded-lg font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-lg font-semibold text-white bg-[#0194F3] hover:bg-blue-600 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Paket
            </button>
        </div>
    </form>
</div>
@endsection
