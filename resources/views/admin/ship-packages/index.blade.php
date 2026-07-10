@extends('layouts.admin')

@section('page-title','Paket Sewa Kapal')

@section('content')
<div class="flex items-center justify-between mb-4">
  <div>
    <div class="text-xl font-extrabold text-slate-900">Paket Sewa Kapal</div>
    <div class="text-sm text-slate-500">CRUD paket sewa kapal + harga weekday/weekend.</div>
  </div>

  <a href="{{ route('admin.ship-packages.create') }}"
     class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-extrabold text-white"
     style="background:#0194F3"
     onmouseover="this.style.background='#0186DB'"
     onmouseout="this.style.background='#0194F3'">
    <i data-lucide="plus" class="w-4 h-4"></i>
    Tambah Paket
  </a>
</div>
{{-- Filter --}}
<form method="GET" action="{{ url()->current() }}"
      class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
    <div class="grid gap-3 md:grid-cols-12 items-end">

        <div class="md:col-span-5">
            <label class="block text-sm font-extrabold text-slate-700 mb-2">Pencarian</label>
            <input type="text"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Cari judul / slug..."
                   class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
        </div>

        <div class="md:col-span-3">
            <label class="block text-sm font-extrabold text-slate-700 mb-2">Kategori</label>
            <select name="category" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">Semua</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-extrabold text-slate-700 mb-2">Status</label>
            <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">Semua</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
        </div>

        <div class="md:col-span-2 flex gap-2">
            <button type="submit" class="w-full rounded-xl px-4 py-2.5 text-sm font-extrabold text-white"
                    style="background:#0194F3;"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'">
                Terapkan
            </button>

            <a href="{{ url()->current() }}"
               class="w-full rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-center">
                Reset
            </a>
        </div>

    </div>
</form>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-700">
        <tr>
          <th class="text-left px-4 py-3 font-extrabold">Paket</th>
          <th class="text-left px-4 py-3 font-extrabold">Kategori</th>
          <th class="text-left px-4 py-3 font-extrabold">Status</th>
          <th class="text-right px-4 py-3 font-extrabold">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
        @forelse($packages as $p)
          <tr>
            <td class="px-4 py-3">
              <div class="font-extrabold text-slate-900">{{ $p->title }}</div>
              <div class="text-xs text-slate-500">/{{ $p->slug }}</div>
              @if($p->label)
                <div class="mt-1 inline-flex text-[11px] font-extrabold px-2 py-1 rounded-full border"
                     style="border-color:rgba(1,148,243,0.25); background:rgba(1,148,243,0.08); color:#0194F3;">
                  {{ $p->label }}
                </div>
              @endif
            </td>
            <td class="px-4 py-3">
              {{ $p->category?->name ?? '-' }}
            </td>
            <td class="px-4 py-3">
              @if($p->is_active)
                <span class="inline-flex px-2 py-1 rounded-full text-xs font-extrabold border border-emerald-200 bg-emerald-50 text-emerald-700">Active</span>
              @else
                <span class="inline-flex px-2 py-1 rounded-full text-xs font-extrabold border border-rose-200 bg-rose-50 text-rose-700">Inactive</span>
              @endif
            </td>
            <td class="px-4 py-3 text-right whitespace-nowrap">
              <a href="{{ route('admin.ship-packages.edit', $p->id) }}"
                 class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 hover:bg-slate-50">
                <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                Edit
              </a>

              <form action="{{ route('admin.ship-packages.destroy', $p->id) }}"
                    method="POST"
                    class="inline-block"
                    onsubmit="return confirm('Hapus paket ini?')">
                @csrf
                @method('DELETE')
                <button
                  class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white"
                  style="background:#ef4444"
                  onmouseover="this.style.background='#dc2626'"
                  onmouseout="this.style.background='#ef4444'">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                  Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-4 py-8 text-center text-slate-500">
              Belum ada paket sewa kapal.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="p-4">
    {{ $packages->links() }}
  </div>
</div>
@endsection
