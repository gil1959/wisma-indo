@extends('layouts.admin')

@section('title', 'Data Top Up Saldo')
@section('page-title', 'Top Up Saldo')

@section('content')
<div x-data="{ 
    rejectModal: false,
    approveModal: false,
    deleteModal: false,
    rejectNote: '',
    formToSubmit: null,

    openReject(form) {
        this.formToSubmit = form;
        this.rejectNote = '';
        this.rejectModal = true;
    },
    submitReject() {
        if (!this.rejectNote.trim()) return;
        let hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'note';
        hidden.value = this.rejectNote;
        this.formToSubmit.appendChild(hidden);
        this.formToSubmit.submit();
    },
    cancelReject() {
        this.rejectModal = false;
    },

    openApprove(form) {
        this.formToSubmit = form;
        this.approveModal = true;
    },
    submitApprove() {
        this.formToSubmit.submit();
    },

    openDelete(form) {
        this.formToSubmit = form;
        this.deleteModal = true;
    },
    submitDelete() {
        this.formToSubmit.submit();
    }
}" class="card p-0 overflow-hidden bg-white shadow rounded-2xl">
    <div class="px-5 py-4 border-b bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-sm font-extrabold text-slate-900">Semua Permintaan Top Up</div>
            <div class="text-xs text-slate-600 mt-1">Kelola transaksi pembelian saldo / kuota oleh user.</div>
        </div>
        
        <form action="{{ route('admin.topups.index') }}" method="GET" class="flex items-center gap-2">
            <select name="status" class="text-sm rounded-xl border-slate-200" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </form>
    </div>
    
    @if(session('success'))
    <div class="m-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="p-0 overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b text-xs font-extrabold text-slate-700">
                <tr>
                    <th class="px-5 py-3">ID</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Kuota/Nominal</th>
                    <th class="px-5 py-3">Harga</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($topups as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3">#{{ $item->id }}</td>
                    <td class="px-5 py-3">{{ $item->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-5 py-3 font-medium">{{ $item->user->name ?? 'Unknown' }}</td>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-900">{{ $item->amount }}</span> 
                        <span class="text-xs text-slate-500 uppercase">{{ $item->quota_type }}</span>
                    </td>
                    <td class="px-5 py-3 font-semibold text-emerald-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        @if($item->status == 'success')
                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Sukses</span>
                        @elseif($item->status == 'pending')
                            <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">Pending</span>
                        @else
                            <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-xs font-bold">Gagal</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        @if($item->payment_proof)
                        <a href="{{ asset($item->payment_proof) }}" target="_blank" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition mr-1" title="Lihat Bukti Transfer">
                            <i data-lucide="image" class="w-4 h-4"></i>
                        </a>
                        @endif
                        
                        @if($item->status == 'pending')
                            <form action="{{ route('admin.topups.update', $item->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="success">
                                <button type="button" @click="openApprove($el.closest('form'))" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition mr-1" title="Approve Transaksi">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.topups.update', $item->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="failed">
                                <button type="button" @click="openReject($el.closest('form'))" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition mr-1" title="Reject Transaksi">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.topups.destroy', $item->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="openDelete($el.closest('form'))" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus Transaksi">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="credit-card" class="w-10 h-10 text-slate-300 mb-2"></i>
                            <div>Belum ada transaksi top up.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($topups->hasPages())
    <div class="p-5 border-t">
        {{ $topups->links() }}
    </div>
    @endif

    {{-- MODAL REJECT --}}
    <template x-teleport="body">
        <div x-show="rejectModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div x-show="rejectModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="cancelReject()"></div>
            <div x-show="rejectModal" x-transition class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 z-10 text-left">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-slate-800">Tolak Top Up</h3>
                    <button type="button" @click="cancelReject()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <p class="text-slate-500 mb-4 text-sm">Berikan alasan penolakan agar user mengetahui kesalahannya.</p>
                <textarea x-model="rejectNote" rows="3" class="w-full rounded-xl border-slate-200 mb-4 text-sm focus:border-orange-500 focus:ring focus:ring-orange-500/20" placeholder="Contoh: Bukti transfer buram, nominal tidak sesuai..."></textarea>
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="cancelReject()" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="button" @click="submitReject()" class="px-4 py-2 rounded-xl font-bold bg-orange-500 text-white hover:bg-orange-600 transition" :disabled="!rejectNote.trim()">Tolak Top Up</button>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL APPROVE --}}
    <template x-teleport="body">
        <div x-show="approveModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div x-show="approveModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="approveModal = false"></div>
            <div x-show="approveModal" x-transition class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center z-10">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Approve Top Up?</h3>
                <p class="text-slate-500 mb-6 text-sm">Kuota atau saldo akan ditambahkan ke akun user secara otomatis.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="approveModal = false" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="button" @click="submitApprove()" class="px-4 py-2 rounded-xl font-bold bg-emerald-500 text-white hover:bg-emerald-600 transition">Ya, Approve</button>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL DELETE --}}
    <template x-teleport="body">
        <div x-show="deleteModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div x-show="deleteModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteModal = false"></div>
            <div x-show="deleteModal" x-transition class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center z-10">
                <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Hapus Transaksi?</h3>
                <p class="text-slate-500 mb-6 text-sm">Data transaksi ini akan dihapus secara permanen dari sistem.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="deleteModal = false" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="button" @click="submitDelete()" class="px-4 py-2 rounded-xl font-bold bg-rose-600 text-white hover:bg-rose-700 transition">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
