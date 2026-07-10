@extends('partner.layouts.app')

@section('page-title','Paket Sewa Kapal')

@section('content')
<div class="flex items-center justify-between mb-4">
  <div>
    <div class="text-xl font-extrabold text-slate-900">Paket Sewa Kapal</div>
    <div class="text-sm text-slate-500">CRUD paket sewa kapal + harga weekday/weekend.</div>
  </div>

  <a href="{{ route('partner.ship-packages.create') }}"
     class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-extrabold text-white"
     style="background:#0194F3"
     onmouseover="this.style.background='#0186DB'"
     onmouseout="this.style.background='#0194F3'">
    <i data-lucide="plus" class="w-4 h-4"></i>
    Tambah Paket
  </a>
</div>
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 mb-4">
  <form method="GET" action="{{ route('partner.ship-packages.index') }}"
        class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

    <div class="md:col-span-4">
      <label class="block text-xs font-extrabold text-slate-600 mb-1">Kategori</label>
      <select name="category_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        <option value="">All</option>
        @foreach($categories as $c)
          <option value="{{ $c->id }}" {{ (string)request('category_id') === (string)$c->id ? 'selected' : '' }}>
            {{ $c->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-3">
      <label class="block text-xs font-extrabold text-slate-600 mb-1">Status</label>
      <select name="active" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        <option value="">All</option>
        <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>

    <div class="md:col-span-5">
      <label class="block text-xs font-extrabold text-slate-600 mb-1">Search</label>
      <input type="text" name="q" value="{{ request('q') }}"
             placeholder="Cari title / slug"
             class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
    </div>

    <div class="md:col-span-12 flex gap-2 justify-end pt-1">
      <button class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-extrabold text-white"
              style="background:#0194F3">
        Filter
      </button>

      <a href="{{ route('partner.ship-packages.index') }}"
         class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50">
        Reset
      </a>
    </div>

  </form>
</div>

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
              <a href="{{ route('partner.ship-packages.edit', $p->id) }}"
                 class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 hover:bg-slate-50">
                <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                Edit
              </a>

              <form action="{{ route('partner.ship-packages.destroy', $p->id) }}"
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
