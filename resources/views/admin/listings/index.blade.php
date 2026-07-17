@extends('layouts.admin')

@section('title', 'Data Iklan Properti')
@section('page-title', 'Iklan Properti')

@section('content')
<div x-data="{ 
    pasangIklanModal: false,
    rejectModal: false,
    approveModal: false,
    deleteModal: false,
    rejectNote: '',
    formToSubmit: null,
    targetSelect: null,

    openReject(form, select = null) {
        this.formToSubmit = form;
        this.targetSelect = select;
        this.rejectNote = '';
        this.rejectModal = true;
    },
    submitReject() {
        if (!this.rejectNote.trim()) return;
        let hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'rejection_note';
        hidden.value = this.rejectNote;
        this.formToSubmit.appendChild(hidden);
        this.formToSubmit.submit();
    },
    cancelReject() {
        if (this.targetSelect) {
            this.targetSelect.value = this.targetSelect.getAttribute('data-original');
        }
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
}" @open-reject-modal="openReject($event.detail.form, $event.detail.select)" class="card p-0 overflow-hidden bg-white shadow rounded-2xl">
    <div class="px-5 py-4 border-b bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-sm font-extrabold text-slate-900">Semua Iklan Properti</div>
            <div class="text-xs text-slate-600 mt-1">Kelola iklan properti yang diposting oleh user.</div>
        </div>
        <div class="flex items-center gap-4 mt-4 sm:mt-0">
            <form action="{{ route('admin.listings.index') }}" method="GET" class="flex items-center gap-2">
                <select name="status" class="text-sm rounded-xl border-slate-200" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="terjual" {{ request('status') == 'terjual' ? 'selected' : '' }}>Terjual</option>
                    <option value="tersewa" {{ request('status') == 'tersewa' ? 'selected' : '' }}>Tersewa</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </form>
            <button @click="pasangIklanModal = true" class="inline-flex items-center gap-2 bg-[#0194F3] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-600 transition shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah
            </button>
        </div>
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
                    <th class="px-5 py-3">Judul / Kategori</th>
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Harga</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($listings as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3">#{{ $item->id }}</td>
                    <td class="px-5 py-3">
                        <div class="font-bold text-slate-900">{{ $item->title }}</div>
                        <div class="text-xs text-slate-500">{{ ucfirst($item->category) }} &bull; {{ ucfirst($item->transaction_type) }}</div>
                    </td>
                    <td class="px-5 py-3 font-medium">{{ $item->user->name ?? 'Unknown' }}</td>
                    <td class="px-5 py-3 font-semibold text-sky-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        <form action="{{ route('admin.listings.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="only_status" value="1">
                            <select name="status" data-original="{{ $item->status }}" class="text-xs rounded-lg border-slate-200 py-1.5 pl-3 pr-8 font-medium bg-white" onchange="handleStatusChange(this)">
                                <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="tersedia" {{ $item->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="rejected" {{ $item->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="terjual" {{ $item->status == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                <option value="tersewa" {{ $item->status == 'tersewa' ? 'selected' : '' }}>Tersewa</option>
                                <option value="nonaktif" {{ $item->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </form>
                    </td>
                    <td class="px-5 py-3 text-right flex justify-end gap-2">
                        @if($item->status === 'pending')
                        <form action="{{ route('admin.listings.update', $item->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="only_status" value="1">
                            <input type="hidden" name="status" value="tersedia">
                            <button type="button" @click="openApprove($el.closest('form'))" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Approve Iklan">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.listings.update', $item->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="only_status" value="1">
                            <input type="hidden" name="status" value="rejected">
                            <button type="button" @click="openReject($el.closest('form'))" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition" title="Tolak Iklan (Beri Catatan)">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('admin.listings.edit', $item->id) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition" title="Edit Iklan">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </a>
                        <form action="{{ route('admin.listings.destroy', $item->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="openDelete($el.closest('form'))" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus Iklan">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mb-2"></i>
                            <div>Belum ada iklan properti.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($listings->hasPages())
    <div class="p-5 border-t">
        {{ $listings->links() }}
    </div>
    @endif

    {{-- MODAL PASANG IKLAN (ADMIN) --}}
    <template x-teleport="body">
        <div x-show="pasangIklanModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div x-show="pasangIklanModal" 
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0"
               x-transition:enter-end="opacity-100"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="opacity-100"
               x-transition:leave-end="opacity-0"
               class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" 
               @click="pasangIklanModal = false"></div>
          
          <div x-show="pasangIklanModal" 
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 translate-y-8 scale-95"
               x-transition:enter-end="opacity-100 translate-y-0 scale-100"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="opacity-100 translate-y-0 scale-100"
               x-transition:leave-end="opacity-0 translate-y-8 scale-95"
               class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden z-10"
               @click.stop>
            
            <div x-data="{ selectedCategory: 'properti' }">
              <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <h3 class="text-xl font-bold text-slate-800">Tambah Iklan Baru</h3>
                <button @click="pasangIklanModal = false" class="p-2 text-slate-400 hover:text-slate-600 transition bg-slate-100 rounded-full hover:bg-slate-200 flex items-center justify-center">
                  <i data-lucide="x" class="w-5 h-5"></i>
                </button>
              </div>
    
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  
                  {{-- Kategori: Properti --}}
                  <div @click="selectedCategory = 'properti'"
                       class="border-2 rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md"
                       :class="selectedCategory === 'properti' ? 'border-[#0194F3] bg-[#0194F3]/5' : 'border-slate-200 hover:border-[#0194F3]/50 bg-white'">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-[#0194F3] flex items-center justify-center mb-4">
                      <i data-lucide="home" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 text-lg mb-1">Properti</h4>
                    <p class="text-xs font-semibold text-[#0194F3] mb-2 uppercase tracking-wide">Iklan Hunian & Tanah</p>
                    <p class="text-sm text-slate-500 leading-relaxed">Jual atau sewa rumah, apartemen, tanah, dll.</p>
                  </div>
    
                  {{-- Kategori: Barang --}}
                  <div @click="selectedCategory = 'barang'"
                       class="border-2 rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md"
                       :class="selectedCategory === 'barang' ? 'border-[#0194F3] bg-[#0194F3]/5' : 'border-slate-200 hover:border-[#0194F3]/50 bg-white'">
                    <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center mb-4">
                      <i data-lucide="package" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 text-lg mb-1">Barang</h4>
                    <p class="text-xs font-semibold text-orange-600 mb-2 uppercase tracking-wide">Perlengkapan</p>
                    <p class="text-sm text-slate-500 leading-relaxed">Jual barang elektronik, otomotif, perabotan.</p>
                  </div>
    
                  {{-- Kategori: Jasa --}}
                  <div @click="selectedCategory = 'jasa'"
                       class="border-2 rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md"
                       :class="selectedCategory === 'jasa' ? 'border-[#0194F3] bg-[#0194F3]/5' : 'border-slate-200 hover:border-[#0194F3]/50 bg-white'">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mb-4">
                      <i data-lucide="briefcase" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 text-lg mb-1">Jasa</h4>
                    <p class="text-xs font-semibold text-purple-600 mb-2 uppercase tracking-wide">Layanan Profesional</p>
                    <p class="text-sm text-slate-500 leading-relaxed">Tawarkan jasa profesional atau keahlian Anda.</p>
                  </div>
    
                </div>
              </div>
    
              <div class="px-6 py-5 border-t border-slate-100 bg-slate-50 flex justify-end">
                <button type="button" @click="window.location.href = '{{ route('admin.listings.create') }}?kategori=' + selectedCategory" 
                  class="px-6 py-3 bg-[#0194F3] hover:bg-blue-600 text-white font-bold rounded-xl shadow-md transition flex items-center gap-2">
                  Lanjutkan
                  <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
              </div>
            </div>
    
          </div>
        </div>
    </template>

    {{-- MODAL REJECT --}}
    <template x-teleport="body">
        <div x-show="rejectModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div x-show="rejectModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="cancelReject()"></div>
            <div x-show="rejectModal" x-transition class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 z-10 text-left">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-slate-800">Tolak Iklan</h3>
                    <button type="button" @click="cancelReject()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <p class="text-slate-500 mb-4 text-sm">Berikan alasan penolakan agar user dapat memperbaiki iklannya.</p>
                <textarea x-model="rejectNote" rows="3" class="w-full rounded-xl border-slate-200 mb-4 text-sm focus:border-orange-500 focus:ring focus:ring-orange-500/20" placeholder="Contoh: Foto kurang jelas, harga tidak masuk akal..."></textarea>
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="cancelReject()" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="button" @click="submitReject()" class="px-4 py-2 rounded-xl font-bold bg-orange-500 text-white hover:bg-orange-600 transition" :disabled="!rejectNote.trim()">Tolak Iklan</button>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL APPROVE --}}
    <template x-teleport="body">
        <div x-show="approveModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div x-show="approveModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="approveModal = false"></div>
            <div x-show="approveModal" x-transition class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center z-10">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Setujui Iklan?</h3>
                <p class="text-slate-500 mb-6 text-sm">Iklan ini akan langsung tersedia dan dapat dilihat oleh publik di website.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="approveModal = false" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="button" @click="submitApprove()" class="px-4 py-2 rounded-xl font-bold bg-[#0194F3] text-white hover:bg-blue-600 transition">Ya, Setujui</button>
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
                <h3 class="text-xl font-bold text-slate-800 mb-2">Hapus Permanen?</h3>
                <p class="text-slate-500 mb-6 text-sm">Data iklan ini akan dihapus secara permanen dari sistem dan tidak bisa dikembalikan.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="deleteModal = false" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="button" @click="submitDelete()" class="px-4 py-2 rounded-xl font-bold bg-rose-600 text-white hover:bg-rose-700 transition">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
function handleStatusChange(select) {
    if (select.value === 'rejected') {
        select.dispatchEvent(new CustomEvent('open-reject-modal', { bubbles: true, detail: { form: select.form, select: select } }));
        return;
    }
    
    select.form.submit();
}
</script>
@endpush
