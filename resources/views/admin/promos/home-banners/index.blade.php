@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">{{ $title }}</h1>
      <div class="text-sm text-slate-600 mt-1">Kelola banner untuk homepage. Klik banner di homepage akan direct ke link.</div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.promos.home-banners.create', ['section'=>$section]) }}" class="btn btn-primary">
        <i data-lucide="plus" class="w-5 h-5"></i>
        Tambah Banner
      </a>
      <a href="{{ route('admin.promos.index') }}" class="btn btn-ghost">Kembali</a>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-800">
      <div class="font-extrabold">Sukses</div>
      <div class="text-sm mt-1">{{ session('success') }}</div>
    </div>
  @endif

  <div class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
      <div class="font-extrabold text-slate-900">Daftar Banner</div>
      <div class="text-xs text-slate-500">Total: {{ $items->count() }}</div>
    </div>

    <div class="p-6">
      @if($items->count() === 0)
        <div class="text-slate-500">Belum ada banner.</div>
      @else
        <div class="grid gap-4 md:grid-cols-2">
          @foreach($items as $it)
            <div class="rounded-2xl border border-slate-200 p-4 flex gap-4">
              <div class="w-44 shrink-0">
                <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50 aspect-[16/7]">
                  <img src="{{ $it->thumbnail_path ? asset('storage/'.$it->thumbnail_path) : '' }}"
                       class="h-full w-full object-cover" alt="">
                </div>
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="text-sm font-extrabold text-slate-900">#{{ $it->id }}</div>
                  <span class="text-xs px-2 py-1 rounded-full border
                    {{ $it->is_active ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-100 border-slate-200 text-slate-600' }}">
                    {{ $it->is_active ? 'AKTIF' : 'NONAKTIF' }}
                  </span>
                </div>

                <div class="mt-2 text-xs text-slate-600 break-all">
                  <div><span class="font-semibold">Link:</span> {{ $it->link_url }}</div>
                  <div class="mt-1"><span class="font-semibold">Urutan:</span> {{ $it->sort_order }}</div>
                </div>

                <div class="mt-3 flex items-center gap-2">
                  <a href="{{ route('admin.promos.home-banners.edit', ['section'=>$section,'banner'=>$it->id]) }}"
                     class="btn btn-ghost !px-3 !py-2">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                    Edit
                  </a>

                  <form method="POST"
                        action="{{ route('admin.promos.home-banners.destroy', ['section'=>$section,'banner'=>$it->id]) }}"
                        onsubmit="return confirm('Hapus banner ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost !px-3 !py-2 !text-rose-600">
                      <i data-lucide="trash" class="w-4 h-4"></i>
                      Hapus
                    </button>
                  </form>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
