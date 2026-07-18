@extends('layouts.admin')
@section('title', 'Edit Artikel')
@section('page-title', 'Edit Artikel')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Judul Artikel</label>
                    <input type="text" name="title" value="{{ old('title', $article->title) }}" class="w-full rounded-xl border-slate-300 focus:border-[#0194F3] focus:ring-[#0194F3]" required>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-bold text-slate-700">Konten</label>
                        <button type="button" onclick="generateAiArticle()" id="btnAiDesc" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg shadow hover:opacity-90 transition">
                            <i data-lucide="bot" class="w-3.5 h-3.5"></i> Generate Konten AI
                        </button>
                    </div>
                    <textarea name="content" id="editor" class="w-full rounded-xl border-slate-300 hidden">{!! old('content', $article->content) !!}</textarea>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Publikasi</label>
                    <label class="flex items-center gap-2 mb-4">
                        <input type="checkbox" name="is_published" value="1" {{ $article->is_published ? 'checked' : '' }} class="rounded text-[#0194F3] focus:ring-[#0194F3]">
                        <span class="text-sm font-bold text-slate-700">Publish Langsung</span>
                    </label>
                    <button type="submit" class="w-full bg-[#0194F3] text-white px-4 py-3 rounded-xl font-bold hover:bg-blue-600">Update Artikel</button>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full rounded-xl border-slate-300 focus:border-[#0194F3] focus:ring-[#0194F3]">
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $article->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Gambar Sampul</label>
                    @if($article->image)
                    <img src="{{ asset($article->image) }}" class="w-full h-32 object-cover rounded-xl mb-3 border">
                    @endif
                    <input type="file" name="image" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#0194F3] hover:file:bg-blue-100">
                </div>
                
                <hr>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">SEO Title (Opsional)</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $article->meta_title) }}" class="w-full rounded-xl border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">SEO Description (Opsional)</label>
                    <textarea name="meta_desc" rows="3" class="w-full rounded-xl border-slate-300 text-sm">{{ old('meta_desc', $article->meta_desc) }}</textarea>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
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
