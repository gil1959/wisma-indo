@php
  $pkg = $package ?? null;

  // Domestic
  $domOld = old('tiers.domestic');
  $domDb = ($pkg?->tiers ?? collect())
    ->where('type','domestic')
    ->map(fn($t) => [
      'id' => $t->id,
      'label_text' => $t->label_text,
      'price' => (int) $t->price,
      'sort_order' => (int) ($t->sort_order ?? 0),
    ])->values()->toArray();

  $domTiers = $domOld ?? $domDb;
  if (!$domTiers || count($domTiers) === 0) {
    $domTiers = [
      ['type'=>'domestic','id'=>null,'label_text'=>'','price'=>0,'sort_order'=>0],
    ];
  }

  // Foreign (optional)
  $forOld = old('tiers.foreign');
  $forDb = ($pkg?->tiers ?? collect())
    ->where('type','foreign')
    ->map(fn($t) => [
      'id' => $t->id,
      'label_text' => $t->label_text,
      'price' => (int) $t->price,
      'sort_order' => (int) ($t->sort_order ?? 0),
    ])->values()->toArray();

  $forTiers = $forOld ?? $forDb;
  if (!$forTiers || count($forTiers) === 0) {
    $forTiers = [
      ['type'=>'foreign','id'=>null,'label_text'=>'','price'=>0,'sort_order'=>0],
    ];
  }
@endphp

{{-- INFORMASI DASAR --}}
<div x-data="{ open: true }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
          class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
          style="background:#0194F3;">
    <span>Informasi Dasar Paket</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
      <div class="md:col-span-6">
        <label class="block text-sm font-bold text-slate-800 mb-1">Judul Paket</label>
        <input type="text" name="title"
               value="{{ old('title', $pkg->title ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               required>
      </div>

      <div class="md:col-span-3">
        <label class="block text-sm font-bold text-slate-800 mb-1">Label (opsional)</label>
        <input type="text" name="label"
               value="{{ old('label', $pkg->label ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               placeholder="Best Seller / Promo">
      </div>

      <div class="md:col-span-3">
        <label class="block text-sm font-bold text-slate-800 mb-1">Slug</label>
        <input type="text" name="slug"
               value="{{ old('slug', $pkg->slug ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               required>
      </div>

      <div class="md:col-span-4">
  <label class="block text-sm font-bold text-slate-800 mb-1">Kategori</label>
  <select name="category_id"
          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
          required>
    <option value="">Pilih kategori</option>
    @foreach ($categories as $c)
      <option value="{{ $c->id }}" {{ old('category_id', $pkg->category_id ?? '') == $c->id ? 'selected' : '' }}>
        {{ $c->name }}
      </option>
    @endforeach
  </select>
</div>

      <div class="md:col-span-4">
        <label class="block text-sm font-bold text-slate-800 mb-1">Destination (opsional)</label>
        <input type="text" name="destination"
               value="{{ old('destination', $pkg->destination ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
      </div>

      <div class="md:col-span-4">
        <label class="block text-sm font-bold text-slate-800 mb-1">Durasi (opsional)</label>
        <input type="text" name="duration_text"
               value="{{ old('duration_text', $pkg->duration_text ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               placeholder="Contoh: 3 Hari 2 Malam">
      </div>

      <div class="md:col-span-4">
        <label class="block text-sm font-bold text-slate-800 mb-1">Rating Value (opsional)</label>
        <input type="number" min="1" max="5" name="rating_value"
               value="{{ old('rating_value', $pkg->rating_value ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
      </div>

      <div class="md:col-span-4">
        <label class="block text-sm font-bold text-slate-800 mb-1">Rating Count (opsional)</label>
        <input type="number" min="0" name="rating_count"
               value="{{ old('rating_count', $pkg->rating_count ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
      </div>

      <div class="md:col-span-4">
        <label class="block text-sm font-bold text-slate-800 mb-1">Status</label>
        @php $active = old('is_active', isset($pkg) ? (int)$pkg->is_active : 1); @endphp
        <select name="is_active"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                required>
          <option value="1" @selected((string)$active === '1')>Aktif</option>
          <option value="0" @selected((string)$active === '0')>Nonaktif</option>
        </select>
      </div>
    </div>
  </div>
</div>

