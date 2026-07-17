@extends('layouts.admin')
@section('title', 'Edit Kategori Iklan')
@section('page-title', 'Edit Kategori Iklan')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.listing-categories.index') }}" class="text-[#0194F3] font-bold text-sm flex items-center gap-2 hover:underline w-max">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Kategori
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-2xl">
    <form action="{{ route('admin.listing-categories.update', $listingCategory->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Kategori</label>
            <select name="type" required class="w-full rounded-xl border-slate-300 focus:ring-[#0194F3] focus:border-[#0194F3]">
                <option value="">Pilih Tipe...</option>
                <option value="property" {{ old('type', $listingCategory->type) == 'property' ? 'selected' : '' }}>Properti (Contoh: Rumah, Gudang, Ruko)</option>
                <option value="goods" {{ old('type', $listingCategory->type) == 'goods' ? 'selected' : '' }}>Barang (Contoh: Elektronik, Furniture)</option>
                <option value="services" {{ old('type', $listingCategory->type) == 'services' ? 'selected' : '' }}>Jasa (Contoh: Renovasi, Pindahan)</option>
            </select>
            @error('type') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Kategori</label>
            <input type="text" name="name" value="{{ old('name', $listingCategory->name) }}" required placeholder="Contoh: Gudang" class="w-full rounded-xl border-slate-300 focus:ring-[#0194F3] focus:border-[#0194F3]">
            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Foto/Gambar (Biarkan kosong jika tidak ingin mengubah)</label>
            
            @if($listingCategory->photo)
            <div class="mb-4">
                <p class="text-xs font-bold text-slate-500 mb-2">Foto Saat Ini:</p>
                <img src="{{ asset($listingCategory->photo) }}" alt="Current Photo" class="h-32 rounded-xl object-cover border border-slate-200 shadow-sm">
            </div>
            @endif

            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:bg-slate-50 transition relative">
                <div class="space-y-1 text-center">
                    <i data-lucide="image-plus" class="mx-auto h-12 w-12 text-slate-400"></i>
                    <div class="flex text-sm text-slate-600 justify-center">
                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-bold text-[#0194F3] hover:text-blue-600 focus-within:outline-none">
                            <span>Upload foto baru</span>
                            <input id="file-upload" name="photo" type="file" class="sr-only" accept="image/*">
                        </label>
                    </div>
                    <p class="text-xs text-slate-500">PNG, JPG, JPEG up to 2MB. Resolusi disarankan: 1:1 atau 4:3</p>
                </div>
            </div>
            @error('photo') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            
            <!-- Image preview -->
            <div id="image-preview-container" class="mt-4 hidden">
                <p class="text-xs font-bold text-slate-500 mb-2">Preview Foto Baru:</p>
                <img id="image-preview" src="#" alt="Preview" class="h-48 rounded-xl object-cover border border-slate-200">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-[#0194F3] hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl transition shadow-md shadow-[#0194F3]/20">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('file-upload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = document.getElementById('image-preview-container');
                const previewImage = document.getElementById('image-preview');
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
