@extends('layouts.front')

@section('content')

<div class="bg-slate-50 min-h-screen py-10 pt-24">
    <div class="max-w-7xl mx-auto px-4">
        
        {{-- Breadcrumb & Title --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-[#0194F3]">Rumaindo</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-slate-800 font-semibold capitalize">{{ str_replace('-', ' ', $type) }}</span>
            </div>
            <h1 class="text-3xl font-bold text-slate-800 capitalize">
                {{ str_replace('-', ' ', $type) }} Terkini
            </h1>
        </div>

        {{-- Tab Kategori --}}
        <div class="flex items-center gap-2 mb-8 border-b border-slate-200">
            <a href="?kategori=properti" class="px-6 py-3 font-semibold text-sm border-b-2 {{ request('kategori', 'properti') == 'properti' ? 'border-[#0194F3] text-[#0194F3]' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Properti
            </a>
            <a href="?kategori=barang" class="px-6 py-3 font-semibold text-sm border-b-2 {{ request('kategori') == 'barang' ? 'border-[#0194F3] text-[#0194F3]' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Barang
            </a>
            <a href="?kategori=jasa" class="px-6 py-3 font-semibold text-sm border-b-2 {{ request('kategori') == 'jasa' ? 'border-[#0194F3] text-[#0194F3]' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Jasa
            </a>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- SIDEBAR FILTER --}}
            <div class="w-full lg:w-1/4 shrink-0">
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm sticky top-24">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <i data-lucide="filter" class="w-5 h-5 text-[#0194F3]"></i> Filter
                        </h3>
                        <a href="{{ url()->current() }}" class="text-sm font-semibold text-rose-500 hover:text-rose-600">Reset</a>
                    </div>

                    <form action="{{ url()->current() }}" method="GET" class="space-y-5">
                        <input type="hidden" name="kategori" value="{{ request('kategori', 'properti') }}">

                        {{-- Kata Kunci --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kata Kunci</label>
                            <input type="text" name="q" placeholder="Cari judul iklan..." value="{{ request('q') }}" class="w-full border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-slate-50 text-sm">
                        </div>

                        {{-- Jenis Transaksi (Hanya jika bukan jasa/barang murni) --}}
                        @if(request('kategori', 'properti') == 'properti')
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Transaksi</label>
                            <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1 rounded-xl">
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="transaksi" value="jual" class="peer sr-only" {{ request('transaksi') == 'jual' ? 'checked' : '' }}>
                                    <div class="py-2 text-sm font-semibold rounded-lg text-slate-500 peer-checked:bg-white peer-checked:text-[#0194F3] peer-checked:shadow-sm transition">Jual</div>
                                </label>
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="transaksi" value="sewa" class="peer sr-only" {{ request('transaksi') == 'sewa' ? 'checked' : '' }}>
                                    <div class="py-2 text-sm font-semibold rounded-lg text-slate-500 peer-checked:bg-white peer-checked:text-[#0194F3] peer-checked:shadow-sm transition">Sewa</div>
                                </label>
                            </div>
                        </div>

                        {{-- Tipe Properti --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tipe Properti</label>
                            <select name="tipe" class="w-full border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-slate-50 text-sm">
                                <option value="">Semua Tipe</option>
                                <option value="rumah" {{ request('tipe') == 'rumah' ? 'selected' : '' }}>Rumah</option>
                                <option value="apartemen" {{ request('tipe') == 'apartemen' ? 'selected' : '' }}>Apartemen</option>
                                <option value="ruko" {{ request('tipe') == 'ruko' ? 'selected' : '' }}>Ruko</option>
                                <option value="tanah" {{ request('tipe') == 'tanah' ? 'selected' : '' }}>Tanah</option>
                            </select>
                        </div>
                        @endif

                        {{-- Harga --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Rentang Harga (Rp)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" class="w-full border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-slate-50 text-sm">
                                <span class="text-slate-400">-</span>
                                <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" class="w-full border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-slate-50 text-sm">
                            </div>
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Lokasi / Daerah</label>
                            <input type="text" name="lokasi" placeholder="Contoh: Jakarta Selatan" value="{{ request('lokasi') }}" class="w-full border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-slate-50 text-sm">
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="w-full py-3 bg-[#0194F3] hover:bg-blue-600 text-white font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2">
                            Terapkan Filter
                        </button>
                    </form>
                </div>
            </div>

            {{-- GRID HASIL LISTING --}}
            <div class="w-full lg:w-3/4">
                
                {{-- Toolbar --}}
                <div class="flex items-center justify-between mb-6">
                    <p class="text-slate-600 font-medium">Menampilkan <span class="font-bold text-slate-800">0</span> properti</p>
                    
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-slate-500 font-medium hidden sm:block">Urutkan:</span>
                        <select class="border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-white text-sm font-semibold">
                            <option value="terbaru">Terbaru</option>
                            <option value="harga_murah">Harga Terendah</option>
                            <option value="harga_mahal">Harga Tertinggi</option>
                        </select>
                    </div>
                </div>

                {{-- Empty State (Karena belum ada data riil) --}}
                <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center flex flex-col items-center justify-center min-h-[400px]">
                    <div class="w-24 h-24 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="folder-x" class="w-12 h-12"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">Data tidak ditemukan</h3>
                    <p class="text-slate-500 mb-6 max-w-md">Coba ubah atau hapus beberapa filter pencarian Anda untuk melihat lebih banyak hasil.</p>
                    <a href="{{ url()->current() }}" class="px-6 py-2 border-2 border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition">
                        Reset Filter
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection
