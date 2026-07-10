@php
  $pkg = $package ?? null;
@endphp

{{-- INFORMASI DASAR --}}
<div x-data="{ open: true }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
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
            <div>
  <label class="block text-sm font-bold text-slate-800 mb-1">Label (opsional)</label>
  <input
    type="text"
    name="label"
    value="{{ old('label', $pkg?->label) }}"
    placeholder="Contoh: PROMO, DISKON, TERLARIS"
    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
  >
  <p class="mt-1 text-xs text-slate-500">Maks 30 karakter. Kosongkan jika tidak perlu.</p>
  @error('label') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-3">
  <label class="block text-sm font-bold text-slate-800 mb-1">Rating (1 - 5)</label>
  <input
    type="number"
    name="rating_value"
    min="1"
    max="5"
    value="{{ old('rating_value', $pkg?->rating_value ?? 5) }}"
    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
  >
  @error('rating_value') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-3">
  <label class="block text-sm font-bold text-slate-800 mb-1">Jumlah Rating</label>
  <input
    type="number"
    name="rating_count"
    min="0"
    value="{{ old('rating_count', $pkg?->rating_count ?? 0) }}"
    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
  >
  @error('rating_count') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
</div>



            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">Slug (URL)</label>
                <input type="text" name="slug"
                       value="{{ old('slug', $pkg->slug ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       required>
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-bold text-slate-800 mb-1">Durasi</label>
                <input type="text" name="duration_text"
                       value="{{ old('duration_text', $pkg->duration_text ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       placeholder="contoh: 3D2N">
            </div>
<div class="md:col-span-4">
    <label class="block text-sm font-bold text-slate-800 mb-1">Status Paket</label>
    <select name="is_active"
            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">

        <option value="1"
            {{ (int) old('is_active', $pkg->is_active ?? 1) === 1 ? 'selected' : '' }}>
            Aktif
        </option>

        <option value="0"
            {{ (int) old('is_active', $pkg->is_active ?? 1) === 0 ? 'selected' : '' }}>
            Nonaktif
        </option>

    </select>

    @error('is_active')
        <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
    @enderror
</div>

            <div class="md:col-span-4">
                <label class="block text-sm font-bold text-slate-800 mb-1">Destinasi</label>
                <input type="text" name="destination"
                       value="{{ old('destination', $pkg->destination ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-bold text-slate-800 mb-1">Kategori</label>
                <select name="category_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                        required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $pkg->category_id ?? '') == $cat->id ? 'selected':'' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4">
  <label class="block text-sm font-bold text-slate-800 mb-1">Sub Kategori</label>
  <select id="subcategorySelect" name="subcategory_id"
          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
    <option value="">-- (Opsional) Pilih Sub Kategori --</option>
  </select>
</div>

        </div>
    </div>
</div>

