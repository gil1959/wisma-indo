@extends('layouts.admin')

@section('title', 'Tambah Review')
@section('page-title', 'Tambah Review')

@section('content')
<div class="max-w-3xl space-y-5">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <div class="text-sm font-extrabold text-slate-900">Tambah Review (Manual dari Admin)</div>
            <div class="mt-1 text-xs text-slate-500">Review ini akan langsung berstatus <b>approved</b>.</div>
        </div>

        <form method="POST" action="{{ route('admin.reviews.store') }}" class="p-6 space-y-4">
            @csrf

            {{-- Paket selector (type + search + select) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Tipe Paket</label>
                    <select id="packageType" name="package_type"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                        <option value="tour" {{ old('package_type','tour') === 'tour' ? 'selected' : '' }}>Tour</option>
                        <option value="rent_car" {{ old('package_type') === 'rent_car' ? 'selected' : '' }}>Rent Car</option>
                        <option value="ship" {{ old('package_type') === 'ship' ? 'selected' : '' }}>Sewa Kapal</option>
                        <option value="umrah" {{ old('package_type') === 'umrah' ? 'selected' : '' }}>Umrah</option>
                    </select>
                    @error('package_type') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Cari Paket</label>
                    <input id="packageSearch" type="text" autocomplete="off"
                           placeholder="Ketik judul atau ID paket..."
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                    <div class="mt-1 text-xs text-slate-500">Contoh: “umrah hemat” atau “12”</div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-800 mb-1">Pilih Paket</label>
                <select id="packageSelect" name="package_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                    <option value="">-- Cari dulu paketnya --</option>
                </select>
                @error('package_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Reviewer --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                    @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                    @error('email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Rating --}}
            <div>
                <label class="block text-sm font-bold text-slate-800 mb-1">Jumlah Bintang (1 - 5)</label>
                <select name="rating"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                    @for($i=1; $i<=5; $i++)
                        <option value="{{ $i }}" {{ (int)old('rating', 5) === $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                @error('rating') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Comment --}}
            <div>
                <label class="block text-sm font-bold text-slate-800 mb-1">Ulasan</label>
                <textarea name="comment" rows="5"
                          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                          placeholder="Tulis ulasan..." required>{{ old('comment') }}</textarea>
                @error('comment') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 pt-2">
                <a href="{{ route('admin.reviews.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-[#0194F3] px-4 py-2 text-sm font-bold text-white hover:opacity-95">
                    Simpan Review
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const typeEl = document.getElementById('packageType');
    const searchEl = document.getElementById('packageSearch');
    const selectEl = document.getElementById('packageSelect');

    let timer = null;

    function setOptionsLoading() {
        selectEl.innerHTML = '<option value="">Loading...</option>';
    }

    function setOptionsEmpty(msg) {
        selectEl.innerHTML = `<option value="">${msg}</option>`;
    }

    async function fetchPackages() {
        const type = typeEl.value;
        const q = (searchEl.value || '').trim();

        setOptionsLoading();

        const url = new URL("{{ route('admin.reviews.packages') }}", window.location.origin);
        url.searchParams.set('type', type);
        if (q !== '') url.searchParams.set('q', q);

        try {
            const res = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!res.ok) throw new Error('Request failed: ' + res.status);

            const data = await res.json();
            const items = data.items || [];

            if (items.length === 0) {
                setOptionsEmpty('Tidak ada hasil');
                return;
            }

            const oldSelected = "{{ old('package_id') }}";
            selectEl.innerHTML = '<option value="">-- Pilih Paket --</option>';

            for (const it of items) {
                const opt = document.createElement('option');
                opt.value = it.id;
                opt.textContent = `#${it.id} - ${it.title}`;
                if (oldSelected && String(oldSelected) === String(it.id)) opt.selected = true;
                selectEl.appendChild(opt);
            }
        } catch (e) {
            setOptionsEmpty('Gagal load data');
        }
    }

    function debounceFetch() {
        clearTimeout(timer);
        timer = setTimeout(fetchPackages, 250);
    }

    typeEl.addEventListener('change', () => {
        searchEl.value = '';
        fetchPackages();
    });

    searchEl.addEventListener('input', debounceFetch);

    // initial load (tanpa query, tampil 20 paket pertama)
    fetchPackages();
})();
</script>
@endpush
