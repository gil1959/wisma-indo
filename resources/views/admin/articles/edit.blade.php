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
                    <label class="block text-sm font-bold text-slate-700 mb-2">Konten</label>
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
<script src="https://cdn.ckeditor.com/ckeditor5/38.1.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => { console.error(error); });
</script>
<style>
.ck-editor__editable_inline { min-height: 400px; }
</style>
@endpush
