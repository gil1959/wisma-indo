@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="max-w-4xl mx-auto">
  <div class="flex items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">{{ $title }}</h1>
      <div class="text-sm text-slate-600 mt-1">
        {{ $banner->exists ? 'Edit banner' : 'Tambah banner baru' }} (thumbnail + link).
      </div>
    </div>

    <a href="{{ route('admin.promos.home-banners.index', ['section'=>$section]) }}" class="btn btn-ghost">Kembali</a>
  </div>

  <form method="POST"
        enctype="multipart/form-data"
        action="{{ $banner->exists
          ? route('admin.promos.home-banners.update', ['section'=>$section,'banner'=>$banner->id])
          : route('admin.promos.home-banners.store', ['section'=>$section]) }}"
        class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm">
    @csrf
    @if($banner->exists) @method('PUT') @endif

    <div class="px-6 py-5 border-b border-slate-200">
      <div class="text-lg font-extrabold text-slate-900">Detail Banner</div>
      <div class="text-sm text-slate-600 mt-1">Klik banner di homepage akan direct ke link ini.</div>
    </div>

    <div class="p-6 grid gap-5">
      <div>
        <label class="block text-sm font-semibold text-slate-900 mb-2">Thumbnail (PNG/JPG/WebP)</label>
        <input type="file" name="thumbnail" accept="image/png,image/jpeg,image/webp"
               id="banner_thumb_input"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 bg-white"
               onchange="previewBannerThumb(this)">

        @error('thumbnail') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror

        @if($banner->exists && $banner->thumbnail_path)
          <div id="banner_existing_wrap" class="mt-3 rounded-2xl border border-slate-200 overflow-hidden bg-slate-50 aspect-[16/7]">
            <img id="banner_existing_img" src="{{ asset('storage/'.$banner->thumbnail_path) }}" class="h-full w-full object-cover" alt="">
          </div>
          <div class="text-xs text-slate-500 mt-2">Upload baru untuk mengganti thumbnail.</div>
        @endif

        <div id="banner_new_wrap" class="hidden mt-3 rounded-2xl border border-blue-200 bg-blue-50 p-3">
          <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Thumbnail Baru</div>
          <div class="rounded-2xl overflow-hidden border border-slate-200 aspect-[16/7]">
            <img id="banner_new_img" src="" class="h-full w-full object-cover" alt="">
          </div>
        </div>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-900 mb-2">Link URL</label>
        <input type="text" name="link_url"
               value="{{ old('link_url', $banner->link_url) }}"
               placeholder="contoh: /tour-packages?promo=1 atau https://..."
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900">

        @error('link_url') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Urutan (sort_order)</label>
          <input type="number" name="sort_order"
                 value="{{ old('sort_order', $banner->sort_order ?? 0) }}"
                 class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900">
        </div>

        <div class="flex items-center gap-3 mt-7">
          <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300"
                 {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
          <div class="text-sm font-semibold text-slate-900">Aktif</div>
        </div>
      </div>
    </div>

    <div class="px-6 py-5 border-t border-slate-200 flex items-center justify-end gap-3">
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
  </form>
</div>
@endsection

<script>
function previewBannerThumb(input) {
    const wrap = document.getElementById('banner_new_wrap');
    const img  = document.getElementById('banner_new_img');
    const existing = document.getElementById('banner_existing_img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.classList.remove('hidden'); if(existing) existing.style.opacity='0.4'; };
        reader.readAsDataURL(input.files[0]);
    } else { wrap.classList.add('hidden'); if(existing) existing.style.opacity='1'; }
}
</script>
