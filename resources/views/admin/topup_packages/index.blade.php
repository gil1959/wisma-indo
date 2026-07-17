@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Paket Top Up</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola daftar paket kuota iklan yang bisa dibeli pengguna.</p>
        </div>
        <a href="{{ route('admin.topup-packages.create') }}" class="px-4 py-2 bg-[#0194F3] text-white rounded-lg font-medium hover:bg-blue-600 transition flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Paket
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-800 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Paket (Jumlah Listing)</th>
                        <th class="px-6 py-4">Harga</th>
                        <th class="px-6 py-4">Bonus Text</th>
                        <th class="px-6 py-4">Diskon Label</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($packages as $pkg)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $pkg->amount }} Listing</td>
                            <td class="px-6 py-4 text-emerald-600 font-bold">Rp {{ number_format($pkg->price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $pkg->bonus ? '+' . $pkg->bonus . ' Iklan' : '-' }}</td>
                            <td class="px-6 py-4">
                                @if($pkg->discount_label)
                                    <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded">{{ $pkg->discount_label }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($pkg->is_active)
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full font-medium">Aktif</span>
                                @else
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded-full font-medium">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.topup-packages.edit', $pkg->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg text-xs font-semibold transition">
                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i> Edit
                                </a>
                                <form action="{{ route('admin.topup-packages.destroy', $pkg->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus paket ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-semibold transition ml-2">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                Belum ada paket Top Up.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
