@extends('layouts.admin')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="space-y-5">

    {{-- Errors --}}
    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="font-extrabold">Ada error</div>
            <ul class="mt-2 list-disc pl-5 text-sm space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.categories.update', $category) }}"
          class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 space-y-4">
        @csrf
        @method('PUT')
<div>
    <label class="block text-sm font-extrabold text-slate-800 mb-1">Jenis Kategori</label>

    <select name="parent_id"
            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        <option value="">Kategori Utama (Parent)</option>
        @foreach($parents as $p)
            <option value="{{ $p->id }}"
                {{ old('parent_id', $category->parent_id) == $p->id ? 'selected' : '' }}>
                Sub Kategori dari: {{ $p->name }}
            </option>
        @endforeach
    </select>

    @error('parent_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

        <div>
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Nama Kategori</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $category->name) }}"
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                   required>
        </div>


        <div>
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Slug</label>
            <input type="text"
                   name="slug"
                   value="{{ old('slug', $category->slug) }}"
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                   required>
        </div>

        <div class="flex items-center justify-between gap-2 pt-2">
            <a href="{{ route('admin.categories.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                Kembali
            </a>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-extrabold text-white transition"
                    style="background:#0194F3;"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection
