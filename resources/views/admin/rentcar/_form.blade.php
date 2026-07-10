@csrf

<div class="space-y-6">

    {{-- Basic --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Title</label>
            <input type="text"
                name="title"
                value="{{ old('title', $package->title ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                required>
        </div>
        <div>
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Label (opsional)</label>
            <input
                type="text"
                name="label"
                value="{{ old('label', $package->label ?? '') }}"
                placeholder="Contoh: PROMO, DISKON, TERLARIS"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            <p class="mt-1 text-xs text-slate-500">Maks 30 karakter. Kosongkan jika tidak perlu.</p>
            @error('label') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Kategori</label>
            <select name="category_id"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                <option value="">- Pilih Kategori -</option>
                @foreach(($categories ?? []) as $c)
                <option value="{{ $c->id }}"
                    {{ (string)old('category_id', $package->category_id ?? '') === (string)$c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Harga 12 Jam</label>
            <input type="number"
                name="price_per_12_hours"
                step="0.01"
                value="{{ old('price_per_12_hours', $package->price_per_12_hours ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                required>
            <div class="text-xs text-slate-500 mt-1">Contoh: 350000</div>
        </div>

        <div>
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Harga 24 Jam</label>
            <input type="number"
                name="price_per_24_hours"
                step="0.01"
                value="{{ old('price_per_24_hours', $package->price_per_24_hours ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                required>
            <div class="text-xs text-slate-500 mt-1">Contoh: 700000</div>
        </div>
    </div>

    {{-- Thumbnail --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        <div class="lg:col-span-7">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Thumbnail</label>
            <input type="file"
                name="thumbnail"
                id="rentcar_thumb_input"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                accept="image/*"
                onchange="previewRentcarThumb(this)">
            <div class="text-xs text-slate-500 mt-1">JPG/PNG/WEBP disarankan.</div>
            {{-- Live preview --}}
            <div id="rentcar_thumb_new_wrap" class="hidden mt-3 rounded-2xl border border-blue-200 bg-blue-50 p-3">
                <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Foto Baru</div>
                <div class="h-28 rounded-xl overflow-hidden border border-slate-200">
                    <img id="rentcar_thumb_new" src="" class="h-full w-full object-cover" alt="">
                </div>
            </div>
        </div>

        <div class="lg:col-span-5">
            @isset($package->thumbnail_path)
            <div id="rentcar-thumb-existing" class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                <div class="text-xs font-extrabold text-slate-600 mb-2">Current Thumbnail</div>
                <div class="h-28 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                    <img src="{{ asset('storage/' . $package->thumbnail_path) }}"
                        id="rentcar_thumb_existing_img"
                        class="h-full w-full object-cover"
                        alt="Thumbnail">
                </div>
            </div>
            @endisset
        </div>
    </div>

    <script>
    function previewRentcarThumb(input) {
        const wrap = document.getElementById('rentcar_thumb_new_wrap');
        const img  = document.getElementById('rentcar_thumb_new');
        const existing = document.getElementById('rentcar_thumb_existing_img');
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
    </script>


    {{-- Status --}}
    <div>
        <label class="block text-sm font-extrabold text-slate-800 mb-1">Status</label>
        <select name="is_active"
            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            <option value="1" {{ old('is_active', $package->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active', $package->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    {{-- Divider --}}
    <div class="h-px bg-slate-200"></div>

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

        @php
        $features = old('features', $package->features ?? []);
        @endphp

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
                            placeholder="Contoh: Free Driver"
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
        <div class="text-sm font-extrabold text-slate-900 mb-3">Deskripsi Paket Rental</div>
        <textarea name="long_description"
            class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
            rows="12">{{ old('long_description', $package->long_description ?? '') }}</textarea>

        @error('long_description')
        <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>
    {{-- SEO --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="text-sm font-extrabold text-slate-900 mb-3">SEO (Opsional)</div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">SEO Title</label>
                <input type="text"
                    name="seo_title"
                    value="{{ old('seo_title', $package->seo_title ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                    placeholder="Judul meta (opsional)">
            </div>

            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">SEO Keywords</label>
                <input type="text"
                    name="seo_keywords"
                    value="{{ old('seo_keywords', $package->seo_keywords ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                    placeholder="contoh: sewa mobil, rental avanza, jogja">
                <div class="mt-1 text-xs text-slate-500">Pisahkan dengan koma.</div>
            </div>

            <div class="md:col-span-12">
                <label class="block text-sm font-bold text-slate-800 mb-1">SEO Description</label>
                <textarea name="seo_description" rows="3"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                    placeholder="Deskripsi meta (opsional)">{{ old('seo_description', $package->seo_description ?? '') }}</textarea>
            </div>
        </div>
    </div>


    {{-- Actions --}}
    <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('admin.rent-car-packages.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
            Back
        </a>

        <button type="submit"
            class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-extrabold text-white transition"
            style="background:#0194F3;"
            onmouseover="this.style.background='#0186DB'"
            onmouseout="this.style.background='#0194F3'">
            <i data-lucide="save" class="w-4 h-4"></i>
            {{ $buttonText ?? 'Save' }}
        </button>
    </div>

</div>

{{-- SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const list = document.getElementById('feature-list');
        const addBtn = document.getElementById('add-feature');

        let index = {
            {
                count($features)
            }
        };

        addBtn?.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'feature-row rounded-2xl border border-slate-200 bg-white p-3';

            row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                <div class="md:col-span-7">
                    <label class="block text-xs font-extrabold text-slate-600 mb-1">Feature name</label>
                    <input type="text"
                           name="features[${index}][name]"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                           placeholder="Contoh: Free Driver"
                           required>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-extrabold text-slate-600 mb-1">Available</label>
                    <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                        <input type="checkbox"
                               class="rounded border-slate-300"
                               name="features[${index}][available]"
                               value="1">
                        <span class="font-bold text-slate-700">Yes</span>
                    </label>
                </div>

                <div class="md:col-span-2 md:text-right">
                    <button type="button"
                            class="remove-feature inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition w-full md:w-auto"
                            style="background:#ef4444">
                        <span>Remove</span>
                    </button>
                </div>
            </div>
        `;

            list.appendChild(row);
            index++;
        });

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.remove-feature');
            if (!btn) return;
            btn.closest('.feature-row')?.remove();
        });
    });
</script>