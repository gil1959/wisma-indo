@extends('layouts.admin')
@section('title', 'Tambah Artikel')
@section('page-title', 'Tambah Artikel')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Judul Artikel</label>
                    <input type="text" name="title" class="w-full rounded-xl border-slate-300 focus:border-[#0194F3] focus:ring-[#0194F3]" required>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-bold text-slate-700">Konten</label>
                        <button type="button" onclick="generateAiArticle()" id="btnAiDesc" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg shadow hover:opacity-90 transition">
                            <i data-lucide="bot" class="w-3.5 h-3.5"></i> Generate Konten AI
                        </button>
                    </div>
                    <textarea name="content" id="editor" class="w-full rounded-xl border-slate-300 hidden"></textarea>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Publikasi</label>
                    <label class="flex items-center gap-2 mb-4">
                        <input type="checkbox" name="is_published" value="1" checked class="rounded text-[#0194F3] focus:ring-[#0194F3]">
                        <span class="text-sm font-bold text-slate-700">Publish Langsung</span>
                    </label>
                    <button type="submit" class="w-full bg-[#0194F3] text-white px-4 py-3 rounded-xl font-bold hover:bg-blue-600">Simpan Artikel</button>
                </div>

                <!-- Kategori dan Gambar Sampul -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full rounded-xl border-slate-300 focus:border-[#0194F3] focus:ring-[#0194F3]">
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Gambar Sampul (Thumbnail)</label>
                    <input type="file" name="image" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#0194F3] hover:file:bg-blue-100">
                </div>
            </div>
        </div>

        <!-- NEW SEO SECTION -->
        <hr class="my-8 border-slate-200">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: SEO Image -->
            <div class="lg:col-span-1 space-y-3">
                <label class="block text-sm font-bold text-slate-700">SEO Image</label>
                <div class="relative group">
                    <div class="w-full aspect-[2/1] rounded-2xl border-2 border-dashed border-slate-300 overflow-hidden flex flex-col items-center justify-center bg-slate-50 hover:bg-slate-100 transition cursor-pointer relative" onclick="document.getElementById('seo_image_input').click()">
                        <img id="seo_image_preview" src="" class="hidden w-full h-full object-cover absolute inset-0 z-0">
                        <div class="z-10 flex flex-col items-center justify-center text-slate-400 group-hover:text-[#0194F3]">
                            <i data-lucide="image" class="w-8 h-8 mb-2"></i>
                            <span class="text-sm font-medium">Klik untuk upload gambar</span>
                        </div>
                    </div>
                    <div class="absolute -bottom-3 -right-3 w-10 h-10 bg-[#5438FF] rounded-full flex items-center justify-center text-white shadow-lg cursor-pointer hover:bg-blue-700 z-20" onclick="document.getElementById('seo_image_input').click()">
                        <i data-lucide="cloud-upload" class="w-5 h-5"></i>
                    </div>
                    <input type="file" name="seo_image" id="seo_image_input" accept=".png,.jpg,.jpeg" class="hidden" onchange="previewSeoImage(this)">
                </div>
                <p class="text-sm text-slate-500 mt-2 font-medium">Supported Files: <b>.png, .jpg, .jpeg</b>. Image will be resized into <b>1180x600px</b></p>
            </div>

            <!-- Right: Meta fields & Submit -->
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Meta Keywords <span class="text-slate-400 font-normal ml-2">Separate multiple keywords by , (comma) or enter key</span>
                    </label>
                    <input type="text" name="meta_keywords" id="meta_keywords" class="w-full rounded-xl border-slate-300 focus:border-[#5438FF] focus:ring-[#5438FF]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Meta Description</label>
                    <textarea name="meta_desc" rows="3" class="w-full rounded-xl border-slate-300 focus:border-[#5438FF] focus:ring-[#5438FF]"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Social Title</label>
                    <input type="text" name="social_title" class="w-full rounded-xl border-slate-300 focus:border-[#5438FF] focus:ring-[#5438FF]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Social Description</label>
                    <textarea name="social_desc" rows="3" class="w-full rounded-xl border-slate-300 focus:border-[#5438FF] focus:ring-[#5438FF]"></textarea>
                </div>

                <button type="submit" class="w-full bg-[#3B30FF] text-white px-4 py-3 rounded-xl font-bold hover:bg-blue-700 transition">Submit</button>
            </div>
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

    function generateAiArticle() {
        let title = document.querySelector('input[name="title"]').value;
        if (!title) {
            Swal.fire('Perhatian', 'Silakan isi Judul Artikel terlebih dahulu.', 'warning');
            return;
        }
        
        let btn = document.getElementById('btnAiDesc');
        let originalText = btn.innerHTML;
        btn.innerHTML = '<i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i> Loading...';
        btn.disabled = true;

        let category = '';
        let categorySelect = document.querySelector('select[name="article_category_id"]');
        if (categorySelect && categorySelect.options[categorySelect.selectedIndex]) {
            category = categorySelect.options[categorySelect.selectedIndex].text;
        }
        
        fetch('{{ route("ai.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                type: 'article',
                title: title,
                category: category
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if(tinymce.get('editor')) {
                    tinymce.get('editor').setContent(data.data);
                } else {
                    document.getElementById('editor').value = data.data;
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
@endpush
