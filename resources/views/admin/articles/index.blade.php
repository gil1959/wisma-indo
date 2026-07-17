@extends('layouts.admin')
@section('title', 'Daftar Artikel')
@section('page-title', 'Daftar Artikel')
@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('admin.articles.create') }}" class="bg-[#0194F3] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-600 flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Artikel
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="p-4 text-sm font-bold text-slate-700">Gambar</th>
                <th class="p-4 text-sm font-bold text-slate-700">Judul Artikel</th>
                <th class="p-4 text-sm font-bold text-slate-700">Kategori</th>
                <th class="p-4 text-sm font-bold text-slate-700">Status</th>
                <th class="p-4 text-sm font-bold text-slate-700 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($articles as $a)
            <tr class="border-b border-slate-100 hover:bg-slate-50">
                <td class="p-4">
                    <img src="{{ $a->image ? asset($a->image) : 'https://via.placeholder.com/100x60' }}" class="w-16 h-10 object-cover rounded-lg border">
                </td>
                <td class="p-4 font-bold text-slate-900">{{ $a->title }}</td>
                <td class="p-4 text-slate-600">{{ $a->category->name ?? '-' }}</td>
                <td class="p-4">
                    @if($a->is_published)
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Published</span>
                    @else
                        <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-xs font-bold">Draft</span>
                    @endif
                </td>
                <td class="p-4 flex justify-end gap-2">
                    <a href="{{ route('admin.articles.edit', $a->id) }}" class="p-2 bg-slate-100 rounded-lg text-slate-600 hover:text-[#0194F3]"><i data-lucide="edit" class="w-4 h-4"></i></a>
                    <form action="{{ route('admin.articles.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Hapus artikel?');">
                        @csrf @method('DELETE')
                        <button class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-100"><i data-lucide="trash" class="w-4 h-4"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-slate-500">Belum ada artikel.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-slate-200">
        {{ $articles->links() }}
    </div>
</div>
@endsection