{{-- THUMBNAIL & GALLERY --}}
<div x-data="{ open: true }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
          class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
          style="background:#0194F3;">
    <span>Media</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-bold text-slate-800 mb-1">Thumbnail (opsional)</label>
        <input type="file" name="thumbnail" id="mice_thumb_input" class="block w-full text-sm text-slate-600" accept="image/*" onchange="previewMiceThumb(this)">
        @if(!empty($pkg?->thumbnail_path))
          <img id="mice_thumb_existing" src="{{ asset('storage/'.$pkg->thumbnail_path) }}" class="mt-3 w-full max-w-sm rounded-2xl border border-slate-200" alt="thumb">
        @endif
        <div id="mice_thumb_new_wrap" class="hidden mt-3 rounded-2xl border border-blue-200 bg-blue-50 p-3">
          <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Thumbnail Baru</div>
          <div class="h-28 rounded-xl overflow-hidden border border-slate-200">
            <img id="mice_thumb_new" src="" class="h-full w-full object-cover" alt="">
          </div>
        </div>
      </div>

      <div>
        <label class="block text-sm font-bold text-slate-800 mb-1">Gallery (opsional, multiple)</label>
        <input type="file" name="gallery[]" multiple id="mice_gallery_input" class="block w-full text-sm text-slate-600" accept="image/*" onchange="previewMiceGallery(this)">
        <div id="mice_gallery_new_wrap" class="hidden mt-3">
          <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Gallery Baru</div>
          <div id="mice_gallery_new_grid" class="grid grid-cols-3 gap-2"></div>
        </div>

        @if($pkg && $pkg->photos && $pkg->photos->count())
          <div class="mt-3 grid grid-cols-2 gap-3">
            @foreach($pkg->photos as $p)
              <div class="rounded-2xl border border-slate-200 overflow-hidden">
                <img src="{{ asset('storage/'.$p->file_path) }}" class="w-full h-28 object-cover" alt="photo">
                <div class="p-2">
                  <button type="button"
        class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
        style="background:#ef4444"
        onmouseover="this.style.background='#dc2626'"
        onmouseout="this.style.background='#ef4444'"
        onclick="window.__bwDeletePhoto('{{ route('admin.mice-packages.delete-photo', $p->id) }}')">
    <i data-lucide="trash-2" class="w-4 h-4"></i>
    Hapus
</button>

                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- TIERS: DOMESTIC (WAJIB) --}}
<div x-data="{ open: true }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
          class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
          style="background:#0194F3;">
    <span>Harga Domestik (Text + Harga)</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5">
    <div x-data="{
          rows: @js($domTiers),
          add() { this.rows.push({type:'domestic', id:null, label_text:'', price:0, sort_order:0}); },
          remove(i){ this.rows.splice(i,1); }
        }" class="space-y-4">

      <template x-for="(row, i) in rows" :key="i">
        <div class="rounded-2xl border border-slate-200 p-4 space-y-3">
          <input type="hidden" :name="'tiers[domestic]['+i+'][type]'" x-model="row.type">
          <input type="hidden" :name="'tiers[domestic]['+i+'][id]'" x-model="row.id">

          <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-7">
              <label class="block text-xs font-extrabold text-slate-700 mb-1">Text (bebas)</label>
              <input type="text" :name="'tiers[domestic]['+i+'][label_text]'" x-model="row.label_text"
                     class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            </div>

            <div class="md:col-span-3">
              <label class="block text-xs font-extrabold text-slate-700 mb-1">Harga (angka)</label>
              <input type="number" min="0" :name="'tiers[domestic]['+i+'][price]'" x-model="row.price"
                     class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-extrabold text-slate-700 mb-1">Urutan</label>
              <input type="number" min="0" :name="'tiers[domestic]['+i+'][sort_order]'" x-model="row.sort_order"
                     class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            </div>
          </div>

          <div class="flex justify-end">
            <button type="button" @click="remove(i)"
                    class="rounded-xl px-3 py-2 text-xs font-extrabold border border-red-200 bg-red-50 text-red-700 hover:bg-red-100">
              Hapus Baris
            </button>
          </div>
        </div>
      </template>

      <div class="flex items-center gap-2">
        <button type="button" @click="add()"
                class="rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50">
          + Tambah Baris
        </button>
        <div class="text-xs text-slate-500">Minimal 1 baris.</div>
      </div>
    </div>
  </div>
</div>

