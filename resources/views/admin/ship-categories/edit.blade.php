@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="mb-5">
        <a href="{{ route('admin.ship-categories.index') }}"
           class="inline-flex items-center gap-2 text-sm font-extrabold text-slate-700 hover:text-slate-900">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-3">Edit Kategori Sewa Kapal</h1>
        <p class="text-sm text-slate-600 mt-1">Ubah nama / slug kategori.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 text-sm">
            <div class="font-extrabold">Periksa input</div>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        <form action="{{ route('admin.ship-categories.update', $ship_category->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Nama</label>
                <input type="text" name="name"
                       value="{{ old('name', $ship_category->name) }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
            </div>

            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Slug</label>
                <input type="text" name="slug"
                       value="{{ old('slug', $ship_category->slug) }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <a href="{{ route('admin.ship-categories.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                        style="background:#0194F3;"
                        onmouseover="this.style.background='#0186DB'"
                        onmouseout="this.style.background='#0194F3'">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
