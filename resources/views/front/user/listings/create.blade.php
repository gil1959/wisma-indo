@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50" x-data="listingForm()">
    <div class="max-w-4xl mx-auto px-4">
        
        <div class="mb-8">
            <a href="{{ route('iklan.saya') }}" class="text-[#0194F3] font-semibold flex items-center gap-1 hover:underline mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
            <h1 class="text-3xl font-bold text-slate-800">Pasang Iklan Baru</h1>
            <p class="text-slate-600">Kategori Terpilih: <span class="font-bold text-[#0194F3] capitalize">{{ $kategori }}</span></p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-2xl border border-red-200">
                <div class="font-bold mb-2">Terjadi kesalahan:</div>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pasang.iklan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            {{-- INFORMASI DASAR --}}
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-lucide="info" class="w-5 h-5 text-[#0194F3]"></i> Informasi Dasar
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kategori Iklan *</label>
                        <select name="listing_category_id" required class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                            <option value="">Pilih Kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('listing_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($kategori == 'properti')
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Transaksi *</label>
                        <select name="transaction_type" x-model="transactionType" required class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                            <option value="dijual">Dijual</option>
                            <option value="disewa">Disewakan</option>
                        </select>
                    </div>
                    @endif
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Judul Iklan *</label>
                    <input type="text" name="title" required value="{{ old('title') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="Contoh: Rumah Minimalis Siap Huni di Pusat Kota">
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-bold text-slate-700">Deskripsi Lengkap *</label>
                        <button type="button" onclick="generateAiDescription()" id="btnAiDesc" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg shadow hover:opacity-90 transition">
                            <i data-lucide="bot" class="w-3.5 h-3.5"></i> Generate AI
                        </button>
                    </div>
                    <textarea name="description" id="listingDesc" class="w-full rounded-xl border-slate-200 hidden">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- HARGA & KEUANGAN --}}
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-lucide="tag" class="w-5 h-5 text-[#0194F3]"></i> Harga & Ketentuan
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Harga (Rp) *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-slate-500 font-bold">Rp</span>
                            </div>
                            <input type="number" name="price" required value="{{ old('price') }}" class="w-full pl-12 rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="0">
                        </div>
                    </div>
                    <div class="flex items-center gap-6 mt-6 md:mt-0">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="negotiable" value="1" {{ old('negotiable') ? 'checked' : '' }} class="rounded border-slate-300 text-[#0194F3] focus:ring-[#0194F3]">
                            <span class="text-sm font-bold text-slate-700">Bisa Nego</span>
                        </label>

                    </div>
                </div>

                @if($kategori == 'properti')
                <div x-show="transactionType == 'disewa'" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" style="display: none;">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Periode Sewa</label>
                        <select name="rental_period" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                            <option value="">Pilih...</option>
                            <option value="Harian" {{ old('rental_period') == 'Harian' ? 'selected' : '' }}>Harian</option>
                            <option value="Bulanan" {{ old('rental_period') == 'Bulanan' ? 'selected' : '' }}>Bulanan</option>
                            <option value="Tahunan" {{ old('rental_period') == 'Tahunan' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Minimal Sewa</label>
                        <input type="text" name="min_rental" value="{{ old('min_rental') }}" placeholder="Cth: 6 Bulan" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>
                </div>
                @endif
            </div>

            {{-- SPESIFIKASI PROPERTI --}}
            @if($kategori == 'properti')
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-lucide="home" class="w-5 h-5 text-[#0194F3]"></i> Spesifikasi Properti
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Luas Tanah (m²)</label>
                        <input type="number" name="land_area" value="{{ old('land_area') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Luas Bngn (m²)</label>
                        <input type="number" name="building_area" value="{{ old('building_area') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">K. Tidur</label>
                        <input type="number" name="bedrooms" value="{{ old('bedrooms') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">K. Mandi</label>
                        <input type="number" name="bathrooms" value="{{ old('bathrooms') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Jml Lantai</label>
                        <input type="number" name="floors" value="{{ old('floors') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Carport</label>
                        <input type="number" name="carport" value="{{ old('carport') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Garasi</label>
                        <input type="number" name="garage" value="{{ old('garage') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Thn Dibangun</label>
                        <input type="number" name="build_year" value="{{ old('build_year') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Sertifikat</label>
                        <select name="certificate" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                            <option value="">Pilih...</option>
                            <option value="SHM - Sertifikat Hak Milik" {{ old('certificate') == 'SHM - Sertifikat Hak Milik' ? 'selected' : '' }}>SHM - Sertifikat Hak Milik</option>
                            <option value="HGB - Hak Guna Bangunan" {{ old('certificate') == 'HGB - Hak Guna Bangunan' ? 'selected' : '' }}>HGB - Hak Guna Bangunan</option>
                            <option value="AJB - Akta Jual Beli" {{ old('certificate') == 'AJB - Akta Jual Beli' ? 'selected' : '' }}>AJB - Akta Jual Beli</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Perabotan</label>
                        <select name="furnished_status" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                            <option value="">Pilih...</option>
                            <option value="Unfurnished" {{ old('furnished_status') == 'Unfurnished' ? 'selected' : '' }}>Unfurnished (Kosong)</option>
                            <option value="Semi Furnished" {{ old('furnished_status') == 'Semi Furnished' ? 'selected' : '' }}>Semi Furnished</option>
                            <option value="Fully Furnished" {{ old('furnished_status') == 'Fully Furnished' ? 'selected' : '' }}>Fully Furnished</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap gap-6 mb-8">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="imb" value="1" {{ old('imb') ? 'checked' : '' }} class="rounded border-slate-300 text-[#0194F3] focus:ring-[#0194F3]">
                        <span class="text-sm font-bold text-slate-700">Ada IMB</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="pbb" value="1" {{ old('pbb') ? 'checked' : '' }} class="rounded border-slate-300 text-[#0194F3] focus:ring-[#0194F3]">
                        <span class="text-sm font-bold text-slate-700">Ada PBB</span>
                    </label>
                </div>

                {{-- FASILITAS --}}
                <div class="mb-8">
                    <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Fasilitas Properti</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @php
                            $fasilitas_list = ['Area Hiburan', 'Balkon', 'Gym', 'Halaman Terbuka', 'Jalan Raya', 'Karaoke', 'Keamanan 24 Jam', 'Kitchen Set', 'Kolam Renang', 'Lapangan Basket', 'Lapangan Tenis', 'One Gate System', 'Parkir', 'Pemanas Air', 'Pendingin Ruangan (AC)', 'Pos Security', 'Rooftop', 'Ruang Rapat', 'Ruang Serbaguna', 'Spa dan Sauna', 'Taman', 'Taman Bermain Anak', 'Telepon', 'Televisi', 'Tempat BBQ', 'Teras', 'Transportasi Umum', 'Trek Lari', 'WiFi'];
                        @endphp
                        @foreach($fasilitas_list as $f)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="{{ $f }}" {{ (is_array(old('facilities')) && in_array($f, old('facilities'))) ? 'checked' : '' }} class="rounded border-slate-300 text-[#0194F3] focus:ring-[#0194F3]">
                                <span class="text-sm text-slate-600">{{ $f }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- AREA SEKITAR --}}
                <div>
                    <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Area Sekitar</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @php
                            $area_list = ['Apotek', 'Kolam Renang', 'Mall', 'Masjid', 'Pasar', 'Rumah Sakit', 'Sarana Pendidikan', 'Sarana Perbelanjaan', 'Tempat Olahraga'];
                        @endphp
                        @foreach($area_list as $a)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="surroundings[]" value="{{ $a }}" {{ (is_array(old('surroundings')) && in_array($a, old('surroundings'))) ? 'checked' : '' }} class="rounded border-slate-300 text-[#0194F3] focus:ring-[#0194F3]">
                                <span class="text-sm text-slate-600">{{ $a }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- SPESIFIKASI BARANG --}}
            @if($kategori == 'barang')
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-lucide="package" class="w-5 h-5 text-orange-500"></i> Spesifikasi Barang
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kondisi Barang *</label>
                        <select name="condition" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring focus:ring-orange-500/20">
                            <option value="Baru" {{ old('condition') == 'Baru' ? 'selected' : '' }}>Baru</option>
                            <option value="Bekas" {{ old('condition') == 'Bekas' ? 'selected' : '' }}>Bekas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Merek / Brand (Opsional)</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring focus:ring-orange-500/20" placeholder="Merek barang">
                    </div>
                </div>
            </div>
            @endif

            {{-- SPESIFIKASI JASA --}}
            @if($kategori == 'jasa')
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-lucide="briefcase" class="w-5 h-5 text-emerald-500"></i> Spesifikasi Jasa
                </h3>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Area Layanan (Cakupan)</label>
                    <input type="text" name="service_area" value="{{ old('service_area') }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring focus:ring-emerald-500/20" placeholder="Contoh: Jakarta dan sekitarnya, Seluruh Indonesia">
                </div>
            </div>
            @endif

            {{-- LOKASI & KONTAK --}}
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-5 h-5 text-[#0194F3]"></i> Lokasi & Kontak
                </h3>
                
                <div class="mb-6 relative">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi Singkat *</label>
                    <input type="text" id="locationSearch" name="location" required value="{{ old('location') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="Contoh: Jakarta Selatan">
                    <p class="text-xs text-slate-500 mt-1">Gunakan kotak pencarian ini untuk mencari lokasi di peta secara otomatis.</p>
                </div>
                
                @if(isset($siteSettings['google_maps_api_key']) && $siteSettings['google_maps_api_key'] != '')
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Peta Lokasi (Otomatis)</label>
                    <div id="mapPicker" class="w-full h-[300px] rounded-xl border border-slate-200 shadow-inner overflow-hidden mb-2 relative z-0 bg-slate-100 flex items-center justify-center">
                        <span class="text-slate-400 font-medium">Memuat Peta...</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Geser pin merah muda pada peta untuk menyesuaikan titik lokasi dengan presisi.</p>
                </div>
                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                @endif
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Lengkap</label>
                    <textarea id="addressField" name="address" rows="3" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="Jalan, RT/RW, Kecamatan...">{{ old('address') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">No. WhatsApp *</label>
                        <input type="text" name="whatsapp" required value="{{ old('whatsapp') ?? auth()->user()->phone }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="08123456789">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">No. Telepon (Opsional)</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="08123456789">
                    </div>
                </div>
            </div>

            {{-- MEDIA (FOTO & VIDEO) --}}
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm" x-data="imageUploader()">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-lucide="image" class="w-5 h-5 text-[#0194F3]"></i> Foto & Media
                </h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Foto Cover (Utama) *</label>
                    <input type="file" name="cover_image" @change="handleCoverChange" required accept="image/*" class="w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#0194F3] hover:file:bg-blue-100">
                    <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG. Maks 2MB. Tampil di halaman depan.</p>
                    
                    <template x-if="coverPreview">
                        <div class="mt-4 relative inline-block group">
                            <img :src="coverPreview" class="h-32 w-auto rounded-xl object-cover border border-slate-200 shadow-sm">
                            <button type="button" @click="removeCoverImage" class="absolute -top-2 -right-2 bg-rose-500 text-white w-6 h-6 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-md" title="Hapus foto cover">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Galeri Foto (Opsional, Maks 12)</label>
                    <input type="file" name="images[]" x-ref="galleryInput" @change="handleGalleryChange" multiple accept="image/*" class="w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#0194F3] hover:file:bg-blue-100">
                    <p class="text-xs text-slate-500 mt-2">Bisa pilih beberapa foto sekaligus. Foto baru yang dipilih akan ditambahkan ke daftar preview di bawah.</p>
                    
                    <!-- Previews -->
                    <div class="mt-4 flex flex-wrap gap-4" x-show="galleryImages.length > 0" x-cloak>
                        <template x-for="img in galleryImages" :key="img.id">
                            <div class="relative group">
                                <img :src="img.previewUrl" class="h-24 w-24 rounded-xl object-cover border border-slate-200 shadow-sm">
                                <button type="button" @click="removeGalleryImage(img.id)" class="absolute -top-2 -right-2 bg-rose-500 text-white w-6 h-6 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-md" title="Hapus foto ini">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">URL YouTube (Opsional)</label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="https://youtube.com/watch?v=...">
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-6">
                <a href="{{ route('iklan.saya') }}" class="px-6 py-3 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-8 py-3 rounded-xl font-bold text-white bg-[#0194F3] hover:bg-blue-600 transition shadow-lg shadow-blue-500/30">
                    Terbitkan Iklan
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
@include('components.image-uploader-script')
<script>
    function listingForm() {
        return {
            transactionType: '{{ old('transaction_type', 'dijual') }}',
        }
    }
</script>
@if(isset($siteSettings['google_maps_api_key']) && $siteSettings['google_maps_api_key'] != '')
<script src="https://maps.googleapis.com/maps/api/js?key={{ $siteSettings['google_maps_api_key'] }}&libraries=places"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var defaultLat = -6.2088;
        var defaultLng = 106.8456;
        var oldLat = document.getElementById('latitude').value;
        var oldLng = document.getElementById('longitude').value;
        var startLat = oldLat ? parseFloat(oldLat) : defaultLat;
        var startLng = oldLng ? parseFloat(oldLng) : defaultLng;

        var map = new google.maps.Map(document.getElementById('mapPicker'), {
            center: {lat: startLat, lng: startLng},
            zoom: oldLat ? 16 : 12,
            mapTypeControl: false,
            streetViewControl: false,
        });

        var marker = new google.maps.Marker({
            position: {lat: startLat, lng: startLng},
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
        });

        var input = document.getElementById('locationSearch');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) return;

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setPosition(place.geometry.location);
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
        });

        marker.addListener('dragend', function() {
            var pos = marker.getPosition();
            document.getElementById('latitude').value = pos.lat();
            document.getElementById('longitude').value = pos.lng();
            
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: pos }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    var city = '';
                    var subloc = '';
                    results[0].address_components.forEach(function(c) {
                        if (c.types.includes('administrative_area_level_2') || c.types.includes('locality')) city = c.long_name;
                        if (c.types.includes('sublocality') || c.types.includes('neighborhood')) subloc = c.long_name;
                    });
                    var shortLoc = [];
                    if (subloc) shortLoc.push(subloc);
                    if (city) shortLoc.push(city);
                    
                    document.getElementById('locationSearch').value = shortLoc.length > 0 ? shortLoc.join(', ') : results[0].formatted_address;
                    document.getElementById('addressField').value = results[0].formatted_address;
                }
            });
        });

        if (!oldLat && !oldLng) {
            const fallbackToIP = function() {
                fetch('https://get.geojs.io/v1/ip/geo.json')
                    .then(response => response.json())
                    .then(data => {
                        if(data.latitude && data.longitude) {
                            var pos = { lat: parseFloat(data.latitude), lng: parseFloat(data.longitude) };
                            map.setCenter(pos);
                            map.setZoom(15);
                            marker.setPosition(pos);
                            document.getElementById('latitude').value = pos.lat;
                            document.getElementById('longitude').value = pos.lng;
                            
                            var geocoder = new google.maps.Geocoder();
                            geocoder.geocode({ location: pos }, function(results, status) {
                                if (status === 'OK' && results[0]) {
                                    var city = '';
                                    var subloc = '';
                                    results[0].address_components.forEach(function(c) {
                                        if (c.types.includes('administrative_area_level_2') || c.types.includes('locality')) city = c.long_name;
                                        if (c.types.includes('sublocality') || c.types.includes('neighborhood')) subloc = c.long_name;
                                    });
                                    var shortLoc = [];
                                    if (subloc) shortLoc.push(subloc);
                                    if (city) shortLoc.push(city);
                                    
                                    document.getElementById('locationSearch').value = shortLoc.length > 0 ? shortLoc.join(', ') : results[0].formatted_address;
                                    document.getElementById('addressField').value = results[0].formatted_address;
                                } else if (data.city) {
                                    document.getElementById('locationSearch').value = data.city + ', ' + data.region;
                                }
                            });
                        }
                    }).catch(err => console.log('IP Geo fallback failed:', err));
            };

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    map.setCenter(pos);
                    map.setZoom(16);
                    marker.setPosition(pos);
                    document.getElementById('latitude').value = pos.lat;
                    document.getElementById('longitude').value = pos.lng;
                    
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ location: pos }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            var city = '';
                            var subloc = '';
                            results[0].address_components.forEach(function(c) {
                                if (c.types.includes('administrative_area_level_2') || c.types.includes('locality')) city = c.long_name;
                                if (c.types.includes('sublocality') || c.types.includes('neighborhood')) subloc = c.long_name;
                            });
                            var shortLoc = [];
                            if (subloc) shortLoc.push(subloc);
                            if (city) shortLoc.push(city);
                            
                            document.getElementById('locationSearch').value = shortLoc.length > 0 ? shortLoc.join(', ') : results[0].formatted_address;
                            document.getElementById('addressField').value = results[0].formatted_address;
                        }
                    });
                }, function(error) {
                    console.warn("Geolokasi browser gagal (kode " + error.code + "): " + error.message);
                    fallbackToIP();
                }, { enableHighAccuracy: true });
            } else {
                fallbackToIP();
            }
        }
    });
