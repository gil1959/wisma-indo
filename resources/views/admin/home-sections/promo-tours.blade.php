@extends('layouts.admin')

@section('title', 'Home - Promo Tours')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Home Section: Promo Paket Tour</h1>
           
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-800">
            <div class="font-extrabold">Sukses</div>
            <div class="text-sm mt-1">{{ session('success') }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.home-sections.promo-tours.update') }}" class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm">
        @csrf

        <div class="px-6 py-5 border-b border-slate-200">
            <div class="text-lg font-extrabold text-slate-900">Pengaturan</div>
            <div class="text-sm text-slate-600 mt-1">Pastikan paket yang mau tampil sudah punya label PROMO.</div>
        </div>

        <div class="p-6 grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2 flex items-center gap-3">
                <input
                    type="checkbox"
                    name="home_promo_enabled"
                    value="1"
                    class="rounded border-slate-300"
                    {{ old('home_promo_enabled', ($settings['home_promo_enabled'] ?? '1')) == '1' ? 'checked' : '' }}
                />
                <div class="text-sm text-slate-900 font-semibold">Aktifkan section Promo</div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-900 mb-2">Badge</label>
                <input
                    name="home_promo_badge"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
                    value="{{ old('home_promo_badge', $settings['home_promo_badge'] ?? 'PROMO') }}"
                    placeholder="PROMO"
                />
                @error('home_promo_badge') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-900 mb-2">Mode</label>
                @php $mode = old('home_promo_mode', $settings['home_promo_mode'] ?? 'auto'); @endphp
                <select name="home_promo_mode" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900">
                    <option value="auto" {{ $mode === 'auto' ? 'selected' : '' }}>Auto (ambil label PROMO)</option>
                    <option value="custom" {{ $mode === 'custom' ? 'selected' : '' }}>Custom (pilih paket)</option>
                </select>
                @error('home_promo_mode') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-900 mb-2">Judul</label>
                <input
                    name="home_promo_title"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
                    value="{{ old('home_promo_title', $settings['home_promo_title'] ?? 'Paket Tour Promo') }}"
                    placeholder="Paket Tour Promo"
                />
                @error('home_promo_title') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-900 mb-2">Deskripsi</label>
                <textarea
                    name="home_promo_desc"
                    rows="3"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
                    placeholder="Tampilkan promo terbaik minggu ini..."
                >{{ old('home_promo_desc', $settings['home_promo_desc'] ?? '') }}</textarea>
                @error('home_promo_desc') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
        <div class="flex items-center justify-between gap-3 mb-2">
        <div class="text-xs text-slate-500">Dipakai hanya kalau mode = Custom</div>

        {{-- SEARCH BOX --}}
        <div class="w-full max-w-xs">
            <input
                id="promoTourSearch"
                type="text"
                class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm text-slate-900"
                placeholder="CARI PAKET TOUR"
                autocomplete="off"
            />
        </div>
        </div>

        <div class="rounded-2xl border border-slate-200 p-4 max-h-80 overflow-auto">
        <div id="promoTourSearchEmpty" class="hidden text-sm text-slate-500">
            Tidak ada paket yang cocok.
        </div>

        @if($promoCandidates->count() > 0)
            <div class="grid gap-2" id="promoTourList">
                @foreach($promoCandidates as $p)
                    <label
                        class="promo-tour-item flex items-center gap-3 text-sm"
                        data-search="{{ \Illuminate\Support\Str::lower('#'.$p->id.' '.$p->title) }}"
                    >

                                    <input
                                        type="checkbox"
                                        name="home_promo_custom_ids[]"
                                        value="{{ $p->id }}"
                                        class="rounded border-slate-300"
                                        {{ in_array((int)$p->id, old('home_promo_custom_ids', $selectedIds)) ? 'checked' : '' }}
                                    />
                                    <span class="text-slate-900 font-semibold">#{{ $p->id }}</span>
                                    <span class="text-slate-700">{{ $p->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-slate-500">
                            Belum ada paket berlabel PROMO (atau belum aktif).
                        </div>
                    @endif
                </div>

                @error('home_promo_custom_ids') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
                @error('home_promo_custom_ids.*') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror

                
            </div>
            {{-- ===================== NEW: PROMO SEWA KAPAL ===================== --}}
<div class="md:col-span-2 mt-4">
    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
        <div class="text-lg font-extrabold text-slate-900">Promo Sewa Kapal</div>
        <div class="text-sm text-slate-600 mt-1">
            Pastikan paket sewa kapal yang mau tampil punya label <b>PROMO</b> dan status aktif.
        </div>
    </div>
</div>

<div class="md:col-span-2 flex items-center gap-3">
    <input
        type="checkbox"
        name="home_ship_promo_enabled"
        value="1"
        class="rounded border-slate-300"
        {{ old('home_ship_promo_enabled', ($settings['home_ship_promo_enabled'] ?? '1')) == '1' ? 'checked' : '' }}
    />
    <div class="text-sm text-slate-900 font-semibold">Aktifkan section Promo Sewa Kapal</div>
</div>

<div>
    <label class="block text-sm font-semibold text-slate-900 mb-2">Badge</label>
    <input
        name="home_ship_promo_badge"
        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
        value="{{ old('home_ship_promo_badge', $settings['home_ship_promo_badge'] ?? 'PROMO KAPAL') }}"
        placeholder="PROMO KAPAL"
    />
    @error('home_ship_promo_badge') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div>
    <label class="block text-sm font-semibold text-slate-900 mb-2">Mode</label>
    @php $shipMode = old('home_ship_promo_mode', $settings['home_ship_promo_mode'] ?? 'auto'); @endphp
    <select name="home_ship_promo_mode" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900">
        <option value="auto" {{ $shipMode === 'auto' ? 'selected' : '' }}>Auto (ambil label PROMO)</option>
        <option value="custom" {{ $shipMode === 'custom' ? 'selected' : '' }}>Custom (pilih paket)</option>
    </select>
    @error('home_ship_promo_mode') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-2">
    <label class="block text-sm font-semibold text-slate-900 mb-2">Judul</label>
    <input
        name="home_ship_promo_title"
        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
        value="{{ old('home_ship_promo_title', $settings['home_ship_promo_title'] ?? 'Paket Sewa Kapal Promo') }}"
        placeholder="Paket Sewa Kapal Promo"
    />
    @error('home_ship_promo_title') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-2">
    <label class="block text-sm font-semibold text-slate-900 mb-2">Deskripsi</label>
    <textarea
        name="home_ship_promo_desc"
        rows="3"
        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
        placeholder="Tampilkan promo terbaik minggu ini..."
    >{{ old('home_ship_promo_desc', $settings['home_ship_promo_desc'] ?? '') }}</textarea>
    @error('home_ship_promo_desc') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-2">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div class="text-xs text-slate-500">Dipakai hanya kalau mode = Custom</div>

        <div class="w-full max-w-xs">
            <input
                id="promoShipSearch"
                type="text"
                class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm text-slate-900"
                placeholder="CARI PAKET SEWA KAPAL"
                autocomplete="off"
            />
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 p-4 max-h-80 overflow-auto">
        <div id="promoShipSearchEmpty" class="hidden text-sm text-slate-500">
            Tidak ada paket yang cocok.
        </div>

        @if(isset($promoShipCandidates) && $promoShipCandidates->count() > 0)
            <div class="grid gap-2" id="promoShipList">
                @foreach($promoShipCandidates as $p)
                    <label
                        class="promo-ship-item flex items-center gap-3 text-sm"
                        data-search="{{ \Illuminate\Support\Str::lower('#'.$p->id.' '.$p->title) }}"
                    >
                        <input
                            type="checkbox"
                            name="home_ship_promo_custom_ids[]"
                            value="{{ $p->id }}"
                            class="rounded border-slate-300"
                            {{ in_array((int)$p->id, old('home_ship_promo_custom_ids', $selectedShipIds ?? [])) ? 'checked' : '' }}
                        />
                        <span class="text-slate-900 font-semibold">#{{ $p->id }}</span>
                        <span class="text-slate-700">{{ $p->title }}</span>
                    </label>
                @endforeach
            </div>
        @else
            <div class="text-sm text-slate-500">
                Belum ada paket sewa kapal berlabel PROMO (atau belum aktif).
            </div>
        @endif
    </div>

    @error('home_ship_promo_custom_ids') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
    @error('home_ship_promo_custom_ids.*') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

{{-- ===================== NEW: PROMO UMRAH ===================== --}}
<div class="md:col-span-2 mt-4">
    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
        <div class="text-lg font-extrabold text-slate-900">Promo Umrah</div>
        <div class="text-sm text-slate-600 mt-1">
            Pastikan paket umrah yang mau tampil punya label <b>PROMO</b> dan status aktif.
        </div>
    </div>
</div>

<div class="md:col-span-2 flex items-center gap-3">
    <input
        type="checkbox"
        name="home_umrah_promo_enabled"
        value="1"
        class="rounded border-slate-300"
        {{ old('home_umrah_promo_enabled', ($settings['home_umrah_promo_enabled'] ?? '1')) == '1' ? 'checked' : '' }}
    />
    <div class="text-sm text-slate-900 font-semibold">Aktifkan section Promo Umrah</div>
</div>

<div>
    <label class="block text-sm font-semibold text-slate-900 mb-2">Badge</label>
    <input
        name="home_umrah_promo_badge"
        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
        value="{{ old('home_umrah_promo_badge', $settings['home_umrah_promo_badge'] ?? 'PROMO UMRAH') }}"
        placeholder="PROMO UMRAH"
    />
    @error('home_umrah_promo_badge') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div>
    <label class="block text-sm font-semibold text-slate-900 mb-2">Mode</label>
    @php $umrahMode = old('home_umrah_promo_mode', $settings['home_umrah_promo_mode'] ?? 'auto'); @endphp
    <select name="home_umrah_promo_mode" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900">
        <option value="auto" {{ $umrahMode === 'auto' ? 'selected' : '' }}>Auto (ambil label PROMO)</option>
        <option value="custom" {{ $umrahMode === 'custom' ? 'selected' : '' }}>Custom (pilih paket)</option>
    </select>
    @error('home_umrah_promo_mode') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-2">
    <label class="block text-sm font-semibold text-slate-900 mb-2">Judul</label>
    <input
        name="home_umrah_promo_title"
        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
        value="{{ old('home_umrah_promo_title', $settings['home_umrah_promo_title'] ?? 'Paket Umrah Promo') }}"
        placeholder="Paket Umrah Promo"
    />
    @error('home_umrah_promo_title') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-2">
    <label class="block text-sm font-semibold text-slate-900 mb-2">Deskripsi</label>
    <textarea
        name="home_umrah_promo_desc"
        rows="3"
        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
        placeholder="Tampilkan promo terbaik minggu ini..."
    >{{ old('home_umrah_promo_desc', $settings['home_umrah_promo_desc'] ?? '') }}</textarea>
    @error('home_umrah_promo_desc') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-2">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div class="text-xs text-slate-500">Dipakai hanya kalau mode = Custom</div>
        <div class="w-full max-w-xs">
            <input
                id="promoUmrahSearch"
                type="text"
                class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm text-slate-900"
                placeholder="CARI PAKET UMRAH"
                autocomplete="off"
            />
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 p-4 max-h-80 overflow-auto">
        <div id="promoUmrahSearchEmpty" class="hidden text-sm text-slate-500">
            Tidak ada paket yang cocok.
        </div>

        @if(isset($promoUmrahCandidates) && $promoUmrahCandidates->count() > 0)
            <div class="grid gap-2" id="promoUmrahList">
                @foreach($promoUmrahCandidates as $p)
                    <label class="promo-umrah-item flex items-center gap-3 text-sm"
                           data-search="{{ \Illuminate\Support\Str::lower('#'.$p->id.' '.$p->title) }}">
                        <input
                            type="checkbox"
                            name="home_umrah_promo_custom_ids[]"
                            value="{{ $p->id }}"
                            class="rounded border-slate-300"
                            {{ in_array((int)$p->id, old('home_umrah_promo_custom_ids', $selectedUmrahIds ?? [])) ? 'checked' : '' }}
                        />
                        <span class="text-slate-900 font-semibold">#{{ $p->id }}</span>
                        <span class="text-slate-700">{{ $p->title }}</span>
                    </label>
                @endforeach
            </div>
        @else
            <div class="text-sm text-slate-500">
                Belum ada paket umrah berlabel PROMO (atau belum aktif).
            </div>
        @endif
    </div>

    @error('home_umrah_promo_custom_ids') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
    @error('home_umrah_promo_custom_ids.*') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

{{-- ===================== NEW: PROMO MICE ===================== --}}
<div class="md:col-span-2 mt-4">
    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
        <div class="text-lg font-extrabold text-slate-900">Promo MICE</div>
        <div class="text-sm text-slate-600 mt-1">
            Pastikan paket MICE yang mau tampil punya label <b>PROMO</b> dan status aktif.
        </div>
    </div>
</div>

<div class="md:col-span-2 flex items-center gap-3">
    <input
        type="checkbox"
        name="home_mice_promo_enabled"
        value="1"
        class="rounded border-slate-300"
        {{ old('home_mice_promo_enabled', ($settings['home_mice_promo_enabled'] ?? '1')) == '1' ? 'checked' : '' }}
    />
    <div class="text-sm text-slate-900 font-semibold">Aktifkan section Promo MICE</div>
</div>

<div>
    <label class="block text-sm font-semibold text-slate-900 mb-2">Badge</label>
    <input
        name="home_mice_promo_badge"
        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
        value="{{ old('home_mice_promo_badge', $settings['home_mice_promo_badge'] ?? 'PROMO MICE') }}"
        placeholder="PROMO MICE"
    />
    @error('home_mice_promo_badge') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div>
    <label class="block text-sm font-semibold text-slate-900 mb-2">Mode</label>
    @php $miceMode = old('home_mice_promo_mode', $settings['home_mice_promo_mode'] ?? 'auto'); @endphp
    <select name="home_mice_promo_mode" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900">
        <option value="auto" {{ $miceMode === 'auto' ? 'selected' : '' }}>Auto (ambil label PROMO)</option>
        <option value="custom" {{ $miceMode === 'custom' ? 'selected' : '' }}>Custom (pilih paket)</option>
    </select>
    @error('home_mice_promo_mode') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-2">
    <label class="block text-sm font-semibold text-slate-900 mb-2">Judul</label>
    <input
        name="home_mice_promo_title"
        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
        value="{{ old('home_mice_promo_title', $settings['home_mice_promo_title'] ?? 'Paket MICE Promo') }}"
        placeholder="Paket MICE Promo"
    />
    @error('home_mice_promo_title') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-2">
    <label class="block text-sm font-semibold text-slate-900 mb-2">Deskripsi</label>
    <textarea
        name="home_mice_promo_desc"
        rows="3"
        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
        placeholder="Tampilkan promo terbaik minggu ini..."
    >{{ old('home_mice_promo_desc', $settings['home_mice_promo_desc'] ?? '') }}</textarea>
    @error('home_mice_promo_desc') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>

<div class="md:col-span-2">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div class="text-xs text-slate-500">Dipakai hanya kalau mode = Custom</div>
        <div class="w-full max-w-xs">
            <input
                id="promoMiceSearch"
                type="text"
                class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm text-slate-900"
                placeholder="CARI PAKET MICE"
                autocomplete="off"
            />
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 p-4 max-h-80 overflow-auto">
        <div id="promoMiceSearchEmpty" class="hidden text-sm text-slate-500">
            Tidak ada paket yang cocok.
        </div>

        @if(isset($promoMiceCandidates) && $promoMiceCandidates->count() > 0)
            <div class="grid gap-2" id="promoMiceList">
                @foreach($promoMiceCandidates as $p)
                    <label class="promo-mice-item flex items-center gap-3 text-sm"
                           data-search="{{ \Illuminate\Support\Str::lower('#'.$p->id.' '.$p->title) }}">
                        <input
                            type="checkbox"
                            name="home_mice_promo_custom_ids[]"
                            value="{{ $p->id }}"
                            class="rounded border-slate-300"
                            {{ in_array((int)$p->id, old('home_mice_promo_custom_ids', $selectedMiceIds ?? [])) ? 'checked' : '' }}
                        />
                        <span class="text-slate-900 font-semibold">#{{ $p->id }}</span>
                        <span class="text-slate-700">{{ $p->title }}</span>
                    </label>
                @endforeach
            </div>
        @else
            <div class="text-sm text-slate-500">
                Belum ada paket MICE berlabel PROMO (atau belum aktif).
            </div>
        @endif
    </div>

    @error('home_mice_promo_custom_ids') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
    @error('home_mice_promo_custom_ids.*') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
</div>


        </div>

        <div class="px-6 py-5 border-t border-slate-200 flex items-center justify-end gap-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function bindSearch(inputId, listId, itemClass, emptyId) {
        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        const empty = document.getElementById(emptyId);

        if (!input || !list) return;

        const items = Array.from(list.querySelectorAll('.' + itemClass));

        function applyFilter() {
            const q = (input.value || '').trim().toLowerCase();
            let visibleCount = 0;

            items.forEach((el) => {
                const hay = (el.dataset.search || '');
                const show = q === '' || hay.includes(q);
                el.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            if (empty) empty.classList.toggle('hidden', visibleCount !== 0);
        }

        input.addEventListener('input', applyFilter);
        applyFilter();
    }

    // tours
    bindSearch('promoTourSearch', 'promoTourList', 'promo-tour-item', 'promoTourSearchEmpty');

    // ships (NEW)
    bindSearch('promoShipSearch', 'promoShipList', 'promo-ship-item', 'promoShipSearchEmpty');

    bindSearch('promoUmrahSearch', 'promoUmrahList', 'promo-umrah-item', 'promoUmrahSearchEmpty');
bindSearch('promoMiceSearch', 'promoMiceList', 'promo-mice-item', 'promoMiceSearchEmpty');
});

</script>
@endpush

@endsection
