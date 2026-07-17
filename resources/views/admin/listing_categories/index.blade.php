@extends('layouts.admin')
@section('title', 'Kategori Iklan')
@section('page-title', 'Kategori Iklan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Daftar Kategori Iklan</h2>
        <p class="text-slate-500 text-sm">Kelola tipe properti, barang, dan jasa untuk halaman utama.</p>
    </div>
    <a href="{{ route('admin.listing-categories.create') }}" class="bg-[#0194F3] hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 transition shadow-sm">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
    </a>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-medium flex items-center gap-3">
    <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Foto/Gambar</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Kategori</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($categories as $category)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="p-4">
                        @if($category->photo)
                            <img src="{{ asset($category->photo) }}" class="w-16 h-16 object-cover rounded-xl border border-slate-200 shadow-sm">
                        @else
                            <div class="w-16 h-16 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 border border-slate-200 shadow-sm">
                                <i data-lucide="image" class="w-6 h-6"></i>
                            </div>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="font-bold text-slate-800">{{ $category->name }}</div>
                        <div class="text-xs text-slate-500 font-medium">Slug: {{ $category->slug }}</div>
                    </td>
                    <td class="p-4">
                        @if($category->type == 'property')
                            <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100">Properti</span>
                        @elseif($category->type == 'goods')
                            <span class="px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 text-xs font-bold border border-orange-100">Barang</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100">Jasa</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.listing-categories.edit', $category->id) }}" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition" title="Edit">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.listing-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="inbox" class="w-12 h-12 text-slate-300 mb-3"></i>
                            <p class="font-medium">Belum ada data kategori.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
