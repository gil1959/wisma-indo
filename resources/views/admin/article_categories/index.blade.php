@extends('layouts.admin')
@section('title', 'Kategori Artikel')
@section('page-title', 'Kategori Artikel')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
    <form action="{{ route('admin.article-categories.store') }}" method="POST" class="mb-6 flex gap-3">
        @csrf
        <input type="text" name="name" placeholder="Nama Kategori Baru" class="flex-1 rounded-xl border-slate-300 focus:border-[#0194F3] focus:ring-[#0194F3]" required>
        <button type="submit" class="bg-[#0194F3] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-600">Tambah</button>
    </form>
    
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="p-3 text-sm font-bold text-slate-700">Nama Kategori</th>
                <th class="p-3 text-sm font-bold text-slate-700">Slug</th>
                <th class="p-3 text-sm font-bold text-slate-700 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
            <tr class="border-b border-slate-100">
                <td class="p-3 font-bold">{{ $cat->name }}</td>
                <td class="p-3 text-slate-500">{{ $cat->slug }}</td>
                <td class="p-3 flex justify-end gap-2">
                    <form action="{{ route('admin.article-categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded-lg"><i data-lucide="trash" class="w-4 h-4"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $categories->links() }}</div>
</div>
@endsection
