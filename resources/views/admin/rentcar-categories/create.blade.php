@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="mb-5">
        <a href="{{ route('admin.rent-car-categories.index') }}"
           class="inline-flex items-center gap-2 text-sm font-extrabold text-slate-700 hover:text-slate-900">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-3">Tambah Kategori Rental</h1>
        <p class="text-sm text-slate-600 mt-1">Buat kategori untuk paket rent car.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 text-sm">
            <div class="font-extrabold mb-1">Ada error:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <p class="text-sm font-extrabold text-slate-800">Form Kategori</p>
        </div>

        <form action="{{ route('admin.rent-car-categories.store') }}" method="POST" class="p-5 grid gap-4">
            @csrf

            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Nama</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       placeholder="Contoh: SUV, City Car, Luxury">
            </div>

            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Slug</label>
                <input type="text"
                       name="slug"
                       value="{{ old('slug') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       placeholder="Contoh: suv, city-car, luxury">
                <p class="text-xs text-slate-500 mt-1">Slug harus unik dan tanpa spasi (pakai dash).</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <a href="{{ route('admin.rent-car-categories.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-extrabold text-slate-800 hover:bg-slate-50">
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                        style="background:#0194F3;"
                        onmouseover="this.style.background='#0186DB'"
                        onmouseout="this.style.background='#0194F3'">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
