@extends('layouts.admin')

@section('title', 'Inspirasi Destinasi')
@section('page-title', 'Inspirasi Destinasi')

@section('content')
<div class="space-y-5">

  <div class="flex items-start sm:items-center justify-between gap-3">
    <div>
      <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Inspirasi Destinasi</h2>
      <p class="mt-1 text-sm text-slate-600">Kelola chip inspirasi di halaman Home.</p>
    </div>

    <a href="{{ route('admin.destination-inspirations.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
       style="background:#0194F3;"
       onmouseover="this.style.background='#0186DB'"
       onmouseout="this.style.background='#0194F3'">
      <i data-lucide="plus" class="w-4 h-4"></i>
      Tambah
    </a>
  </div>

  <div class="card p-4 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2">Nama</th>
          <th class="py-2">Icon</th>
          <th class="py-2">Kategori Tour</th>
          <th class="py-2">Urutan</th>
          <th class="py-2">Aktif</th>
          <th class="py-2 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="text-slate-800">
        @forelse($items as $it)
          <tr class="border-t">
            <td class="py-2 font-bold">{{ $it->name }}</td>
            <td class="py-2">{{ $it->icon }}</td>
            <td class="py-2">{{ $it->tourCategory?->name ?? '-' }}</td>
            <td class="py-2">{{ $it->sort_order }}</td>
            <td class="py-2">{{ $it->is_active ? 'Ya' : 'Tidak' }}</td>
            <td class="py-2 text-right">
              <a href="{{ route('admin.destination-inspirations.edit', $it) }}"
                 class="inline-flex items-center gap-2 rounded-xl px-3 py-2 border border-slate-200 hover:bg-slate-50">
                <i data-lucide="pencil" class="w-4 h-4"></i> Edit
              </a>

              <form action="{{ route('admin.destination-inspirations.destroy', $it) }}"
                    method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl px-3 py-2 border border-red-200 text-red-600 hover:bg-red-50"
                        onclick="return confirm('Hapus inspirasi ini?')">
                  <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr class="border-t">
            <td class="py-3 text-slate-500" colspan="6">Belum ada data.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
