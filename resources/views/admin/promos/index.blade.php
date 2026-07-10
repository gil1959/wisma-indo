@extends('layouts.admin')

@section('title', 'Promo')
@section('page-title', 'Promo')

@section('content')
<div class="space-y-5">

    {{-- Errors --}}
    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="font-extrabold">Ada error</div>
            <ul class="mt-2 list-disc pl-5 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-start justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Promo</h2>
            <p class="mt-1 text-sm text-slate-600">Buat kode promo dan kelola promo aktif.</p>
        </div>
    </div>

    {{-- Card: Add Promo --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-2xl grid place-items-center border"
                     style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                    <i data-lucide="ticket-percent" class="w-5 h-5" style="color:#0194F3;"></i>
                </div>
                <div>
                    <div class="font-extrabold text-slate-900">Tambah Promo Baru</div>
                    <div class="text-xs text-slate-500">Buat kode & atur tipe diskon.</div>
                </div>
            </div>

            @if(session('success'))
                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                      style="background: rgba(16,185,129,0.10); border-color: rgba(16,185,129,0.25); color:#065f46;">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    {{ session('success') }}
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.promos.store') }}" class="mt-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                {{-- Code --}}
                <div class="md:col-span-4">
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Kode Promo</label>
                    <input type="text"
                           name="code"
                           value="{{ old('code') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           placeholder="CONTOH: LIBURAN50"
                           autocomplete="off"
                           required>
                    <div class="mt-1 text-xs text-slate-500">Saran: huruf besar + angka, tanpa spasi.</div>
                </div>

                {{-- Type --}}
                <div class="md:col-span-4">
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Tipe Diskon</label>
                    <select name="type"
                            id="discount_type"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                            required>
                        <option value="nominal" {{ old('type') === 'nominal' ? 'selected' : '' }}>Potongan Harga (Rp)</option>
                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                    <div class="mt-1 text-xs text-slate-500">Nominal = rupiah, Persentase = %.</div>
                </div>

                {{-- Value --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Nilai Diskon</label>
                    <input type="number"
                           name="value"
                           id="discount_value"
                           value="{{ old('value') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           placeholder="contoh: 50000"
                           min="0"
                           required>
                    <div class="mt-1 text-xs text-slate-500" id="value_help">Masukkan angka rupiah.</div>
                </div>

                {{-- Submit --}}
                <div class="md:col-span-1 flex md:justify-end items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                            style="background:#0194F3;"
                            onmouseover="this.style.background='#0186DB'"
                            onmouseout="this.style.background='#0194F3'">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- List Promo --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <div class="font-extrabold text-slate-900">Daftar Promo Aktif</div>
                <div class="text-xs text-slate-500">Hapus promo yang sudah tidak dipakai.</div>
            </div>
        </div>

        @if ($promos->isEmpty())
            <div class="p-10 text-center">
                <div class="mx-auto h-12 w-12 rounded-2xl border grid place-items-center"
                     style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                    <i data-lucide="ticket" class="w-6 h-6" style="color:#0194F3;"></i>
                </div>
                <div class="mt-3 font-extrabold text-slate-900">Belum ada kode promo</div>
                <div class="mt-1 text-sm text-slate-600">Buat promo pertama dari form di atas.</div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-[720px] w-full text-left">
                    <thead class="bg-slate-50">
                    <tr class="text-xs font-extrabold text-slate-600">
                        <th class="px-5 py-3">Kode</th>
                        <th class="px-5 py-3 w-[220px]">Tipe</th>
                        <th class="px-5 py-3 w-[220px]">Nilai</th>
                        <th class="px-5 py-3 text-right w-[160px]">Aksi</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                    @foreach ($promos as $p)
                        <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">{{ $p->code }}</div>
                            </td>

                            <td class="px-5 py-4">
                                @if($p->type === 'nominal')
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                                        <i data-lucide="badge-dollar-sign" class="w-4 h-4"></i>
                                        Potongan (Rp)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(16,185,129,0.10); border-color: rgba(16,185,129,0.25); color:#065f46;">
                                        <i data-lucide="percent" class="w-4 h-4"></i>
                                        Persentase (%)
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">
                                    @if($p->type === 'nominal')
                                        Rp {{ number_format($p->value, 0, ',', '.') }}
                                    @else
                                        {{ rtrim(rtrim(number_format($p->value, 2), '0'), '.') }}%
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500">Value: {{ $p->value }}</div>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <form method="POST"
                                      action="{{ route('admin.promos.destroy', $p->id) }}"
                                      onsubmit="return confirm('Hapus promo ini?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                            style="background:#ef4444"
                                            onmouseover="this.style.background='#dc2626'"
                                            onmouseout="this.style.background='#ef4444'">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('discount_type');
    const valueInput = document.getElementById('discount_value');
    const help = document.getElementById('value_help');

    function updateInputAppearance() {
        const t = typeSelect?.value;

        if (!valueInput || !help) return;

        if (t === 'nominal') {
            valueInput.placeholder = "contoh: 50000";
            help.textContent = "Masukkan angka rupiah.";
        } else if (t === 'percentage') {
            valueInput.placeholder = "contoh: 10";
            help.textContent = "Masukkan angka persen (tanpa %).";
        }
    }

    typeSelect?.addEventListener('change', updateInputAppearance);
    updateInputAppearance();
});
</script>
@endsection
