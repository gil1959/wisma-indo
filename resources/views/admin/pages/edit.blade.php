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

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
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

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
                Update Halaman
            </button>
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
        'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
</script>
@endpush
