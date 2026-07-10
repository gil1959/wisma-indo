@extends('layouts.admin')

@section('title', $package->exists ? 'Edit Paket Wisata' : 'Tambah Paket Wisata')
@section('page-title', $package->exists ? 'Edit Paket Wisata' : 'Tambah Paket Wisata')

@section('content')
    @if(session('success'))
        <div class="mb-4 px-4 py-2 bg-emerald-50 text-emerald-700 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 px-4 py-2 bg-red-50 text-red-700 text-sm rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $package->exists
                        ? route('admin.tour-packages.update', $package)
                        : route('admin.tour-packages.store') }}">
        @csrf
        @if($package->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- KIRI: detail paket --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-lg shadow p-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Judul Paket</label>
                        <input type="text" name="title" value="{{ old('title', $package->title) }}"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Kategori</label>
                            <select name="category" class="w-full border rounded px-3 py-2 text-sm">
                                <option value="domestic" {{ old('category', $package->category) === 'domestic' ? 'selected' : '' }}>Domestik</option>
                                <option value="international" {{ old('category', $package->category) === 'international' ? 'selected' : '' }}>Internasional</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Destinasi</label>
                            <input type="text" name="destination" value="{{ old('destination', $package->destination) }}"
                                   class="w-full border rounded px-3 py-2 text-sm" placeholder="Nusa Penida, Bali, dll">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Durasi</label>
                            <input type="text" name="duration_text" value="{{ old('duration_text', $package->duration_text) }}"
                                   class="w-full border rounded px-3 py-2 text-sm" placeholder="1 Hari, 3D2N, dst">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Ringkasan Singkat</label>
                        <textarea name="short_description" rows="3"
                                  class="w-full border rounded px-3 py-2 text-sm">{{ old('short_description', $package->short_description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Deskripsi Lengkap</label>
                        <textarea name="description" rows="6"
                                  class="w-full border rounded px-3 py-2 text-sm">{{ old('description', $package->description) }}</textarea>
                    </div>
                </div>

                {{-- ================== SECTION HARGA ================== --}}
               @php
    $domesticRanges = [
        ['key' => 'domestic_1_2',  'label' => '1–2 Pax',  'min' => 1,  'max' => 2],
        ['key' => 'domestic_3_6',  'label' => '3–6 Pax',  'min' => 3,  'max' => 6],
        ['key' => 'domestic_7_10', 'label' => '7–10 Pax', 'min' => 7,  'max' => 10],
        ['key' => 'domestic_11_15','label' => '11–15 Pax','min' => 11, 'max' => 15],
    ];

    $wnaRanges = [
        ['key' => 'wna_1_2',  'label' => '1–2 Pax',  'min' => 1,  'max' => 2],
        ['key' => 'wna_3_6',  'label' => '3–6 Pax',  'min' => 3,  'max' => 6],
        ['key' => 'wna_7_10', 'label' => '7–10 Pax', 'min' => 7,  'max' => 10],
        ['key' => 'wna_11_15','label' => '11–15 Pax','min' => 11, 'max' => 15],
    ];
@endphp

{{-- HARGA BERTINGKAT DOMESTIK --}}
<div class="mt-6 p-4 rounded-lg border border-emerald-100 bg-emerald-50/40">
    <div class="font-semibold text-sm mb-2">
        👥 Harga Bertingkat (Domestik / WNI)
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        @foreach($domesticRanges as $range)
            @php
                // cari data lama / dari DB
                $tierOld = old('price_tiers.' . $range['key'] . '.price_per_pax');

                $tierModel = $package->priceTiers
                    ->where('audience_type', 'domestic')
                    ->firstWhere('min_pax', $range['min']);

                $value = $tierOld !== null
                    ? $tierOld
                    : ($tierModel->price_per_pax ?? '');
            @endphp

            <div>
                <div class="text-xs text-gray-600 mb-1">{{ $range['label'] }}</div>

                {{-- hidden mapping --}}
                <input type="hidden" name="price_tiers[{{ $range['key'] }}][audience_type]" value="domestic">
                <input type="hidden" name="price_tiers[{{ $range['key'] }}][min_pax]" value="{{ $range['min'] }}">
                <input type="hidden" name="price_tiers[{{ $range['key'] }}][max_pax]" value="{{ $range['max'] }}">

                <div class="flex items-center gap-1">
                    <span class="text-xs text-gray-500">Rp</span>
                    <input
                        type="number"
                        min="0"
                        name="price_tiers[{{ $range['key'] }}][price_per_pax]"
                        value="{{ $value }}"
                        class="w-full border rounded px-2 py-1 text-sm"
                        placeholder="0">
                </div>
            </div>
        @endforeach
    </div>

    <p class="mt-2 text-[11px] text-gray-500">
        Kosongkan field yang tidak dipakai. Harga diisi per orang.
    </p>
</div>

{{-- HARGA BERTINGKAT WNA --}}
<div class="mt-4 p-4 rounded-lg border border-orange-100 bg-orange-50/60">
    <div class="font-semibold text-sm mb-2">
        🌐 Harga Bertingkat (Asing / WNA)
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        @foreach($wnaRanges as $range)
            @php
                $tierOld = old('price_tiers.' . $range['key'] . '.price_per_pax');

                $tierModel = $package->priceTiers
                    ->where('audience_type', 'wna')
                    ->firstWhere('min_pax', $range['min']);

                $value = $tierOld !== null
                    ? $tierOld
                    : ($tierModel->price_per_pax ?? '');
            @endphp

            <div>
                <div class="text-xs text-gray-600 mb-1">{{ $range['label'] }}</div>

                <input type="hidden" name="price_tiers[{{ $range['key'] }}][audience_type]" value="wna">
                <input type="hidden" name="price_tiers[{{ $range['key'] }}][min_pax]" value="{{ $range['min'] }}">
                <input type="hidden" name="price_tiers[{{ $range['key'] }}][max_pax]" value="{{ $range['max'] }}">

                <div class="flex items-center gap-1">
                    <span class="text-xs text-gray-500">Rp</span>
                    <input
                        type="number"
                        min="0"
                        name="price_tiers[{{ $range['key'] }}][price_per_pax]"
                        value="{{ $value }}"
                        class="w-full border rounded px-2 py-1 text-sm"
                        placeholder="0">
                </div>
            </div>
        @endforeach
    </div>

    <p class="mt-2 text-[11px] text-gray-500">
        Kosongkan field yang tidak dipakai. Harga diisi per orang.
    </p>
</div>
{{-- HARGA TIKET PESAWAT (OPSIONAL) --}}
@php
    $includeFlight = old(
        'include_flight_option',
        $package->include_flight_option ?? false
    );

    $flightPrice = old(
        'flight_surcharge_per_pax',
        $package->flight_surcharge_per_pax ?? ''
    );
@endphp

<div class="mt-6 p-4 rounded-lg border border-sky-100 bg-sky-50/60">
    <div class="font-semibold text-sm mb-2">
        ✈️ Opsi Tiket Pesawat
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox"
               name="include_flight_option"
               value="1"
               {{ $includeFlight ? 'checked' : '' }}>
        <span>Aktifkan pilihan <strong>"Dengan Tiket Pesawat"</strong> di halaman pemesanan</span>
    </label>

    <div class="mt-3 flex items-center gap-2">
        <span class="text-xs text-gray-500">Tambahan harga per orang</span>
        <span class="text-xs text-gray-500">Rp</span>
        <input
            type="number"
            min="0"
            name="flight_surcharge_per_pax"
            value="{{ $flightPrice }}"
            class="border rounded px-2 py-1 text-sm w-40"
            placeholder="1000000">
    </div>

    <p class="mt-2 text-[11px] text-gray-500">
        Contoh: isi <strong>1.000.000</strong> &mdash; jika customer memilih "dengan tiket pesawat",
        sistem akan menambahkan Rp 1.000.000 ke <em>harga per orang</em>.
        Biarkan kosong jika harga tiket ingin dicek manual via WhatsApp.
    </p>
</div>

                {{-- ================== END SECTION HARGA ================== --}}
            </div>

            {{-- KANAN: pengaturan meta & flag --}}
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow p-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Slug URL (opsional)</label>
                        <input type="text" name="slug" value="{{ old('slug', $package->slug) }}"
                               class="w-full border rounded px-3 py-2 text-sm"
                               placeholder="auto dari judul kalau dikosongkan">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">URL Thumbnail (sementara)</label>
                        <input type="text" name="thumbnail_path" value="{{ old('thumbnail_path', $package->thumbnail_path) }}"
                               class="w-full border rounded px-3 py-2 text-sm"
                               placeholder="nanti bisa diganti upload file">
                    </div>

                    <div class="flex flex-col gap-2 mt-2">
                        <label class="inline-flex items-center text-sm">
                            <input type="hidden" name="include_flight_option" value="0">
                            <input type="checkbox" name="include_flight_option" value="1"
                                   {{ old('include_flight_option', $package->include_flight_option) ? 'checked' : '' }}
                                   class="mr-1">
                            Paket ini punya opsi <strong>dengan tiket pesawat</strong>.
                        </label>

                        <label class="inline-flex items-center text-sm">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}
                                   class="mr-1">
                            Tampilkan sebagai paket aktif
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $package->meta_title) }}"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="3"
                                  class="w-full border rounded px-3 py-2 text-sm">{{ old('meta_description', $package->meta_description) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button class="px-4 py-2 rounded bg-emerald-600 text-white text-sm">
                        {{ $package->exists ? 'Simpan Perubahan' : 'Simpan Paket' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
