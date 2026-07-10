@extends('partner.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="mb-5">
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Tambah Kategori Tour</h1>
        <p class="text-sm text-slate-600 mt-1">Bisa kategori parent atau subkategori.</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        <form method="POST" action="{{ route('partner.tour-categories.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-extrabold text-slate-600 mb-1">Nama</label>
                <input name="name" value="{{ old('name') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                @error('name') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-600 mb-1">Slug</label>
                <input name="slug" value="{{ old('slug') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                @error('slug') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-600 mb-1">Parent (Opsional)</label>
                <select name="parent_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                    <option value="">- Tidak ada (kategori parent) -</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('partner.tour-categories.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 transition">
                    Kembali
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
