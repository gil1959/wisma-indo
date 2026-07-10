@php
    $pkg = $package ?? null;
    $tiers = ($pkg?->tiers ?? collect())->where('type', $type)->values();
@endphp

<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>{{ $label }}</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5">
        <div
            x-data='{
                rows: @json(old("tiers.$type", $tiers->toArray())),
                insertCustom() {
                    if (this.rows.some(r => Number(r.is_custom) === 1 || r.is_custom === true)) return;
                    this.rows.push({ is_custom: 1, min_people: 2, max_people: null, price: "" });
                },
                insertNormal() {
                    this.rows.push({ is_custom: 0, min_people: "", max_people: "", price: "" });
                }
            }'
            class="space-y-3"
        >

            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <template x-if="Number(row.is_custom) === 1">
                        <div class="mb-3 text-xs font-extrabold" style="color:#055a93;">
                            Custom Tier (min 2 orang, tanpa batas)
                        </div>
                    </template>
<div class="sm:col-span-3">
  <label class="block text-sm font-bold text-slate-800 mb-1">Label</label>
  <input type="text"
         x-model="row.label_text"
         :name="`tiers[{{ $type }}][${index}][label_text]`"
         class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
</div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-4">
                            <label class="block text-sm font-bold text-slate-800 mb-1">Min Orang</label>
                            <input type="number"
                                   :readonly="Number(row.is_custom) === 1"
                                   x-model="row.min_people"
                                   :name="`tiers[{{ $type }}][${index}][min_people]`"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-sm font-bold text-slate-800 mb-1">Max Orang</label>
                            <input type="number"
                                   :readonly="Number(row.is_custom) === 1"
                                   x-model="row.max_people"
                                   :placeholder="Number(row.is_custom) === 1 ? '∞' : ''"
                                   :name="`tiers[{{ $type }}][${index}][max_people]`"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-sm font-bold text-slate-800 mb-1">Harga / pax</label>
                            <input type="number"
                                   x-model="row.price"
                                   :name="`tiers[{{ $type }}][${index}][price]`"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                        </div>

                        <div class="sm:col-span-1 flex sm:items-end">
                            <button type="button"
                                    @click="rows.splice(index, 1)"
                                    class="w-full inline-flex items-center justify-center rounded-xl px-3 py-2.5 text-xs font-extrabold text-white transition"
                                    style="background:#ef4444"
                                    onmouseover="this.style.background='#dc2626'"
                                    onmouseout="this.style.background='#ef4444'">
                                X
                            </button>
                        </div>

                        {{-- required hidden fields --}}
                        <input type="hidden"
                               :name="`tiers[{{ $type }}][${index}][is_custom]`"
                               :value="Number(row.is_custom) === 1 ? 1 : 0">

                        <input type="hidden"
                               :name="`tiers[{{ $type }}][${index}][type]`"
                               value="{{ $type }}">
                    </div>
                </div>
            </template>

            <div class="flex flex-col sm:flex-row gap-2 pt-2">
                <button type="button"
                        @click="insertNormal()"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                        style="background:#16a34a"
                        onmouseover="this.style.background='#15803d'"
                        onmouseout="this.style.background='#16a34a'">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Tier Normal
                </button>

                <button type="button"
                        @click="insertCustom()"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition disabled:opacity-50"
                        style="background:#4f46e5"
                        onmouseover="this.style.background='#4338ca'"
                        onmouseout="this.style.background='#4f46e5'"
                        :disabled="rows.some(r => Number(r.is_custom) === 1 || r.is_custom === true)">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    Tambah Tier Custom
                </button>
            </div>

        </div>
    </div>
</div>
