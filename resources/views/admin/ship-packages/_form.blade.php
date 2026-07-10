@csrf

@php
  $tiersOld = old('tiers');
  $tiersDb = ($package?->tiers ?? collect())->map(fn($t) => [
    'type' => $t->type,
    'label_text' => $t->label_text,
    'price' => (int)$t->price,
  ])->toArray();

  $tiers = $tiersOld ?? $tiersDb;

  if (!$tiers || count($tiers) === 0) {
    $tiers = [
      ['type'=>'weekday','label_text'=>'','price'=>0],
      ['type'=>'weekend','label_text'=>'','price'=>0],
    ];
  }

  $features = old('features', $package->features ?? []);
@endphp

<div class="space-y-6">

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-extrabold text-slate-800 mb-1">Title</label>
      <input type="text" name="title"
             value="{{ old('title', $package->title ?? '') }}"
             class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
    </div>

    <div>
      <label class="block text-sm font-extrabold text-slate-800 mb-1">Slug (URL)</label>
      <input type="text" name="slug"
             value="{{ old('slug', $package->slug ?? '') }}"
             class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
    </div>

    <div>
  <label class="block text-sm font-extrabold text-slate-800 mb-1">Label (opsional)</label>
  <input type="text" name="label"
         value="{{ old('label', $package->label ?? '') }}"
         placeholder="Contoh: PROMO, DISKON, TERLARIS"
         class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
  <p class="mt-1 text-xs text-slate-500">Maks 30 karakter. Kosongkan jika tidak perlu.</p>
  @error('label') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div>
  <label class="block text-sm font-extrabold text-slate-800 mb-1">Rating (1 - 5)</label>
  <input
    type="number"
    name="rating_value"
    min="1"
    max="5"
    step="0.1"
    value="{{ old('rating_value', $package->rating_value ?? 5) }}"
    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
  >
  @error('rating_value') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div>
  <label class="block text-sm font-extrabold text-slate-800 mb-1">Jumlah Rating</label>
  <input
    type="number"
    name="rating_count"
    min="0"
    value="{{ old('rating_count', $package->rating_count ?? 0) }}"
    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
  >
  @error('rating_count') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div>
  <label class="block text-sm font-extrabold text-slate-800 mb-1">Kategori</label>
  <select name="category_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
    <option value="">-</option>
    @foreach($categories as $c)
      <option value="{{ $c->id }}" {{ (string)old('category_id', $package->category_id ?? '') === (string)$c->id ? 'selected' : '' }}>
        {{ $c->name }}
      </option>
    @endforeach
  </select>
  @error('category_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
</div>


    <div>
      <label class="block text-sm font-extrabold text-slate-800 mb-1">Kategori</label>
      <select name="category_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        <option value="">- Pilih Kategori -</option>
        @foreach(($categories ?? []) as $c)
          <option value="{{ $c->id }}"
            {{ (string)old('category_id', $package->category_id ?? '') === (string)$c->id ? 'selected' : '' }}>
            {{ $c->name }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  {{-- Thumbnail --}}
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
    <div class="lg:col-span-7">
      <label class="block text-sm font-extrabold text-slate-800 mb-1">Thumbnail</label>
      <input type="file" name="thumbnail" id="ship_thumb_input"
             class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" accept="image/*"
             onchange="previewShipThumb(this)">
      <div class="text-xs text-slate-500 mt-1">JPG/PNG/WEBP disarankan.</div>
      <div id="ship_thumb_new_wrap" class="hidden mt-3 rounded-2xl border border-blue-200 bg-blue-50 p-3">
        <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Thumbnail Baru</div>
        <div class="h-28 rounded-xl overflow-hidden border border-slate-200">
          <img id="ship_thumb_new" src="" class="h-full w-full object-cover" alt="">
        </div>
      </div>
    </div>

    <div class="lg:col-span-5">
      @if(!empty($package?->thumbnail_path))
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
          <div class="text-xs font-extrabold text-slate-600 mb-2">Current Thumbnail</div>
          <div class="h-28 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
            <img id="ship_thumb_existing" src="{{ asset('storage/' . $package->thumbnail_path) }}" class="h-full w-full object-cover" alt="Thumbnail">
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- Status --}}
  <div>
    <label class="block text-sm font-extrabold text-slate-800 mb-1">Status</label>
    <select name="is_active" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
      <option value="1" {{ old('is_active', $package->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
      <option value="0" {{ old('is_active', $package->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
    </select>
  </div>

  <div class="h-px bg-slate-200"></div>

  {{-- Harga Weekday/Weekend --}}
  <div x-data="shipPricing()" x-init="init()" class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center justify-between gap-3">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Harga Sewa Kapal</div>
        <div class="text-xs text-slate-500 mt-0.5">Tab weekday/weekend. Input: teks bebas + harga.</div>
      </div>
      <button type="button"
              class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50"
              @click="addRow(active)">
        <i data-lucide="plus" class="w-4 h-4" style="color:#0194F3;"></i>
        Tambah Baris
      </button>
    </div>

    <div class="mt-4 flex bg-slate-100 rounded-full p-1 text-sm font-semibold">
      <button type="button"
              class="flex-1 py-2 rounded-full transition"
              :class="active==='weekday' ? 'bg-[#0194F3] text-white shadow-sm' : 'text-slate-600'"
              @click="active='weekday'">
        Weekday
      </button>

      <button type="button"
              class="flex-1 py-2 rounded-full transition"
              :class="active==='weekend' ? 'bg-[#0194F3] text-white shadow-sm' : 'text-slate-600'"
              @click="active='weekend'">
        Weekend
      </button>
    </div>

    {{-- FIX UTAMA: render SEMUA rows (biar submit kirim weekday+weekend),
         tapi tampilkan sesuai tab via x-show --}}
    <div class="mt-4 space-y-2">
      <template x-for="row in rows" :key="row.__key">
        <div class="rounded-2xl border border-slate-200 bg-white p-3"
             x-show="row.type === active"
             style="display:none;">
          <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <div class="md:col-span-7">
              <label class="block text-xs font-extrabold text-slate-600 mb-1">Teks</label>
              <input type="text"
                     class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                     placeholder="Contoh: Kapal A (max 10 orang) / 4 jam"
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

            <input type="hidden" :name="`tiers[${row.__idx}][type]`" :value="row.type">
          </div>
        </div>
      </template>
    </div>
  </div>

  {{-- Features --}}
  <div>
    <div class="flex items-center justify-between gap-3">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Package Features</div>
        <div class="text-xs text-slate-500 mt-0.5">Tambahkan fitur & tandai tersedia / tidak.</div>
      </div>

      <button type="button"
              id="add-feature"
              class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
        <i data-lucide="plus" class="w-4 h-4" style="color:#0194F3;"></i>
        Add Feature
      </button>
    </div>

    <div id="feature-list" class="mt-4 space-y-2">
      @foreach ($features as $i => $feat)
        <div class="feature-row rounded-2xl border border-slate-200 bg-white p-3">
          <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <div class="md:col-span-7">
              <label class="block text-xs font-extrabold text-slate-600 mb-1">Feature name</label>
              <input type="text"
                     name="features[{{ $i }}][name]"
                     value="{{ $feat['name'] ?? '' }}"
                     class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                     required>
            </div>

            <div class="md:col-span-3">
              <label class="block text-xs font-extrabold text-slate-600 mb-1">Available</label>
              <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                <input type="checkbox"
                       class="rounded border-slate-300"
                       name="features[{{ $i }}][available]"
                       value="1"
                       {{ !empty($feat['available']) ? 'checked' : '' }}>
                <span class="font-bold text-slate-700">Yes</span>
              </label>
            </div>

            <div class="md:col-span-2 md:text-right">
              <button type="button"
                      class="remove-feature inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition w-full md:w-auto"
                      style="background:#ef4444"
                      onmouseover="this.style.background='#dc2626'"
                      onmouseout="this.style.background='#ef4444'">
                <i data-lucide="x" class="w-4 h-4"></i>
                Remove
              </button>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Deskripsi --}}
  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="text-sm font-extrabold text-slate-900 mb-3">Deskripsi Paket Sewa Kapal</div>
    <textarea name="long_description"
      class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
      rows="12">{{ old('long_description', $package->long_description ?? '') }}</textarea>
  </div>

  {{-- SEO --}}
  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="text-sm font-extrabold text-slate-900 mb-3">SEO (Opsional)</div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
      <div class="md:col-span-6">
        <label class="block text-sm font-bold text-slate-800 mb-1">SEO Title</label>
        <input type="text" name="seo_title"
          value="{{ old('seo_title', $package->seo_title ?? '') }}"
          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
      </div>

      <div class="md:col-span-6">
        <label class="block text-sm font-bold text-slate-800 mb-1">SEO Keywords</label>
        <input type="text" name="seo_keywords"
          value="{{ old('seo_keywords', $package->seo_keywords ?? '') }}"
          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
          placeholder="Pisahkan dengan koma">
      </div>

      <div class="md:col-span-12">
        <label class="block text-sm font-bold text-slate-800 mb-1">SEO Description</label>
        <textarea name="seo_description" rows="3"
          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">{{ old('seo_description', $package->seo_description ?? '') }}</textarea>
      </div>
    </div>
  </div>

  {{-- Actions --}}
  <div class="flex items-center justify-end gap-2 pt-2">
    <a href="{{ route('admin.ship-packages.index') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
      Back
    </a>

    <button type="submit"
            class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-extrabold text-white transition"
            style="background:#0194F3"
            onmouseover="this.style.background='#0186DB'"
            onmouseout="this.style.background='#0194F3'">
      <i data-lucide="save" class="w-4 h-4"></i>
      {{ $buttonText ?? 'Save' }}
    </button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // features dynamic (copy from rentcar)
  const list = document.getElementById('feature-list');
  const addBtn = document.getElementById('add-feature');
  let index = {{ count($features) }};

  addBtn?.addEventListener('click', function () {
    const row = document.createElement('div');
    row.className = 'feature-row rounded-2xl border border-slate-200 bg-white p-3';
    row.innerHTML = `
      <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
        <div class="md:col-span-7">
          <label class="block text-xs font-extrabold text-slate-600 mb-1">Feature name</label>
          <input type="text" name="features[${index}][name]"
                 class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" required>
        </div>
        <div class="md:col-span-3">
          <label class="block text-xs font-extrabold text-slate-600 mb-1">Available</label>
          <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            <input type="checkbox" class="rounded border-slate-300"
                   name="features[${index}][available]" value="1">
            <span class="font-bold text-slate-700">Yes</span>
          </label>
        </div>
        <div class="md:col-span-2 md:text-right">
          <button type="button"
                  class="remove-feature inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition w-full md:w-auto"
                  style="background:#ef4444">
            ✕ Remove
          </button>
        </div>
      </div>
    `;
    list.appendChild(row);
    index++;
  });

  document.addEventListener('click', function(e){
    if (e.target.closest('.remove-feature')) {
      const row = e.target.closest('.feature-row');
      row?.remove();
    }
  });
});

function shipPricing() {
  let seed = @json($tiers);
  seed = Array.isArray(seed) ? seed : [];

  if (seed.length === 0) {
    seed = [
      { type: 'weekday', label_text: '', price: 0 },
      { type: 'weekend', label_text: '', price: 0 }
    ];
  }

  const makeKey = (i) => {
    try { return crypto.randomUUID(); }
    catch (e) { return (Date.now() + '-' + i + '-' + Math.random()); }
  };

  const normalizeType = (t) => (t === 'weekend' ? 'weekend' : 'weekday');

  return {
    active: 'weekday',
    rows: [],

    init() {
      this.rows = seed.map((r, i) => ({
        __key: makeKey(i),
        __idx: i,
        type: normalizeType(r.type),
        label_text: r.label_text || '',
        price: Number(r.price || 0),
      }));
      this.reindex();
    },

    reindex() {
      this.rows.forEach((r, i) => { r.__idx = i; });
    },

    addRow(t) {
      this.rows.push({
        __key: makeKey(this.rows.length),
        __idx: this.rows.length,
        type: normalizeType(t),
        label_text: '',
        price: 0
      });
      this.reindex();
    },

    removeRow(key) {
      this.rows = this.rows.filter(r => r.__key !== key);
      this.reindex();
    }
  };
}

function previewShipThumb(input) {
    const wrap = document.getElementById('ship_thumb_new_wrap');
    const img  = document.getElementById('ship_thumb_new');
    const existing = document.getElementById('ship_thumb_existing');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.classList.remove('hidden'); if(existing) existing.style.opacity='0.4'; };
        reader.readAsDataURL(input.files[0]);
    } else { wrap.classList.add('hidden'); if(existing) existing.style.opacity='1'; }
}
</script>
