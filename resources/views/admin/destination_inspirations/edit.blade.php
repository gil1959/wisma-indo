@extends('layouts.admin')

@section('title', 'Edit Inspirasi Destinasi')
@section('page-title', 'Edit Inspirasi Destinasi')

@section('content')
<div class="card p-5 max-w-2xl">
  <form method="POST" action="{{ route('admin.destination-inspirations.update', $item) }}" enctype="multipart/form-data" class="space-y-4">
  @csrf
  @method('PUT')

  <div>
    <label class="block text-sm font-bold mb-1">Judul</label>
    <input name="title" class="w-full border rounded-xl px-3 py-2" value="{{ old('title', $item->title) }}" required>
  </div>

  <div>
    <label class="block text-sm font-bold mb-1">Foto (kosongkan kalau tidak ganti)</label>
    <input type="file" name="image" id="dest_edit_img_input" class="w-full border rounded-xl px-3 py-2" accept="image/*" onchange="previewDestEditImg(this)">
    @if($item->image_path)
      <img id="dest_edit_existing" src="{{ asset('storage/'.$item->image_path) }}" class="mt-2 rounded-xl border max-w-sm" style="max-height:200px;object-fit:cover;">
    @endif
    <div id="dest_edit_new_wrap" class="hidden mt-3 rounded-2xl border border-blue-200 bg-blue-50 p-3">
      <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Foto Baru</div>
      <div class="rounded-xl overflow-hidden border border-slate-200 aspect-[3/2]">
        <img id="dest_edit_new" src="" class="h-full w-full object-cover" alt="">
      </div>
    </div>
  </div>

    <div>
    <label class="block text-sm font-bold mb-1">Kategori Tour (untuk link)</label>
    <select name="tour_category_id" class="w-full border rounded-xl px-3 py-2">
      <option value="">Semua Paket</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected(old('tour_category_id', $item->tour_category_id) == $c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
  </div>
<div class="mt-3">
  <label class="block text-sm font-bold mb-1">Sub Kategori (opsional)</label>
  <select id="inspSubcategory" name="tour_subcategory_id" class="w-full border rounded-xl px-3 py-2">
    <option value="">-- Semua Sub Kategori --</option>
  </select>
</div>

  {{-- TAMBAH BLOK INI --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-bold mb-1">Urutan</label>
      <input
        type="number"
        name="sort_order"
        class="w-full border rounded-xl px-3 py-2"
        value="{{ old('sort_order', $item->sort_order ?? 0) }}"
        min="0"
      >
    </div>

    <div class="flex items-center gap-2 mt-6">
      {{-- hidden ini penting biar kalau checkbox dimatiin tetap ngirim nilai 0 --}}
      <input type="hidden" name="is_active" value="0">
      <input
        type="checkbox"
        name="is_active"
        value="1"
        class="h-4 w-4"
        @checked(old('is_active', (bool) $item->is_active))
      >
      <label class="text-sm font-bold">Aktif</label>
    </div>
  </div>
  {{-- END BLOK TAMBAHAN --}}

  <button class="rounded-xl px-4 py-2 font-extrabold text-white" style="background:#0194F3;">Update</button>
</form>


</div>
@endsection
@push('scripts')
<script>
(function(){
  const cat = document.querySelector('select[name="tour_category_id"]');
  const sub = document.getElementById('inspSubcategory');
  const oldSub = "{{ old('tour_subcategory_id', $item->tour_subcategory_id ?? '') }}";
  async function loadSubs() {
    const catId = cat.value;
    sub.innerHTML = '<option value="">-- Semua Sub Kategori --</option>';
    if (!catId) return;
    const url = new URL("{{ route('admin.categories.subcategories', ['category' => 0]) }}".replace('/0/', '/' + catId + '/'), window.location.origin);
    const res = await fetch(url.toString(), { headers: {'X-Requested-With': 'XMLHttpRequest'}});
    if (!res.ok) return;
    const data = await res.json();
    (data.items || []).forEach(it => {
      const opt = document.createElement('option');
      opt.value = it.id;
      opt.textContent = it.name;
      if (oldSub && String(oldSub) === String(it.id)) opt.selected = true;
      sub.appendChild(opt);
    });
  }
  cat.addEventListener('change', () => { sub.value=''; loadSubs(); });
  loadSubs();
})();

function previewDestEditImg(input) {
    const wrap = document.getElementById('dest_edit_new_wrap');
    const img  = document.getElementById('dest_edit_new');
    const existing = document.getElementById('dest_edit_existing');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.classList.remove('hidden'); if(existing) existing.style.opacity='0.4'; };
        reader.readAsDataURL(input.files[0]);
    } else { wrap.classList.add('hidden'); if(existing) existing.style.opacity='1'; }
}
</script>
@endpush
