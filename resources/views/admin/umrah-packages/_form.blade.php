@php
  $pkg = $package ?? null;

  $tiersOld = old('tiers');
  $tiersDb = ($pkg?->tiers ?? collect())
    ->map(fn($t) => [
      'id' => $t->id,
      'label_text' => $t->label_text,
      'price' => (int) $t->price,
      'sort_order' => (int) ($t->sort_order ?? 0),
    ])->values()->toArray();


  $tiers = $tiersOld ?? $tiersDb;

  if (!$tiers || count($tiers) === 0) {
    $tiers = [
      ['type'=>'domestic','label_text'=>'','price'=>0],
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

      <div class="md:col-span-6">
        <label class="block text-sm font-bold text-slate-800 mb-1">Label (opsional)</label>
        <input type="text" name="label"
               value="{{ old('label', $pkg?->label) }}"
               placeholder="Contoh: PROMO, DISKON, TERLARIS"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        <p class="mt-1 text-xs text-slate-500">Maks 30 karakter. Kosongkan jika tidak perlu.</p>
      </div>

      <div class="md:col-span-3">
        <label class="block text-sm font-bold text-slate-800 mb-1">Rating (1 - 5)</label>
        <input type="number" name="rating_value" min="1" max="5"
               value="{{ old('rating_value', $pkg?->rating_value ?? 5) }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
      </div>

      <div class="md:col-span-3">
        <label class="block text-sm font-bold text-slate-800 mb-1">Jumlah Rating</label>
        <input type="number" name="rating_count" min="0"
               value="{{ old('rating_count', $pkg?->rating_count ?? 0) }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
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
               placeholder="contoh: 9H / 12H / 10D9N">
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
          @foreach(($categories ?? []) as $cat)
            <option value="{{ $cat->id }}"
              {{ old('category_id', $pkg->category_id ?? '') == $cat->id ? 'selected':'' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-12">
        <label class="block text-sm font-bold text-slate-800 mb-1">Status</label>
        <select name="is_active" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
          <option value="1" {{ old('is_active', $pkg->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
          <option value="0" {{ old('is_active', $pkg->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>

    </div>
  </div>
</div>

{{-- FOTO --}}
<div x-data="{ open: true }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
    class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
    style="background:#0194F3;">
    <span>Foto Paket Umrah</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5 space-y-4">

    @if(!empty($pkg?->thumbnail_path))
      <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <div class="text-sm font-extrabold text-slate-900">Thumbnail Saat Ini</div>
        <img id="umrah_thumb_existing" src="{{ asset('storage/' . $pkg->thumbnail_path) }}"
             class="mt-3 h-28 w-auto rounded-xl object-cover border border-slate-200">
      </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
      <div class="md:col-span-6">
        <label class="block text-sm font-bold text-slate-800 mb-1">Upload Thumbnail</label>
        <input type="file" name="thumbnail" accept="image/*" id="umrah_thumb_input"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               onchange="previewUmrahThumb(this)">
        <div class="text-xs text-slate-500 mt-1">PNG/JPG/WEBP disarankan.</div>
        <div id="umrah_thumb_new_wrap" class="hidden mt-3 rounded-2xl border border-blue-200 bg-blue-50 p-3">
          <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Thumbnail Baru</div>
          <div class="h-28 rounded-xl overflow-hidden border border-slate-200">
            <img id="umrah_thumb_new" src="" class="h-full w-full object-cover" alt="">
          </div>
        </div>
      </div>

      <div class="md:col-span-6">
      <label class="block text-sm font-bold text-slate-800 mb-1">Tambah Gallery</label>

{{-- Container input dinamis --}}
<div id="umrah-gallery-inputs" class="space-y-2">
  {{-- input pertama --}}
  <input
    type="file"
    name="gallery[]"
    accept="image/*"
    multiple
    class="umrah-gallery-input w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
  >
</div>

<div class="text-xs text-slate-500 mt-1">
  Pilih foto bisa berkali-kali. Kalau pilih lagi, file sebelumnya tidak hilang—akan ditambah ke daftar.
</div>

{{-- Preview daftar file baru yang dipilih --}}
<div id="umrah-gallery-selected" class="mt-3 space-y-2"></div>


      </div>
    </div>

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
                        onclick="window.__bwDeletePhoto('{{ route('admin.umrah-packages.delete-photo', $photo->id) }}')">
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

{{-- SEO --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
    class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
    style="background:#0194F3;">
    <span>SEO Paket Umrah</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
      <div class="md:col-span-6">
        <label class="block text-sm font-bold text-slate-800 mb-1">SEO Title</label>
        <input type="text" name="seo_title"
               value="{{ old('seo_title', $pkg->seo_title ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               placeholder="Judul meta (opsional)">
      </div>

      <div class="md:col-span-6">
        <label class="block text-sm font-bold text-slate-800 mb-1">SEO Keywords</label>
        <input type="text" name="seo_keywords"
               value="{{ old('seo_keywords', $pkg->seo_keywords ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               placeholder="contoh: paket umrah, umrah ramadhan, umrah murah">
        <div class="mt-1 text-xs text-slate-500">Pisahkan dengan koma.</div>
      </div>

      <div class="md:col-span-12">
        <label class="block text-sm font-bold text-slate-800 mb-1">SEO Description</label>
        <textarea name="seo_description" rows="3"
                  class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                  placeholder="Deskripsi meta (opsional)">{{ old('seo_description', $pkg->seo_description ?? '') }}</textarea>
      </div>
    </div>
  </div>
</div>

{{-- DESKRIPSI --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
    class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
    style="background:#0194F3;">
    <span>Deskripsi Paket</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5">
    <label class="block text-sm font-bold text-slate-800 mb-1">Deskripsi Lengkap</label>
    <textarea name="long_description"
              class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
              rows="12">{{ old('long_description', $pkg->long_description ?? '') }}</textarea>
  </div>
</div>

{{-- ITINERARY (UMRAH: wysiwyg, bukan add satu2) --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
    class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
    style="background:#0194F3;">
    <span>Itinerary Perjalanan</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5">
    <label class="block text-sm font-bold text-slate-800 mb-1">Itinerary</label>
    <textarea name="itinerary"
              class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
              rows="10">{{ old('itinerary', $pkg->itinerary ?? '') }}</textarea>
  </div>
</div>

{{-- INCLUDE & EXCLUDE (UMRAH: wysiwyg) --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button" @click="open=!open"
    class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
    style="background:#0194F3;">
    <span>Include & Exclude</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5 space-y-4">
    <div>
      <label class="block text-sm font-bold text-slate-800 mb-1">Include</label>
      <textarea name="include_text"
                class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                rows="8">{{ old('include_text', $pkg->include_text ?? '') }}</textarea>
    </div>

    <div>
      <label class="block text-sm font-bold text-slate-800 mb-1">Exclude</label>
      <textarea name="exclude_text"
                class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                rows="8">{{ old('exclude_text', $pkg->exclude_text ?? '') }}</textarea>
    </div>
  </div>
</div>

{{-- HARGA DOMESTIK (UMRAH: label bebas + harga, tanpa min/max, tanpa tab) --}}
<div x-data="umrahPricing()" x-init="init()" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <button type="button"
    @click="open = !open"
    class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
    style="background:#0194F3;"
  >
    <span>Harga Domestik</span>
    <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
  </button>

  <div x-show="open" x-cloak class="p-5">
    <div class="flex items-center justify-between gap-3">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Harga Paket Umrah</div>
        <div class="text-xs text-slate-500 mt-0.5">Input: teks label bebas + harga.</div>
      </div>

      <button type="button"
              class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50"
              @click="addRow()">
        <i data-lucide="plus" class="w-4 h-4" style="color:#0194F3;"></i>
        Tambah Baris
      </button>
    </div>

    <div class="mt-4 space-y-2">
      <template x-for="row in rows" :key="row.__key">
        <div class="rounded-2xl border border-slate-200 bg-white p-3">
          <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <div class="md:col-span-7">
              <label class="block text-xs font-extrabold text-slate-600 mb-1">Label / Teks</label>
              <input type="text"
                     class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                     placeholder="Contoh: QUAD / TRIPLE / Double / Promo Early Bird"
                     :name="`tiers[${row.__idx}][label_text]`"
                     x-model="row.label_text"
                     required>
            </div>

            <div class="md:col-span-4">
              <label class="block text-xs font-extrabold text-slate-600 mb-1">Harga</label>
              <input type="number"
                     class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                     :name="`tiers[${row.__idx}][price]`"
                     x-model.number="row.price"
                     min="0"
                     required>
            </div>

            <div class="md:col-span-1 md:text-right">
              <button type="button"
                      class="inline-flex w-full md:w-auto items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white"
                      style="background:#ef4444"
                      onmouseover="this.style.background='#dc2626'"
                      onmouseout="this.style.background='#ef4444'"
                      @click="removeRow(row.__key)">
                <i data-lucide="x" class="w-4 h-4"></i>
              </button>
            </div>

            <input type="hidden" :name="`tiers[${row.__idx}][type]`" value="domestic">
          </div>
        </div>
      </template>
    </div>
  </div>
</div>

<script>
function umrahPricing() {
  return {
    open: false,
    rows: [],
    init() {
      const initial = @json(array_values($tiers));
      this.rows = (initial || []).map((r, i) => ({
        __key: (r.id ? `db_${r.id}` : `new_${i}_${Math.random().toString(16).slice(2)}`),
        __idx: i,
        label_text: r.label_text || '',
        price: Number(r.price || 0),
      }));

      if (this.rows.length === 0) {
        this.rows = [{
          __key: `new_0_${Math.random().toString(16).slice(2)}`,
          __idx: 0,
          label_text: '',
          price: 0,
        }];
      }

      this.reindex();
    },
    reindex() {
      this.rows.forEach((r, idx) => r.__idx = idx);
    },
    addRow() {
      this.rows.push({
        __key: `new_${this.rows.length}_${Math.random().toString(16).slice(2)}`,
        __idx: this.rows.length,
        label_text: '',
        price: 0,
      });
      this.reindex();
    },
    removeRow(key) {
      this.rows = this.rows.filter(r => r.__key !== key);
      if (this.rows.length === 0) this.addRow();
      this.reindex();
    }
  }
}

function previewUmrahThumb(input) {
    const wrap = document.getElementById('umrah_thumb_new_wrap');
    const img  = document.getElementById('umrah_thumb_new');
    const existing = document.getElementById('umrah_thumb_existing');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.classList.remove('hidden'); if(existing) existing.style.opacity='0.4'; };
        reader.readAsDataURL(input.files[0]);
    } else { wrap.classList.add('hidden'); if(existing) existing.style.opacity='1'; }
}
</script>
@push('scripts')
<script>
(function () {
  const inputsWrap = document.getElementById('umrah-gallery-inputs');
  const selectedWrap = document.getElementById('umrah-gallery-selected');
  if (!inputsWrap || !selectedWrap) return;

  let idx = 0;

  // bikin satu item preview + tombol remove
  function addPreviewItem(inputEl, file, fileIndex) {
    const row = document.createElement('div');
    row.className = 'flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2';

    const left = document.createElement('div');
    left.className = 'min-w-0';

    const name = document.createElement('div');
    name.className = 'text-sm font-semibold text-slate-800 truncate';
    name.textContent = file.name;

    const meta = document.createElement('div');
    meta.className = 'text-xs text-slate-500';
    meta.textContent = `${Math.round(file.size / 1024)} KB`;

    left.appendChild(name);
    left.appendChild(meta);

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'shrink-0 rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-bold text-slate-700 hover:bg-slate-50';
    btn.textContent = 'Remove';

    // IMPORTANT:
    // FileList nggak bisa diedit. Jadi cara remove yang paling aman:
    // - kalau input ini cuma berisi 1 file -> hapus inputnya dari DOM
    // - kalau input ini berisi banyak file -> kita kosongkan seluruh inputnya (semua file dari selection itu hilang)
    btn.addEventListener('click', () => {
      const files = inputEl.files ? Array.from(inputEl.files) : [];
      if (files.length <= 1) {
        // hapus input untuk file ini
        inputEl.remove();
      } else {
        // selection input ini banyak: paling aman kosongkan semuanya
        inputEl.value = '';
      }
      row.remove();
    });

    row.appendChild(left);
    row.appendChild(btn);
    selectedWrap.appendChild(row);
  }

  function lockAndSpawnNewInput(oldInput) {
    // Kunci input lama biar user nggak bisa replace selection yang sudah "ditambah"
    oldInput.classList.add('hidden');
    oldInput.setAttribute('data-locked', '1');

    // Spawn input baru (yang fresh) untuk selection berikutnya
    const newInput = document.createElement('input');
    newInput.type = 'file';
    newInput.name = 'gallery[]';
    newInput.accept = 'image/*';
    newInput.multiple = true;
    newInput.className = 'umrah-gallery-input w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm';

    inputsWrap.appendChild(newInput);

    // pas newInput berubah, ulangi proses
    newInput.addEventListener('change', onChange);
  }

  function onChange(e) {
    const inputEl = e.target;
    const files = inputEl.files ? Array.from(inputEl.files) : [];
    if (!files.length) return;

    // bikin preview untuk semua file yang dipilih di selection ini
    files.forEach((f, i) => addPreviewItem(inputEl, f, i));

    // habis pilih: kunci input ini dan bikin input baru
    lockAndSpawnNewInput(inputEl);
  }

  // bind ke input pertama yang udah ada
  const first = inputsWrap.querySelector('input.umrah-gallery-input');
  if (first) first.addEventListener('change', onChange);
})();
</script>
@endpush

