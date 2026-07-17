@extends('layouts.admin')
@section('title', 'Home Setting (Dinamis)')
@section('page-title', 'Home Setting (Dinamis)')
@section('content')

@if(session('success'))
<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 font-bold">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-900 font-bold">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div x-data="{ tab: 'hero' }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- Tabs Header -->
    <div class="flex flex-wrap border-b border-slate-200 bg-slate-50">
        <button @click="tab = 'hero'" :class="tab === 'hero' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Background Hero</button>
        <button @click="tab = 'texts'" :class="tab === 'texts' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Teks Section Beranda</button>
        <button @click="tab = 'location'" :class="tab === 'location' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Lokasi Unggulan</button>
        <button @click="tab = 'banner'" :class="tab === 'banner' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Banner Iklan</button>
        <button @click="tab = 'button'" :class="tab === 'button' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Tombol & Icon</button>
    </div>

    <!-- TAB 1: HERO -->
    <div x-show="tab === 'hero'" class="p-6">
        <form action="{{ route('admin.settings.home.hero') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl">
            @csrf
            <h3 class="text-lg font-extrabold text-slate-800 mb-4">Ubah Gambar & Teks Hero</h3>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 mb-1">Judul Hero</label>
                <input type="text" name="hero_title" value="{{ old('hero_title', $settings['hero_title'] ?? 'Temukan Properti Impian Anda Hari Ini.') }}" class="w-full rounded-xl border-slate-300">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 mb-1">Sub Judul Hero</label>
                <textarea name="hero_subtitle" rows="2" class="w-full rounded-xl border-slate-300">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? 'Pilihan terbaik untuk rumah, apartemen, ruko, dan barang & jasa pendukung properti.') }}</textarea>
            </div>
            

            <h4 class="text-md font-extrabold text-slate-800 mb-4">Gambar Background Hero</h4>
            @if(isset($settings['home_hero_bg_image']))
            <img src="{{ asset($settings['home_hero_bg_image']) }}" class="w-full h-48 object-cover rounded-xl border border-slate-200 mb-4">
            @endif

            <input type="file" name="home_hero_bg_image" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#0194F3] hover:file:bg-blue-100 mb-4">
            <button type="submit" class="bg-[#0194F3] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-600">Simpan Perubahan</button>
        </form>
    </div>

    <!-- TAB 2: TEXTS SECTION -->
    <div x-show="tab === 'texts'" x-cloak class="p-6">
        <h3 class="text-lg font-extrabold text-slate-800 mb-4">Kelola Teks Section Beranda</h3>
        <form action="{{ route('admin.settings.home.texts') }}" method="POST" class="space-y-6 max-w-4xl">
            @csrf
            
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <h4 class="font-bold text-[#0194F3] mb-3">1. Section Tipe Properti Terpopuler</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Judul</label>
                        <input type="text" name="home_tipe_title" value="{{ old('home_tipe_title', $settings['home_tipe_title'] ?? 'Tipe Properti Terpopuler') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi</label>
                        <textarea name="home_tipe_desc" rows="2" class="w-full rounded-xl border-slate-300">{{ old('home_tipe_desc', $settings['home_tipe_desc'] ?? 'Cari properti impian Anda mulai dari rumah minimalis, apartemen modern, ruko strategis, hingga tanah kavling siap bangun.') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <h4 class="font-bold text-orange-500 mb-3">2. Section Kategori Barang Terpopuler</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Judul</label>
                        <input type="text" name="home_kategori_barang_title" value="{{ old('home_kategori_barang_title', $settings['home_kategori_barang_title'] ?? 'Kategori Barang Terpopuler') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi</label>
                        <textarea name="home_kategori_barang_desc" rows="2" class="w-full rounded-xl border-slate-300">{{ old('home_kategori_barang_desc', $settings['home_kategori_barang_desc'] ?? 'Temukan berbagai macam barang keperluan rumah tangga dan kantor.') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <h4 class="font-bold text-emerald-500 mb-3">3. Section Kategori Jasa Terpopuler</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Judul</label>
                        <input type="text" name="home_kategori_jasa_title" value="{{ old('home_kategori_jasa_title', $settings['home_kategori_jasa_title'] ?? 'Kategori Jasa Terpopuler') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi</label>
                        <textarea name="home_kategori_jasa_desc" rows="2" class="w-full rounded-xl border-slate-300">{{ old('home_kategori_jasa_desc', $settings['home_kategori_jasa_desc'] ?? 'Penyedia layanan dan jasa terbaik untuk semua kebutuhan Anda.') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <h4 class="font-bold text-[#0194F3] mb-3">4. Section Lokasi Unggulan & Strategis</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Judul</label>
                        <input type="text" name="home_lokasi_title" value="{{ old('home_lokasi_title', $settings['home_lokasi_title'] ?? 'Lokasi Unggulan & Strategis') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi</label>
                        <textarea name="home_lokasi_desc" rows="2" class="w-full rounded-xl border-slate-300">{{ old('home_lokasi_desc', $settings['home_lokasi_desc'] ?? 'Daftar kawasan favorit dengan akses transportasi mudah, fasilitas publik lengkap, dan nilai investasi properti tinggi.') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <h4 class="font-bold text-[#0194F3] mb-3">5. Section Rekomendasi Jual Beli</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Judul</label>
                        <input type="text" name="home_rekomendasi_title" value="{{ old('home_rekomendasi_title', $settings['home_rekomendasi_title'] ?? 'Rekomendasi Jual Beli & Sewa Properti') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi</label>
                        <textarea name="home_rekomendasi_desc" rows="2" class="w-full rounded-xl border-slate-300">{{ old('home_rekomendasi_desc', $settings['home_rekomendasi_desc'] ?? 'Temukan iklan rumah dijual, sewa apartemen murah, dan ruko komersial dari agen terverifikasi.') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <h4 class="font-bold text-[#0194F3] mb-3">6. Section Kebutuhan Barang & Jasa</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Judul</label>
                        <input type="text" name="home_kebutuhan_title" value="{{ old('home_kebutuhan_title', $settings['home_kebutuhan_title'] ?? 'Kebutuhan Barang & Jasa') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi</label>
                        <textarea name="home_kebutuhan_desc" rows="2" class="w-full rounded-xl border-slate-300">{{ old('home_kebutuhan_desc', $settings['home_kebutuhan_desc'] ?? 'Pusat penyedia jasa renovasi rumah, perawatan properti, dan perlengkapan rumah tangga terpercaya terlengkap di sekitar Anda.') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-[#0194F3] text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-600">Simpan Teks</button>
            </div>
        </form>
    </div>

    <!-- TAB 3: LOKASI UNGGULAN -->
    <div x-show="tab === 'location'" x-cloak class="p-6">
        <h3 class="text-lg font-extrabold text-slate-800 mb-4">Kelola Lokasi Unggulan</h3>
        
        <!-- Form Tambah -->
        <form action="{{ route('admin.settings.home.location.store') }}" method="POST" enctype="multipart/form-data" class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6 flex flex-col gap-3">
            @csrf
            <div class="flex flex-col md:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Gambar Banner Kota</label>
                    <input type="file" name="image" required class="w-full text-sm rounded-xl border-slate-300 bg-white">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Headline (Contoh: Jakarta)</label>
                    <input type="text" name="title" required class="w-full rounded-xl border-slate-300">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Subteks (Contoh: Pusat Bisnis...)</label>
                    <input type="text" name="subtitle" class="w-full rounded-xl border-slate-300">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 mb-1">URL (Opsional)</label>
                    <input type="text" name="url" placeholder="https://" class="w-full rounded-xl border-slate-300">
                </div>
            </div>
            <div class="flex gap-4 items-center mt-2">
                <div class="w-24">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Order</label>
                    <input type="number" name="order" value="0" class="w-full rounded-xl border-slate-300 text-sm py-1">
                </div>
                <div class="pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-[#0194F3]">
                        <span class="text-sm font-semibold text-slate-700">Aktif</span>
                    </label>
                </div>
                <button type="submit" class="bg-[#0194F3] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-600 h-10 ml-auto">Tambah Lokasi</button>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($locations as $loc)
            <div x-data="{ showEdit: false, showDelete: false }" class="border border-slate-200 rounded-xl overflow-hidden relative group">
                <img src="{{ asset($loc->image) }}" class="w-full h-48 object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex flex-col justify-end p-4">
                    <h4 class="text-white font-bold text-lg leading-tight">{{ $loc->title }}</h4>
                    <p class="text-white/80 text-sm truncate">{{ $loc->subtitle }}</p>
                </div>
                <div class="absolute flex gap-2" style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                    <button @click="showEdit = true" type="button" class="text-white px-3 py-1.5 rounded-lg shadow-md transition text-xs font-bold" style="background-color: #f59e0b;">Edit</button>
                    <button @click="showDelete = true" type="button" class="text-white px-3 py-1.5 rounded-lg shadow-md transition text-xs font-bold" style="background-color: #ef4444;">Hapus</button>
                </div>

                <!-- Modal Delete -->
                <div x-show="showDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div @click.outside="showDelete = false" class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl">
                        <h3 class="text-lg font-bold mb-2">Konfirmasi Hapus</h3>
                        <p class="text-sm text-slate-600 mb-6">Yakin ingin menghapus lokasi <strong>{{ $loc->title }}</strong>?</p>
                        <div class="flex justify-end gap-3">
                            <button @click="showDelete = false" type="button" class="px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                            <form action="{{ route('admin.settings.home.location.destroy', $loc->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-500 hover:bg-red-600 rounded-xl">Ya, Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div x-show="showEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
                    <div @click.outside="showEdit = false" class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-xl">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Edit Lokasi Unggulan</h3>
                            <button @click="showEdit = false" type="button" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                        </div>
                        <form action="{{ route('admin.settings.home.location.update', $loc->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Gambar Banner Kota (Biarkan kosong jika tidak diubah)</label>
                                <input type="file" name="image" class="w-full text-sm rounded-xl border-slate-300 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Headline (Contoh: Jakarta)</label>
                                <input type="text" name="title" value="{{ $loc->title }}" required class="w-full rounded-xl border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Subteks</label>
                                <input type="text" name="subtitle" value="{{ $loc->subtitle }}" class="w-full rounded-xl border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">URL (Opsional)</label>
                                <input type="text" name="url" value="{{ $loc->url }}" class="w-full rounded-xl border-slate-300">
                            </div>
                            <div class="flex gap-4 items-center mt-2">
                                <div class="w-24">
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Order</label>
                                    <input type="number" name="order" value="{{ $loc->order }}" class="w-full rounded-xl border-slate-300 text-sm py-1">
                                </div>
                                <div class="pt-5">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" {{ $loc->is_active ? 'checked' : '' }} class="rounded text-[#0194F3]">
                                        <span class="text-sm font-semibold text-slate-700">Aktif</span>
                                    </label>
                                </div>
                                <button type="submit" class="bg-[#0194F3] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-600 h-10 ml-auto">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 4: BANNERS -->
    <div x-show="tab === 'banner'" x-cloak class="p-6">
        <h3 class="text-lg font-extrabold text-slate-800 mb-4">Kelola Banner Promosi</h3>
        
        <!-- Form Tambah -->
        <form action="{{ route('admin.settings.home.banner.store') }}" method="POST" enctype="multipart/form-data" class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6">
            @csrf
            <div class="flex flex-col md:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Gambar Banner</label>
                    <input type="file" name="image" required class="w-full text-sm rounded-xl border-slate-300 bg-white">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 mb-1">URL / Link (Opsional)</label>
                    <input type="text" name="url" placeholder="https://" class="w-full rounded-xl border-slate-300">
                </div>
            </div>
            <div class="flex gap-4 items-center mt-4">
                <div class="w-24">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Order</label>
                    <input type="number" name="order" value="0" class="w-full rounded-xl border-slate-300 text-sm py-1">
                </div>
                <div class="pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-[#0194F3]">
                        <span class="text-sm font-semibold text-slate-700">Aktif</span>
                    </label>
                </div>
                <button type="submit" class="bg-[#0194F3] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-600 h-10 ml-auto">Tambah Banner</button>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($banners as $b)
            <div class="border border-slate-200 rounded-xl overflow-hidden relative">
                <img src="{{ asset($b->image) }}" class="w-full h-32 object-cover">
                <div class="p-3 bg-white flex justify-between items-center">
                    <span class="text-xs text-slate-500 truncate max-w-[150px]">{{ $b->url ?? 'Tidak ada link' }}</span>
                    <form action="{{ route('admin.settings.home.banner.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus banner?');">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded-lg"><i data-lucide="trash" class="w-4 h-4"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 5: BUTTONS -->
    <div x-show="tab === 'button'" x-cloak class="p-6">
        <h3 class="text-lg font-extrabold text-slate-800 mb-4">Pengaturan CTA Banner (Samping Tombol Layanan)</h3>
        <form action="{{ route('admin.settings.home.hero') }}" method="POST" class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-8">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 mb-1">Judul CTA</label>
                <input type="text" name="cta_title" value="{{ old('cta_title', $settings['cta_title'] ?? 'Pasang Iklan Sekarang!') }}" class="w-full rounded-xl border-slate-300">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 mb-1">Sub Judul CTA</label>
                <textarea name="cta_subtitle" rows="2" class="w-full rounded-xl border-slate-300">{{ old('cta_subtitle', $settings['cta_subtitle'] ?? 'Jangkau jutaan pencari properti dengan mudah.') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Teks Tombol CTA</label>
                    <input type="text" name="cta_button_text" value="{{ old('cta_button_text', $settings['cta_button_text'] ?? 'Pasang Iklan') }}" class="w-full rounded-xl border-slate-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Link Tombol CTA</label>
                    <input type="text" name="cta_button_link" value="{{ old('cta_button_link', $settings['cta_button_link'] ?? '/pasang-iklan') }}" class="w-full rounded-xl border-slate-300">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-[#0194F3] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-600">Simpan Pengaturan CTA</button>
            </div>
        </form>

        <h3 class="text-lg font-extrabold text-slate-800 mb-4">Kelola Tombol Layanan (Icon Bawah Hero)</h3>

        <!-- Form Tambah Tombol -->
        <form action="{{ route('admin.settings.home.button.store') }}" method="POST" enctype="multipart/form-data" class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Label Tombol</label>
                    <input type="text" name="label" placeholder="Cari Properti" required class="w-full rounded-xl border-slate-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Subteks (Opsional)</label>
                    <input type="text" name="subtitle" placeholder="Beli & Sewa" class="w-full rounded-xl border-slate-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">URL / Link</label>
                    <input type="text" name="url" placeholder="/dijual" class="w-full rounded-xl border-slate-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Upload Icon Gambar/SVG</label>
                    <input type="file" name="icon_image" required class="w-full text-sm rounded-xl border-slate-300 bg-white py-2 px-2">
                </div>
            </div>
            <div class="flex gap-4 items-center">
                <div class="w-24">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Order</label>
                    <input type="number" name="order" value="0" class="w-full rounded-xl border-slate-300 text-sm py-1">
                </div>
                <div class="pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-[#0194F3]">
                        <span class="text-sm font-semibold text-slate-700">Aktif</span>
                    </label>
                </div>
                <button type="submit" class="bg-[#0194F3] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-600 h-10 ml-auto">Tambah Tombol</button>
            </div>
        </form>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="p-3 text-sm font-bold text-slate-700">Icon</th>
                    <th class="p-3 text-sm font-bold text-slate-700">Label</th>
                    <th class="p-3 text-sm font-bold text-slate-700">URL Target</th>
                    <th class="p-3 text-sm font-bold text-slate-700 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($buttons as $btn)
                <tr class="border-b border-slate-100">
                    <td class="p-3">
                        <img src="{{ asset($btn->icon_image) }}" class="w-10 h-10 object-contain p-1 border rounded-lg bg-slate-50">
                    </td>
                    <td class="p-3 font-bold">{{ $btn->label }}</td>
                    <td class="p-3 text-slate-500 text-sm">{{ $btn->url }}</td>
                    <td class="p-3 flex justify-end gap-2">
                        <form action="{{ route('admin.settings.home.button.destroy', $btn->id) }}" method="POST" onsubmit="return confirm('Hapus tombol ini?');">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded-lg"><i data-lucide="trash" class="w-4 h-4"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<!-- Modal Peringatan Ukuran File -->
<div id="file-size-modal" class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl p-6 w-[90%] max-w-md shadow-xl transform scale-95 transition-transform duration-300">
        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4 mx-auto">
            <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600"></i>
        </div>
        <h3 class="text-2xl font-extrabold text-center text-slate-800 mb-2">File Terlalu Besar!</h3>
        <p class="text-slate-600 text-center mb-6 font-medium">Ukuran gambar yang Abang upload melebih batas maksimal <b class="text-red-500">2MB</b>. Silakan kompres gambar tersebut terlebih dahulu sebelum di-upload kembali.</p>
        <button onclick="closeFileModal()" class="w-full text-white font-bold py-3 px-4 rounded-xl transition shadow-lg flex justify-center items-center gap-2" style="background-color: #0194F3;">
            Mengerti & Tutup
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                let hasError = false;
                const fileInputs = form.querySelectorAll('input[type="file"]');
                
                fileInputs.forEach(input => {
                    if (input.files.length > 0) {
                        const fileSize = input.files[0].size;
                        if (fileSize > maxFileSize) {
                            hasError = true;
                        }
                    }
                });
                
                if (hasError) {
                    e.preventDefault();
                    const modal = document.getElementById('file-size-modal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    setTimeout(() => {
                        modal.children[0].classList.remove('scale-95');
                        modal.children[0].classList.add('scale-100');
                    }, 50);
                }
            });
        });
    });

    function closeFileModal() {
        const modal = document.getElementById('file-size-modal');
        modal.children[0].classList.remove('scale-100');
        modal.children[0].classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 150);
    }
</script>
@endpush
@endsection
