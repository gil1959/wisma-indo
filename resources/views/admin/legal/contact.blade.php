@extends('layouts.admin')
@section('title', 'Kontak')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        
        <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-[#0194F3]">
            <h2 class="text-xl font-bold text-white">Contact Page</h2>
            <div class="text-white/80 text-sm font-semibold italic">/contact</div>
        </div>

        <form action="{{ route('admin.legal.contact.update') }}" method="POST" class="p-6">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Judul</label>
                <input type="text" name="title" value="{{ old('title', $title) }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3] focus:ring-opacity-20" required>
                @error('title')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Konten Tambahan (Di bawah info alamat dll)</label>
                <textarea name="content" id="editor" class="w-full rounded-xl border-slate-200 hidden">{!! old('content', $content) !!}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#0194F3] hover:bg-blue-600 text-white font-bold rounded-xl shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/38.1.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
</script>
<style>
.ck-editor__editable_inline { min-height: 400px; border-radius: 0 0 12px 12px !important; }
.ck-toolbar { border-radius: 12px 12px 0 0 !important; }
</style>
@endpush
