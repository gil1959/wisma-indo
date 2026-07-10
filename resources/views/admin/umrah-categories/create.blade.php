@extends('layouts.admin')

@section('title', 'Tambah Kategori Umrah')
@section('page-title', 'Tambah Kategori Umrah')

@section('content')
<div class="space-y-5">

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

    <form action="{{ route('admin.umrah-categories.store') }}" method="POST" class="space-y-4">
        @csrf

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 space-y-4">
            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
            </div>

            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.umrah-categories.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                Kembali
            </a>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-extrabold text-white transition"
                    style="background:#0194F3;"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan
            </button>
        </div>
    </form>

</div>
@endsection
