@extends('layouts.admin')

@section('title', 'Tambah Kategori MICE')
@section('page-title', 'Tambah Kategori MICE')

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

    <form action="{{ route('admin.mice-categories.store') }}" method="POST" class="space-y-4">
        @csrf

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 font-extrabold text-white" style="background:#0194F3;">Data Kategori</div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Slug (opsional)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           placeholder="Jika kosong akan dibuat otomatis">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.mice-categories.index') }}"
               class="rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50">
                Kembali
            </a>
            <button type="submit"
                    class="rounded-xl px-4 py-2.5 text-sm font-extrabold text-white"
                    style="background:#0194F3;">
                Simpan
            </button>
        </div>
    </form>

</div>
@endsection