{{-- TIERS: FOREIGN (OPSIONAL) --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
          class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
          style="background:#0194F3;">
    <span>Harga Foreign Tourist / WNA (Opsional)</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5">
    <div x-data="{
          rows: @js($forTiers),
          add() { this.rows.push({type:'foreign', id:null, label_text:'', price:0, sort_order:0}); },
          remove(i){ this.rows.splice(i,1); }
        }" class="space-y-4">

      <template x-for="(row, i) in rows" :key="i">
        <div class="rounded-2xl border border-slate-200 p-4 space-y-3">
          <input type="hidden" :name="'tiers[foreign]['+i+'][type]'" x-model="row.type">
          <input type="hidden" :name="'tiers[foreign]['+i+'][id]'" x-model="row.id">

          <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-7">
              <label class="block text-xs font-extrabold text-slate-700 mb-1">Text (bebas)</label>
              <input type="text" :name="'tiers[foreign]['+i+'][label_text]'" x-model="row.label_text"
                     class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            </div>

            <div class="md:col-span-3">
              <label class="block text-xs font-extrabold text-slate-700 mb-1">Harga (angka)</label>
              <input type="number" min="0" :name="'tiers[foreign]['+i+'][price]'" x-model="row.price"
                     class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-extrabold text-slate-700 mb-1">Urutan</label>
              <input type="number" min="0" :name="'tiers[foreign]['+i+'][sort_order]'" x-model="row.sort_order"
                     class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            </div>
          </div>

          <div class="flex justify-end">
            <button type="button" @click="remove(i)"
                    class="rounded-xl px-3 py-2 text-xs font-extrabold border border-red-200 bg-red-50 text-red-700 hover:bg-red-100">
              Hapus Baris
            </button>
          </div>
        </div>
      </template>

      <div class="flex items-center gap-2">
        <button type="button" @click="add()"
                class="rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50">
          + Tambah Baris
        </button>
        <div class="text-xs text-slate-500">Bagian ini opsional.</div>
      </div>
    </div>
  </div>
</div>

{{-- KONTEN (Editor kayak Umrah) --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
          class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
          style="background:#0194F3;">
    <span>Konten (Deskripsi / Itinerary / Include / Exclude)</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5 space-y-4">
    {{-- NOTE: di project lo editor umrah pakai class "wysiwyg" --}}
    <div>
      <label class="block text-sm font-bold text-slate-800 mb-1">Deskripsi</label>
      <textarea name="long_description" class="wysiwyg">{{ old('long_description', $pkg->long_description ?? '') }}</textarea>
    </div>

    <div>
      <label class="block text-sm font-bold text-slate-800 mb-1">Itinerary</label>
      <textarea name="itinerary" class="wysiwyg">{{ old('itinerary', $pkg->itinerary ?? '') }}</textarea>
    </div>

    <div>
      <label class="block text-sm font-bold text-slate-800 mb-1">Include</label>
      <textarea name="include_text" class="wysiwyg">{{ old('include_text', $pkg->include_text ?? '') }}</textarea>
    </div>

    <div>
      <label class="block text-sm font-bold text-slate-800 mb-1">Exclude</label>
      <textarea name="exclude_text" class="wysiwyg">{{ old('exclude_text', $pkg->exclude_text ?? '') }}</textarea>
    </div>
  </div>
</div>

{{-- SEO (pola sama: seo_title, seo_description, seo_keywords) --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
          class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
          style="background:#0194F3;">
    <span>SEO</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5 space-y-4">
    <div>
      <label class="block text-sm font-bold text-slate-800 mb-1">SEO Title (opsional)</label>
      <input type="text" name="seo_title"
             value="{{ old('seo_title', $pkg->seo_title ?? '') }}"
             class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
    </div>

    <div>
      <label class="block text-sm font-bold text-slate-800 mb-1">SEO Description (opsional)</label>
      <textarea name="seo_description" rows="4"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">{{ old('seo_description', $pkg->seo_description ?? '') }}</textarea>
    </div>

    <div>
      <label class="block text-sm font-bold text-slate-800 mb-1">SEO Keywords (opsional)</label>
      <textarea name="seo_keywords" rows="3"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">{{ old('seo_keywords', $pkg->seo_keywords ?? '') }}</textarea>
    </div>
  </div>
</div>

<script>
function previewMiceThumb(input) {
    const wrap = document.getElementById('mice_thumb_new_wrap');
    const img  = document.getElementById('mice_thumb_new');
    const existing = document.getElementById('mice_thumb_existing');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.classList.remove('hidden'); if(existing) existing.style.opacity='0.4'; };
        reader.readAsDataURL(input.files[0]);
    } else { wrap.classList.add('hidden'); if(existing) existing.style.opacity='1'; }
}
function previewMiceGallery(input) {
    const wrap = document.getElementById('mice_gallery_new_wrap');
    const grid = document.getElementById('mice_gallery_new_grid');
    grid.innerHTML = '';
    if (input.files && input.files.length > 0) {
        wrap.classList.remove('hidden');
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const d = document.createElement('div');
                d.className = 'h-20 rounded-xl overflow-hidden border border-slate-200';
                d.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover">`;
                grid.appendChild(d);
            };
            reader.readAsDataURL(file);
        });
    } else { wrap.classList.add('hidden'); }
}
</script>
