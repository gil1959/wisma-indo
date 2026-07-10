@csrf

<div class="space-y-6">

    {{-- Basic --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

        <div class="md:col-span-6">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Title</label>
            <input type="text" name="title"
                value="{{ old('title', $package->title ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                required>
        </div>

        <div class="md:col-span-6">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Slug (auto)</label>
            <input type="text" name="slug"
                value="{{ old('slug', $package->slug ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                placeholder="akan dibuat otomatis dari title (boleh diubah)">
        </div>

        <div class="md:col-span-4">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Label</label>
            <input type="text" name="label"
                value="{{ old('label', $package->label ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                placeholder="opsional (misal: PROMO)">
        </div>

        <div class="md:col-span-4">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Price per Night</label>
            <input type="number" name="price_per_night" min="0"
                value="{{ old('price_per_night', $package->price_per_night ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                required>
        </div>



    </div>

    {{-- Thumbnail --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-6">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Thumbnail</label>
            <input type="file" name="thumbnail" accept="image/*"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            <p class="mt-1 text-xs text-slate-500">PNG/JPG/WEBP, max 2MB</p>
        </div>

        <div class="md:col-span-6">
            @isset($package)
            @if(!empty($package->thumbnail_path))
            <div class="mt-6">
                <div class="text-xs font-extrabold text-slate-700 mb-2">Current Thumbnail</div>
                <div class="h-28 w-44 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                    <img src="{{ asset('storage/' . $package->thumbnail_path) }}"
                        class="h-full w-full object-cover"
                        alt="Thumbnail">
                </div>
            </div>
            @endif
            @endisset
        </div>
    </div>

    {{-- Status --}}
    @role('admin')
    <div>
        <label class="block text-sm font-extrabold text-slate-800 mb-1">Status</label>
        <select name="is_active"
            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            <option value="1" {{ old('is_active', $package->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active', $package->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    @endrole

    {{-- Divider --}}
    <div class="h-px bg-slate-200"></div>

    {{-- Features --}}
    <div>
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-sm font-extrabold text-slate-900">Package Features</div>
                <div class="text-xs text-slate-500">Checklist fitur tersedia / tidak.</div>
            </div>
        </div>

        @php
        $features = old('features', $package->features ?? []);
        if (!is_array($features)) $features = [];
        @endphp

        <div class="mt-3 space-y-2"
            x-data="{
                rows: @js($features),
                addRow(){ this.rows.push({name:'', available:false}); },
                removeRow(i){ this.rows.splice(i,1); }
             }">

            <template x-for="(row, idx) in rows" :key="idx">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                    <div class="sm:col-span-8">
                        <input type="text"
                            :name="`features[${idx}][name]`"
                            x-model="row.name"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                            placeholder="Nama fitur (contoh: Driver, BBM, AC)">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="inline-flex items-center gap-2 text-sm font-extrabold text-slate-800">
                            <input type="checkbox"
                                :name="`features[${idx}][available]`"
                                x-model="row.available"
                                class="rounded border-slate-300">
                            Available
                        </label>
                    </div>

                    <div class="sm:col-span-1">
                        <button type="button"
                            @click="removeRow(idx)"
                            class="w-full inline-flex items-center justify-center rounded-xl px-3 py-2.5 text-xs font-extrabold text-white transition"
                            style="background:#ef4444"
                            onmouseover="this.style.background='#dc2626'"
                            onmouseout="this.style.background='#ef4444'">
                            X
                        </button>
                    </div>
                </div>
            </template>

            <button type="button"
                @click="addRow()"
                class="mt-3 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                style="background:#16a34a"
                onmouseover="this.style.background='#15803d'"
                onmouseout="this.style.background='#16a34a'">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Feature
            </button>
        </div>
    </div>

    {{-- Divider --}}
    <div class="h-px bg-slate-200"></div>

    {{-- Description --}}
    <div>
        <label class="block text-sm font-extrabold text-slate-800 mb-1">Description</label>
        <textarea name="long_description"
            rows="10"
            class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
            placeholder="Deskripsi paket...">{{ old('long_description', $package->long_description ?? '') }}</textarea>
    </div>

    {{-- SEO --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-4">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">SEO Title</label>
            <input type="text" name="seo_title"
                value="{{ old('seo_title', $package->seo_title ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </div>

        <div class="md:col-span-4">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">SEO Keywords</label>
            <input type="text" name="seo_keywords"
                value="{{ old('seo_keywords', $package->seo_keywords ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </div>

        <div class="md:col-span-4">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">SEO Description</label>
            <input type="text" name="seo_description"
                value="{{ old('seo_description', $package->seo_description ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('partner.hotel-packages.index') }}"
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