{{-- FOTO --}}
<div x-data="{ open: true }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>Foto Paket Wisata</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5 space-y-4">

        @if(!empty($pkg?->thumbnail_path))
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-sm font-extrabold text-slate-900">Thumbnail Saat Ini</div>
                <img src="{{ asset('storage/' . $pkg->thumbnail_path) }}"
                     class="mt-3 h-28 w-auto rounded-xl object-cover border border-slate-200">
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">Upload Thumbnail</label>
                <input type="file" name="thumbnail" accept="image/*"
                       id="tour_thumbnail_input"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       onchange="previewTourThumb(this)">
                <div class="text-xs text-slate-500 mt-1">PNG/JPG/WEBP disarankan.</div>
                {{-- Live preview thumbnail baru --}}
                <div id="tour_thumb_new_wrap" class="hidden mt-3 rounded-2xl border border-blue-200 bg-blue-50 p-3">
                    <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Thumbnail Baru</div>
                    <div class="h-28 rounded-xl overflow-hidden border border-slate-200">
                        <img id="tour_thumb_new" src="" class="h-full w-full object-cover" alt="">
                    </div>
                </div>
            </div>

            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">Tambah Gallery (multi upload)</label>
                <input type="file" name="gallery[]" accept="image/*" multiple
                       id="tour_gallery_input"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       onchange="previewTourGallery(this)">
                <div class="text-xs text-slate-500 mt-1">Boleh lebih dari 1 foto.</div>
                {{-- Live preview gallery baru --}}
                <div id="tour_gallery_new_wrap" class="hidden mt-3">
                    <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Gallery Baru</div>
                    <div id="tour_gallery_new_grid" class="grid grid-cols-3 gap-2"></div>
                </div>
            </div>
        </div>
{{-- SEO --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>SEO Paket Tour</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">SEO Title</label>
                <input type="text"
                       name="seo_title"
                       value="{{ old('seo_title', $pkg->seo_title ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       placeholder="Judul meta (opsional)">
            </div>

            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">SEO Keywords</label>
                <input type="text"
                       name="seo_keywords"
                       value="{{ old('seo_keywords', $pkg->seo_keywords ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       placeholder="contoh: paket wisata, bali, tour murah">
                <div class="mt-1 text-xs text-slate-500">Pisahkan dengan koma.</div>
            </div>

            <div class="md:col-span-12">
                <label class="block text-sm font-bold text-slate-800 mb-1">SEO Description</label>
                <textarea name="seo_description"
                          rows="3"
                          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                          placeholder="Deskripsi meta (opsional)">{{ old('seo_description', $pkg->seo_description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

        {{-- Gallery existing (edit only) --}}
        @if($pkg && method_exists($pkg, 'photos') && $pkg->photos->count())
            <div class="pt-2">
                <div class="text-sm font-extrabold text-slate-900 mb-3">Galeri Foto</div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($pkg->photos as $photo)
                        <div class="rounded-2xl border border-slate-200 bg-white p-3">
                            <img src="{{ asset('storage/' . $photo->file_path) }}"
                                 class="h-24 w-full object-cover rounded-xl border border-slate-200">

                            <div class="mt-3">
    <button type="button"
            class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
            style="background:#ef4444"
            onmouseover="this.style.background='#dc2626'"
            onmouseout="this.style.background='#ef4444'"
            onclick="window.__bwDeletePhoto('{{ route('admin.tour-packages.delete-photo', $photo->id) }}')">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
        Hapus
    </button>
</div>

                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- KONTEN (Deskripsi / Itinerary / Include / Exclude) --}}

@php
    // Prefill Itinerary editor dari data DB (tour_itineraries) -> jadi <p>per baris</p>
    $itineraryHtml = old('itinerary_text');
    if ($itineraryHtml === null) {
        $lines = ($pkg?->itineraries ?? collect())
            ->sortBy('sort_order')
            ->pluck('title')
            ->filter()
            ->values();

        $itineraryHtml = $lines->map(fn($t) => '<p>' . e($t) . '</p>')->implode('');
    }

    // Prefill Include editor dari JSON includes
    $includeHtml = old('include_text');
    if ($includeHtml === null) {
        $lines = collect($pkg->includes ?? [])->filter()->values();
        $includeHtml = $lines->map(fn($t) => '<p>' . e($t) . '</p>')->implode('');
    }

    // Prefill Exclude editor dari JSON excludes
    $excludeHtml = old('exclude_text');
    if ($excludeHtml === null) {
        $lines = collect($pkg->excludes ?? [])->filter()->values();
        $excludeHtml = $lines->map(fn($t) => '<p>' . e($t) . '</p>')->implode('');
    }
@endphp

<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button" @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>Konten (Deskripsi / Itinerary / Include / Exclude)</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5 space-y-4">
        <div>
            <label class="block text-sm font-bold text-slate-800 mb-1">Deskripsi</label>
            <textarea name="long_description"
                class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                rows="10">{{ old('long_description', $pkg->long_description ?? '') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-800 mb-1">Itinerary</label>
            <textarea name="itinerary_text"
                class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                rows="10">{!! $itineraryHtml !!}</textarea>
            <p class="mt-1 text-xs text-slate-500">Satu baris = satu item. Enter = item baru.</p>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-800 mb-1">Include</label>
            <textarea name="include_text"
                class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                rows="8">{!! $includeHtml !!}</textarea>
            <p class="mt-1 text-xs text-slate-500">Satu baris = satu item. Enter = item baru.</p>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-800 mb-1">Exclude</label>
            <textarea name="exclude_text"
                class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                rows="8">{!! $excludeHtml !!}</textarea>
            <p class="mt-1 text-xs text-slate-500">Satu baris = satu item. Enter = item baru.</p>
        </div>
    </div>
</div>


{{-- TIERS --}}
@include('admin.tour-packages._tier_section', [
    'type' => 'domestic',
    'label' => 'Harga Domestik',
    'package' => $pkg
])

@include('admin.tour-packages._tier_section', [
    'type' => 'international',
    'label' => 'Harga WNA',
    'package' => $pkg
])

{{-- FLIGHT INFO --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>Pengaturan Tiket Pesawat</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5">
        <label class="block text-sm font-bold text-slate-800 mb-1">Paket Termasuk Tiket Pesawat?</label>
        <select name="flight_info"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            <option value="not_included"
                {{ old('flight_info', $pkg->flight_info ?? '') === 'not_included' ? 'selected':'' }}>
                Tidak termasuk tiket pesawat
            </option>
            <option value="included"
                {{ old('flight_info', $pkg->flight_info ?? '') === 'included' ? 'selected':'' }}>
                Termasuk tiket pesawat
            </option>
        </select>
    </div>
</div>
@push('scripts')
<script>
(function(){
  const cat = document.querySelector('select[name="category_id"]');
  const sub = document.getElementById('subcategorySelect');
  const oldSub = "{{ old('subcategory_id', $pkg->subcategory_id ?? '') }}";

  async function loadSubs() {
    const catId = cat.value;
    sub.innerHTML = '<option value="">-- (Opsional) Pilih Sub Kategori --</option>';
    if (!catId) return;

    const url = new URL("{{ route('admin.categories.subcategories', ['category' => 0]) }}".replace('/0/', '/' + catId + '/'), window.location.origin);

    const res = await fetch(url.toString(), { headers: {'X-Requested-With': 'XMLHttpRequest'}});
    if (!res.ok) return;

    const data = await res.json();
    const items = data.items || [];

    for (const it of items) {
      const opt = document.createElement('option');
      opt.value = it.id;
      opt.textContent = it.name;
      if (oldSub && String(oldSub) === String(it.id)) opt.selected = true;
      sub.appendChild(opt);
    }
  }

  cat.addEventListener('change', () => {

    sub.value = '';
    loadSubs();
  });

  loadSubs();
})();

// Live preview thumbnail
function previewTourThumb(input) {
    const wrap = document.getElementById('tour_thumb_new_wrap');
    const img  = document.getElementById('tour_thumb_new');
    const existing = document.querySelector('#tour-thumb-existing img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            wrap.classList.remove('hidden');
            if (existing) existing.style.opacity = '0.4';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        wrap.classList.add('hidden');
        if (existing) existing.style.opacity = '1';
    }
}

// Live preview gallery multi
function previewTourGallery(input) {
    const wrap = document.getElementById('tour_gallery_new_wrap');
    const grid = document.getElementById('tour_gallery_new_grid');
    grid.innerHTML = '';
    if (input.files && input.files.length > 0) {
        wrap.classList.remove('hidden');
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'h-20 rounded-xl overflow-hidden border border-slate-200';
                div.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover">`;
                grid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    } else {
        wrap.classList.add('hidden');
    }
}
</script>
@endpush

