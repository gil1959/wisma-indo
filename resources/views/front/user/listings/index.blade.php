@extends('user.layouts.app')

@section('content')
<div x-data="{ 
    deleteModal: false, 
    pasangIklanModal: false,
    selectedCategory: 'properti',
    formToSubmit: null, 
    openDelete(form) { this.formToSubmit = form; this.deleteModal = true; }, 
    submitDelete() { this.formToSubmit.submit(); } 
}">
    <div class="mx-auto w-full">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <h1 class="text-3xl font-bold text-slate-800">Iklan Saya</h1>
            
            <button type="button" @click="pasangIklanModal = true" class="px-6 py-2 bg-[#0194F3] text-white font-bold rounded-xl hover:bg-blue-600 transition flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Pasang Iklan Baru
            </button>
        </div>

        @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
        @endif
        
        @if(isset($listings) && $listings->count() > 0)
        <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b text-xs font-extrabold text-slate-700 uppercase">
                        <tr>
                            <th class="px-6 py-4">Info Iklan</th>
                            <th class="px-6 py-4">Harga</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($listings as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                                        @if($item->primary_image)
                                            <img src="{{ asset($item->primary_image) }}" alt="Iklan" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <i data-lucide="image" class="w-6 h-6"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-base mb-1">{{ $item->title }}</div>
                                        <div class="text-xs text-slate-500 flex items-center gap-2">
                                            <span class="capitalize px-2 py-0.5 rounded bg-slate-100 font-medium">{{ $item->category }}</span>
                                            <span class="capitalize text-[#0194F3] font-medium">{{ $item->transaction_type ?? 'Jual/Sewa' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->status == 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600">Pending</span>
                                @elseif($item->status == 'tersedia' || $item->status == 'active')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">Aktif</span>
                                @elseif($item->status == 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600">Ditolak</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 capitalize">{{ $item->status }}</span>
                                @endif
                                
                                @if($item->status == 'rejected' && $item->rejection_note)
                                    <div class="mt-2 text-xs text-rose-600 bg-rose-50 p-2 rounded-lg border border-rose-100">
                                        <strong>Alasan Ditolak:</strong><br>
                                        {{ $item->rejection_note }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('listing_promotions.packages', ['listing' => $item->id, 'type' => 'sundul']) }}" class="inline-flex items-center justify-center h-9 px-3 rounded-xl bg-indigo-100 text-indigo-700 hover:bg-indigo-200 hover:text-indigo-900 transition text-xs font-bold" title="Sundulan">
                                        <i data-lucide="arrow-up" class="w-4 h-4 mr-1"></i> Sundulan
                                    </a>
                                    <a href="{{ route('listing_promotions.packages', ['listing' => $item->id, 'type' => 'premium']) }}" class="inline-flex items-center justify-center h-9 px-3 rounded-xl bg-yellow-100 text-yellow-700 hover:bg-yellow-400 hover:text-yellow-900 transition text-xs font-bold" title="Premium">
                                        <i data-lucide="star" class="w-4 h-4 mr-1"></i> Premium
                                    </a>
                                    <a href="{{ route('iklan.saya.edit', $item->id) }}" class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-[#0194F3] hover:text-white transition" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('iklan.saya.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="openDelete($el.closest('form'))" class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-rose-500 hover:text-white transition" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="inbox" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Iklan</h3>
            <p class="text-slate-500 mb-6">Anda belum memasang iklan apapun. Mulai pasang iklan pertama Anda sekarang!</p>
        </div>
        @endif
        
    </div>

    {{-- MODAL DELETE --}}
    <template x-teleport="body">
        <div x-show="deleteModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div x-show="deleteModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteModal = false"></div>
            <div x-show="deleteModal" x-transition class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center z-10">
                <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Hapus Iklan?</h3>
                <p class="text-slate-500 mb-6 text-sm">Data iklan ini akan dihapus secara permanen dari sistem.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="deleteModal = false" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="button" @click="submitDelete()" class="px-4 py-2 rounded-xl font-bold bg-rose-600 text-white hover:bg-rose-700 transition">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL PASANG IKLAN --}}
    <template x-teleport="body">
        <div x-show="pasangIklanModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        
            {{-- OVERLAY --}}
            <div x-show="pasangIklanModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" 
                @click="pasangIklanModal = false"></div>
        
            {{-- MODAL CONTENT --}}
            <div x-show="pasangIklanModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden z-10"
                @click.stop>
                
                <div>
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                        <h3 class="text-xl font-bold text-slate-800">Mau pasang iklan apa?</h3>
                        <button @click="pasangIklanModal = false" class="p-2 text-slate-400 hover:text-slate-600 transition bg-slate-100 rounded-full hover:bg-slate-200 flex items-center justify-center">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                            {{-- Kategori: Properti --}}
                            <div @click="selectedCategory = 'properti'"
                                class="border-2 rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md"
                                :class="selectedCategory === 'properti' ? 'border-[#0194F3] bg-[#0194F3]/5' : 'border-slate-200 hover:border-[#0194F3]/50 bg-white'">
                                <div class="w-12 h-12 rounded-xl bg-blue-100 text-[#0194F3] flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
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
                                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
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
                                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 text-lg mb-1">Jasa</h4>
                                <p class="text-xs font-semibold text-purple-600 mb-2 uppercase tracking-wide">Layanan Profesional</p>
                                <p class="text-sm text-slate-500 leading-relaxed">Tawarkan jasa profesional atau keahlian Anda.</p>
                            </div>

                        </div>
                    </div>

                    <div class="px-6 py-5 border-t border-slate-100 bg-slate-50 flex justify-end">
                        <button type="button" @click="window.location.href = '{{ \Route::has('pasang.iklan') ? route('pasang.iklan') : '#' }}?kategori=' + selectedCategory" 
                            class="px-6 py-3 bg-[#0194F3] hover:bg-blue-600 text-white font-bold rounded-xl shadow-md transition flex items-center gap-2">
                            Lanjutkan
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </template>
</div>
@endsection
