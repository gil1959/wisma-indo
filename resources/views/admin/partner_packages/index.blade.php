@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Paket Bulanan Partner</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola daftar paket langganan bulanan untuk Agen Partner.</p>
        </div>
        <a href="{{ route('admin.partner_packages.create') }}" class="px-4 py-2 bg-[#0194F3] text-white rounded-lg font-medium hover:bg-blue-600 transition flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Paket
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 text-red-600 rounded-lg border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form Khusus Paket Free --}}
    <div class="mb-8 bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Konfigurasi Paket Free (Default)</h2>
                <p class="text-sm text-slate-500 mt-1">Paket ini otomatis aktif saat akun partner disetujui. Kuota di-reset mengikuti jadwal billing langganan ini.</p>
            </div>
            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Paket Otomatis</span>
        </div>
        
        <form action="{{ route('admin.partner_packages.update_free') }}" method="POST" class="flex gap-4 items-end">
            @csrf
            <div class="w-64">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Kuota Pasang Iklan</label>
                <div class="relative">
                    <input type="number" name="listing_quota" value="{{ $freePackage->listing_quota }}" class="w-full rounded-lg border-slate-200 pr-16 focus:ring focus:ring-blue-200" required>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Listing</span>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg font-medium hover:bg-slate-700 transition">
                Simpan Kuota Free
            </button>
        </form>
    </div>

    {{-- Daftar Paket Berbayar --}}
    <div x-data="{ 
        deleteModal: false,
        formToSubmit: null,
        openDelete(form) {
            this.formToSubmit = form;
            this.deleteModal = true;
        },
        submitDelete() {
            if(this.formToSubmit) this.formToSubmit.submit();
        }
    }" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 font-semibold text-slate-700">Nama Paket</th>
                        <th class="px-6 py-4 font-semibold text-slate-700">Harga</th>
                        <th class="px-6 py-4 font-semibold text-slate-700">Benefit Tambahan</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($packages as $p)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800 flex items-center gap-2">
                                {{ $p->name }}
                                @if(!$p->is_active)
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded text-[10px] font-bold uppercase tracking-wider">Draft</span>
                                @endif
                                @if($p->is_voucher)
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-[10px] font-bold uppercase tracking-wider">Voucher</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 mt-1 truncate max-w-xs">{{ Str::limit($p->description, 50) }}</div>
                            <div class="text-xs text-slate-500 mt-1">Kuota: <strong class="text-slate-700">{{ $p->listing_quota == -1 ? 'Unlimited' : $p->listing_quota }}</strong> &bull; Durasi: <strong class="text-slate-700">{{ $p->duration_days }} Hari</strong></div>
                        </td>
                        <td class="px-6 py-4">
                            @if($p->discount_label)
                                <div class="inline-block px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-bold mb-1">{{ $p->discount_label }}</div>
                            @endif
                            <div class="font-bold text-[#0194F3]">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                            @if($p->original_price)
                                <div class="text-xs text-slate-400 line-through mt-0.5">Rp {{ number_format($p->original_price, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            @if($p->bonus)
                                <div class="text-sm"><span class="font-bold text-emerald-600">+{{ $p->bonus }}</span> Bonus Iklan</div>
                            @endif
                            @if($p->is_voucher && $p->valid_until)
                                <div class="text-xs text-orange-600 mt-1">
                                    Berlaku s/d {{ \Carbon\Carbon::parse($p->valid_until)->format('d M Y H:i') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.partner_packages.edit', $p) }}" class="p-2 text-slate-400 hover:text-blue-500 transition rounded-lg hover:bg-blue-50">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.partner_packages.destroy', $p) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="openDelete($el.closest('form'))" class="p-2 text-slate-400 hover:text-red-500 transition rounded-lg hover:bg-red-50">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            Belum ada paket berbayar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL DELETE --}}
        <template x-teleport="body">
            <div x-show="deleteModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
                <div x-show="deleteModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteModal = false"></div>
                <div x-show="deleteModal" x-transition class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center z-10">
                    <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="trash-2" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Hapus Paket Ini?</h3>
                    <p class="text-slate-500 mb-6 text-sm">Paket yang dihapus tidak dapat dikembalikan lagi. Tindakan ini bersifat permanen.</p>
                    <div class="flex gap-3 justify-center">
                        <button type="button" @click="deleteModal = false" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                        <button type="button" @click="submitDelete()" class="px-4 py-2 rounded-xl font-bold bg-red-500 text-white hover:bg-red-600 transition">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
