@extends('layouts.admin')

@section('content')
<div class="py-6 min-h-screen bg-slate-50" x-data="listingForm()">
    <div class="max-w-4xl mx-auto px-4">
        
        <div class="mb-8">
            <a href="{{ route('admin.listings.index') }}" class="text-[#0194F3] font-semibold flex items-center gap-1 hover:underline mb-4">
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

        <form action="{{ route('admin.listings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                    <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Lengkap *</label>
                    <textarea name="description" required rows="5" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="Jelaskan detail keunggulan, spesifikasi, dan hal menarik lainnya dari iklan Anda...">{{ old('description') }}</textarea>
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
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi Singkat *</label>
                    <input type="text" name="location" required value="{{ old('location') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="Contoh: Jakarta Selatan">
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Lengkap</label>
                    <textarea name="address" rows="3" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="Jalan, RT/RW, Kecamatan...">{{ old('address') }}</textarea>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Google Maps Embed URL (Opsional)</label>
                    <input type="url" name="maps_url" value="{{ old('maps_url') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="Contoh: https://www.google.com/maps/embed?...">
                    <p class="text-xs text-slate-500 mt-2">Buka Google Maps, klik Bagikan > Sematkan peta > Salin HTML, lalu ambil link di dalam <code>src="..."</code> saja.</p>
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
                <a href="{{ route('admin.listings.index') }}" class="px-6 py-3 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
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
@endpush

@push('scripts')
<script>
    function listingForm() {
        return {
            transactionType: '{{ old('transaction_type', 'dijual') }}',
        }
    }
</script>
@endpush