</script>

<script>
function generateAiDescription() {
    let title = document.querySelector('input[name="title"]').value;
    let price = document.querySelector('input[name="price"]') ? document.querySelector('input[name="price"]').value : '';
    let transaction = '{{ $kategori }}';
    
    if (!title || !price) {
        Swal.fire('Perhatian', 'Silakan isi Judul Iklan dan Harga terlebih dahulu agar AI bisa membuat deskripsi yang akurat.', 'warning');
        return;
    }

    let category = '';
    let categorySelect = document.querySelector('select[name="listing_category_id"]');
    if (categorySelect && categorySelect.options[categorySelect.selectedIndex]) {
        category = categorySelect.options[categorySelect.selectedIndex].text;
    }

    let payload = {
        type: 'listing',
        title: title,
        category: category,
        price: price,
        transaction_type: transaction,
        location: document.querySelector('input[name="location"]') ? document.querySelector('input[name="location"]').value : ''
    };

    if ('{{ $kategori }}' === 'properti') {
        payload.land_area = document.querySelector('input[name="land_area"]') ? document.querySelector('input[name="land_area"]').value : '';
        payload.building_area = document.querySelector('input[name="building_area"]') ? document.querySelector('input[name="building_area"]').value : '';
        payload.bedrooms = document.querySelector('input[name="bedrooms"]') ? document.querySelector('input[name="bedrooms"]').value : '';
        payload.bathrooms = document.querySelector('input[name="bathrooms"]') ? document.querySelector('input[name="bathrooms"]').value : '';
        
        let certSelect = document.querySelector('select[name="certificate"]');
        payload.certificate = (certSelect && certSelect.selectedIndex > 0) ? certSelect.options[certSelect.selectedIndex].text : '';
        
        let furnSelect = document.querySelector('select[name="furnished_status"]');
        payload.furnished_status = (furnSelect && furnSelect.selectedIndex > 0) ? furnSelect.options[furnSelect.selectedIndex].text : '';
        
        payload.facilities = Array.from(document.querySelectorAll('input[name="facilities[]"]:checked')).map(el => el.value);
        payload.surroundings = Array.from(document.querySelectorAll('input[name="surroundings[]"]:checked')).map(el => el.value);
    } else if ('{{ $kategori }}' === 'barang') {
        let condSelect = document.querySelector('select[name="condition"]');
        payload.condition = (condSelect && condSelect.selectedIndex > 0) ? condSelect.options[condSelect.selectedIndex].text : '';
        payload.brand = document.querySelector('input[name="brand"]') ? document.querySelector('input[name="brand"]').value : '';
    } else if ('{{ $kategori }}' === 'jasa') {
        payload.service_area = document.querySelector('input[name="service_area"]') ? document.querySelector('input[name="service_area"]').value : '';
    }
    
    let btn = document.getElementById('btnAiDesc');
    let originalText = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i> Loading...';
    btn.disabled = true;

    fetch('{{ route("ai.generate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if(tinymce.get('listingDesc')) {
                tinymce.get('listingDesc').setContent(data.data);
            } else {
                document.getElementById('listingDesc').value = data.data;
            }
        } else {
            Swal.fire('Gagal', data.message || 'Gagal generate dengan AI.', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Terjadi kesalahan koneksi saat menghubungi AI.', 'error');
        console.error(error);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        lucide.createIcons();
    });
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#listingDesc',
        height: 300,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
</script>
@endif
@endpush
