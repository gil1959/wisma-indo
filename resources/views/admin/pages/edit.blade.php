@extends('layouts.admin')

@section('title', 'Edit Halaman')
@section('page-title', 'Edit Halaman')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Edit Halaman</h2>
        <a href="{{ route('admin.pages.index') }}" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition text-sm">
            Kembali
        </a>
    </div>

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">Judul Halaman</label>
            <input type="text" name="title" value="{{ old('title', $page->title) }}" required placeholder="Contoh: Tentang Kami" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20 text-sm">
            @error('title')<p class="text-xs text-rose-500 font-bold mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">URL Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $page->slug) }}" required placeholder="Contoh: tentang-kami" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20 text-sm">
            <p class="text-xs text-slate-500">Hanya gunakan huruf kecil, angka, dan tanda hubung (-).</p>
            @error('slug')<p class="text-xs text-rose-500 font-bold mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">Konten Halaman</label>
            <textarea name="content" id="editor" rows="10" placeholder="Tulis isi halaman di sini..." class="w-full rounded-xl border-slate-300 hidden">{{ old('content', $page->content) }}</textarea>
            @error('content')<p class="text-xs text-rose-500 font-bold mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $page->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-[#0194F3] focus:ring-[#0194F3]">
            <label for="is_active" class="text-sm font-bold text-slate-700 cursor-pointer">Status Aktif</label>
        </div>

        <!-- NEW SEO SECTION -->
        <hr class="my-8 border-slate-200">
        <h3 class="text-lg font-bold text-slate-800">Pengaturan SEO</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: SEO Image -->
            <div class="lg:col-span-1 space-y-3">
                <label class="block text-sm font-bold text-slate-700">SEO Image</label>
                <div class="relative group">
                    <div class="w-full aspect-[2/1] rounded-2xl border-2 border-dashed border-slate-300 overflow-hidden flex flex-col items-center justify-center bg-slate-50 hover:bg-slate-100 transition cursor-pointer relative" onclick="document.getElementById('seo_image_input').click()">
                        <img id="seo_image_preview" src="{{ $page->seo_image ? asset($page->seo_image) : '' }}" class="{{ $page->seo_image ? '' : 'hidden' }} w-full h-full object-cover absolute inset-0 z-0">
                        <div class="z-10 flex flex-col items-center justify-center text-slate-400 group-hover:text-[#0194F3] {{ $page->seo_image ? 'hidden' : '' }}">
                            <i data-lucide="image" class="w-8 h-8 mb-2"></i>
                            <span class="text-sm font-medium">Klik untuk upload gambar</span>
                        </div>
                    </div>
                    <div class="absolute -bottom-3 -right-3 w-10 h-10 bg-[#5438FF] rounded-full flex items-center justify-center text-white shadow-lg cursor-pointer hover:bg-blue-700 z-20" onclick="document.getElementById('seo_image_input').click()">
                        <i data-lucide="cloud-upload" class="w-5 h-5"></i>
                    </div>
                    <input type="file" name="seo_image" id="seo_image_input" accept=".png,.jpg,.jpeg" class="hidden" onchange="previewSeoImage(this)">
                </div>
                <p class="text-sm text-slate-500 mt-2 font-medium">Supported Files: <b>.png, .jpg, .jpeg</b>.</p>
            </div>

            <!-- Right: Meta fields & Submit -->
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Meta Keywords <span class="text-slate-400 font-normal ml-2">Separate multiple keywords by , (comma) or enter key</span>
                    </label>
                    <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}" class="w-full rounded-xl border-slate-300 focus:border-[#5438FF] focus:ring-[#5438FF]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Meta Description</label>
                    <textarea name="meta_desc" rows="3" class="w-full rounded-xl border-slate-300 focus:border-[#5438FF] focus:ring-[#5438FF]">{{ old('meta_desc', $page->meta_desc) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Social Title</label>
                    <input type="text" name="social_title" value="{{ old('social_title', $page->social_title) }}" class="w-full rounded-xl border-slate-300 focus:border-[#5438FF] focus:ring-[#5438FF]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Social Description</label>
                    <textarea name="social_desc" rows="3" class="w-full rounded-xl border-slate-300 focus:border-[#5438FF] focus:ring-[#5438FF]">{{ old('social_desc', $page->social_desc) }}</textarea>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
                Update Halaman
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    // Initialize Tagify
    var input = document.querySelector('#meta_keywords');
    new Tagify(input);

    function previewSeoImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.getElementById('seo_image_preview');
                img.src = e.target.result;
                img.classList.remove('hidden');
                
                var placeholder = input.parentElement.querySelector('.z-10.flex-col');
                if(placeholder) {
                    placeholder.classList.remove('flex');
                    placeholder.classList.add('hidden');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    tinymce.init({
        selector: '#editor',
        height: 400,
        menubar: false,
        branding: false,
        promotion: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | link | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
</script>
@endpush
