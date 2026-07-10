@extends('layouts.admin')


@section('title', 'Edit Logo')

@section('content')
<div class="admin-container py-6 max-w-3xl">
  <div class="flex items-center justify-between mb-5">
    <h1 class="text-2xl font-extrabold">Edit Logo</h1>
    <a href="{{ route('admin.client-logos.index') }}" class="btn btn-ghost">
      <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
  </div>

  @if(session('success'))
    <div class="alert-success mb-4">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert-error mb-4">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form class="card p-6 space-y-4" method="POST" action="{{ route('admin.client-logos.update', $clientLogo->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div>
      <label class="label">Nama</label>
      <input class="input" name="name" value="{{ old('name', $clientLogo->name) }}" required>
    </div>

    <div>
      <label class="label">Logo Sekarang</label>
      <div class="card p-4 bg-slate-50">
        <img src="{{ asset('storage/'.$clientLogo->image_path) }}" alt="{{ $clientLogo->name }}" class="h-12 object-contain">
      </div>
    </div>

    <div>
      <label class="label">Ganti Logo (opsional)</label>
      <input class="input file:mr-3 file:rounded-xl file:border-0 file:px-4 file:py-2 file:text-sm file:font-extrabold file:text-white file:shadow-sm file:[background:#0194F3]"
             type="file" name="logo" id="logo_edit_input" accept="image/*" onchange="previewLogoEdit(this)">
      <p class="text-xs text-slate-500 mt-1">Kalau gak upload, logo lama dipakai.</p>
      <div id="logo_edit_new_wrap" class="hidden mt-3 rounded-2xl border border-blue-200 bg-blue-50 p-3">
        <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Logo Baru</div>
        <div class="h-16 rounded-xl overflow-hidden border border-slate-200 bg-white flex items-center justify-center p-2">
          <img id="logo_edit_new_preview" src="" class="max-h-full max-w-full object-contain" alt="">
        </div>
      </div>
    </div>

    <div>
      <label class="label">URL (opsional)</label>
      <input class="input" name="url" value="{{ old('url', $clientLogo->url) }}" placeholder="https://...">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="label">Urutan (sort order)</label>
        <input class="input" type="number" name="sort_order" value="{{ old('sort_order', $clientLogo->sort_order) }}" min="0">
      </div>

      <div class="flex items-end gap-2">
        <input id="is_active" type="checkbox" name="is_active" value="1" class="accent-[#0194F3]"
               {{ old('is_active', $clientLogo->is_active) ? 'checked' : '' }}>
        <label for="is_active" class="text-sm font-semibold text-slate-700">Aktif</label>
      </div>
    </div>

    <div class="pt-2 flex gap-2 justify-end">
      <button class="btn btn-primary" type="submit">
        <i data-lucide="save" class="w-4 h-4"></i> Update
      </button>
    </div>
  </form>
</div>
@endsection
<script>
function previewLogoEdit(input) {
    const wrap = document.getElementById('logo_edit_new_wrap');
    const img  = document.getElementById('logo_edit_new_preview');
    const existing = document.querySelector('.card.p-4.bg-slate-50 img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.classList.remove('hidden'); if(existing) existing.style.opacity='0.4'; };
        reader.readAsDataURL(input.files[0]);
    } else { wrap.classList.add('hidden'); if(existing) existing.style.opacity='1'; }
}
</script>
