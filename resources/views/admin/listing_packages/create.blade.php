@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.listing-packages.index') }}" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Paket Promosi</h1>
            <p class="text-slate-500 text-sm">Buat paket promosi iklan (sundulan/premium) baru.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('admin.listing-packages.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Paket</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required placeholder="Contoh: Paket Sundulan Basic">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tipe Paket</label>
                    <select name="type" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required>
                        <option value="sundul" {{ old('type') == 'sundul' ? 'selected' : '' }}>Sundulan</option>
                        <option value="premium" {{ old('type') == 'premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                    @error('type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Value / Jumlah (Kali/Hari)</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required placeholder="Contoh: 1">
                    <p class="text-xs text-slate-500 mt-1">Isi 1 jika Premium, isi kelipatan jika Sundulan (berkali-kali).</p>
                    @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Paket (Rp)</label>
                    <input type="number" name="price" value="{{ old('price') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required placeholder="Contoh: 50000">
                    @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Asli (Harga Coret)</label>
                    <input type="number" name="original_price" value="{{ old('original_price') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: 100000">
                    <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ada diskon.</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Label Diskon (Opsional)</label>
                    <input type="text" name="discount_label" value="{{ old('discount_label') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: Hemat 20%">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Teks Tombol Pembelian</label>
                    <input type="text" name="button_text" value="{{ old('button_text', 'Pilih Paket') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Contoh: Pilih Paket">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Daftar Keuntungan / Benefit Paket</label>
                <textarea name="benefits" rows="4" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" placeholder="Disundul sebanyak 3x&#10;Meningkatkan posisi iklan">{{ old('benefits') }}</textarea>
                <p class="text-xs text-slate-500 mt-1">Pisahkan setiap benefit dengan baris baru (Enter).</p>
            </div>

            <div class="mb-8 space-y-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 rounded border-slate-300 text-[#0194F3] focus:ring-[#0194F3]" checked>
                    <span class="text-sm font-semibold text-slate-700">Aktifkan Paket Ini</span>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.listing-packages.index') }}" class="px-6 py-2.5 rounded-lg font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg font-semibold text-white bg-[#0194F3] hover:bg-blue-600 transition">Simpan Paket</button>
            </div>
        </form>
    </div>
</div>
@endsection
