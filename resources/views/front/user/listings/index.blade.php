@php $isPartner = auth()->check() && auth()->user()->hasRole('partner'); @endphp
@extends($isPartner ? 'user.layouts.app' : 'layouts.front')

@section('content')
@if(!$isPartner)
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
@endif
@php
    $hasMetaAccount = \App\Models\SocialAccount::where('user_id', auth()->id())->where('provider', 'meta')->exists();
@endphp
<div x-data="{ 
    deleteModal: false, 
    pasangIklanModal: false,
    selectedCategory: 'properti',
    formToSubmit: null, 
    openDelete(form) { this.formToSubmit = form; this.deleteModal = true; }, 
    submitDelete() { this.formToSubmit.submit(); },

    autopostModal: false,
    hasMeta: {{ $hasMetaAccount ? 'true' : 'false' }},
    autopostForm: {
        listing_id: null,
        title: '',
        media_url: '',
        caption: '',
        platforms: ['fb_page'],
        is_scheduled: false,
        scheduled_at: ''
    },
    openAutopost(id, title, img) {
        this.autopostForm.listing_id = id;
        this.autopostForm.title = title;
        this.autopostForm.media_url = img;
        this.autopostForm.caption = '';
        this.autopostForm.platforms = ['fb_page'];
        this.autopostForm.is_scheduled = false;
        this.autopostForm.scheduled_at = '';
        this.autopostModal = true;
        
        setTimeout(() => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }, 100);
    },
    generateCaption() {
        if (!this.autopostForm.listing_id) return;
        this.autopostForm.caption = 'Sedang membuat caption dengan AI... mohon tunggu.';
        fetch('{{ route('autopost.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ listing_id: this.autopostForm.listing_id })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                this.autopostForm.caption = data.data;
            } else {
                this.autopostForm.caption = 'Gagal: ' + data.message;
            }
        })
        .catch(err => {
            this.autopostForm.caption = 'Gagal terhubung ke AI server.';
        });
    }
}">
    <div class="mx-auto w-full">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <h1 class="text-3xl font-bold text-slate-800">Iklan Saya</h1>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('autopost.logs') }}" class="px-5 py-2 bg-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-300 transition flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4"></i> Riwayat Autopost
                </a>
                <a href="{{ route('bulk-uploads.index') }}" class="px-5 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition flex items-center gap-2">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i> Upload Massal
                </a>
                <button type="button" @click="pasangIklanModal = true" class="px-6 py-2 bg-[#0194F3] text-white font-bold rounded-xl hover:bg-blue-600 transition flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> Pasang Iklan Baru
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 font-medium">
            {{ session('error') }}
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
                                    <button type="button" @click="openAutopost({{ $item->id }}, '{{ addslashes($item->title) }}', '{{ asset($item->cover_image) }}')" class="inline-flex items-center justify-center h-9 px-3 rounded-xl bg-orange-100 text-orange-700 hover:bg-orange-200 hover:text-orange-900 transition text-xs font-bold" title="Autopost FB/IG">
                                        <i data-lucide="share-2" class="w-4 h-4 mr-1"></i> Autopost FB/IG
                                    </button>
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
    {{-- MODAL AUTOPOST FB/IG --}}
    <template x-teleport="body">
        <div x-show="autopostModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div x-show="autopostModal" x-transition.opacity class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="autopostModal = false"></div>
            
            <div x-show="autopostModal" x-transition class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden z-10 flex flex-col max-h-[90vh]">
                
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-white">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="share-2" class="w-5 h-5 text-[#0194F3]"></i> Autopost Media Sosial
                    </h3>
                    <button type="button" @click="autopostModal = false" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-xl p-2 transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 bg-slate-50">
                    <div x-show="!hasMeta">
                        <div class="p-12 text-center flex flex-col items-center justify-center min-h-[400px]">
                            <div class="w-24 h-24 bg-blue-50 text-[#0194F3] rounded-full flex items-center justify-center mb-6">
                                <svg class="w-12 h-12 fill-current" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-800 mb-3">Hubungkan Akun Meta</h2>
                            <p class="text-slate-500 mb-8 max-w-md mx-auto">Untuk menggunakan fitur Autopost, Anda harus menghubungkan halaman Facebook (FB Page) atau Instagram Business Anda terlebih dahulu.</p>
                            <a href="{{ route('autopost.auth.meta') }}" class="px-8 py-4 bg-[#1877F2] text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition flex items-center gap-3">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg> Hubungkan Facebook & Instagram
                            </a>
                        </div>
                    </div>

                    <form x-show="hasMeta" action="{{ route('autopost.publish') }}" method="POST" class="flex flex-col lg:flex-row h-full">
                        @csrf
                        <input type="hidden" name="listing_id" x-model="autopostForm.listing_id">
                            
                            {{-- LEFT COLUMN: SETTINGS --}}
                            <div class="flex-1 p-6 lg:p-8 overflow-y-auto lg:border-r border-slate-200 bg-white">
                                
                                <div class="mb-6">
                                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">URL Media / Gambar Iklan</label>
                                    <input type="url" name="media_url" x-model="autopostForm.media_url" class="w-full rounded-xl border-slate-300 text-sm focus:border-[#0194F3] bg-slate-50" required>
                                    <p class="text-[10px] text-slate-400 mt-1">Anda dapat mengganti URL ini dengan link gambar eksternal.</p>
                                </div>

                                <div class="mb-6">
                                    <div class="flex justify-between items-end mb-2">
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Caption (Teks Postingan)</label>
                                        <button type="button" @click="generateCaption()" class="text-xs font-bold bg-purple-50 text-purple-600 px-3 py-1.5 rounded-lg border border-purple-200 hover:bg-purple-100 transition flex items-center gap-1">
                                            <i data-lucide="sparkles" class="w-3 h-3"></i> AI Enhance Caption
                                        </button>
                                    </div>
                                    <textarea name="caption" x-model="autopostForm.caption" rows="8" class="w-full rounded-xl border-slate-300 text-sm focus:border-[#0194F3] leading-relaxed" required placeholder="Tulis caption memikat di sini..."></textarea>
                                </div>

                                <div class="mb-8">
                                    <label class="block text-xs font-bold text-slate-500 mb-3 uppercase tracking-wide">Target Platform Publikasi</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-colors" :class="autopostForm.platforms.includes('fb_page') ? 'border-[#1877F2] bg-blue-50' : 'border-slate-200 hover:border-slate-300'">
                                            <input type="checkbox" name="platforms[]" value="fb_page" x-model="autopostForm.platforms" class="hidden">
                                            <svg class="w-5 h-5 fill-current" :class="autopostForm.platforms.includes('fb_page') ? 'text-[#1877F2]' : 'text-slate-400'" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                                            <span class="text-sm font-bold" :class="autopostForm.platforms.includes('fb_page') ? 'text-[#1877F2]' : 'text-slate-600'">Facebook Page</span>
                                        </label>
                                        <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-colors" :class="autopostForm.platforms.includes('ig_business') ? 'border-pink-500 bg-pink-50' : 'border-slate-200 hover:border-slate-300'">
                                            <input type="checkbox" name="platforms[]" value="ig_business" x-model="autopostForm.platforms" class="hidden">
                                            <svg class="w-5 h-5 fill-current" :class="autopostForm.platforms.includes('ig_business') ? 'text-pink-500' : 'text-slate-400'" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                            <span class="text-sm font-bold" :class="autopostForm.platforms.includes('ig_business') ? 'text-pink-600' : 'text-slate-600'">Instagram</span>
                                        </label>
                                        <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-colors" :class="autopostForm.platforms.includes('threads') ? 'border-black bg-slate-100' : 'border-slate-200 hover:border-slate-300'">
                                            <input type="checkbox" name="platforms[]" value="threads" x-model="autopostForm.platforms" class="hidden">
                                            <svg class="w-5 h-5" :class="autopostForm.platforms.includes('threads') ? 'text-black' : 'text-slate-400'" viewBox="0 0 24 24" fill="currentColor"><path d="M14.28 11.08c-.7-1.12-2.12-1.39-3.48-.95-1.57.51-2.43 2.05-2.02 3.65.34 1.35 1.57 2.19 2.91 2.12 1.34-.07 2.22-1.11 2.37-2.31h2.5c-.26 2.45-2 4.39-4.5 4.57-2.58.19-4.9-1.33-5.58-3.79-.76-2.73.91-5.63 3.69-6.26 2.07-.47 4.1.28 5.25 1.96.64.93.97 2.08 1 3.23v.9h-5.2c.11.96.94 1.63 1.93 1.58.91-.04 1.57-.61 1.76-1.42h2.24c-.11.66-.46 1.48-1.07 2.22l-1.8.55Z"/></svg>
                                            <span class="text-sm font-bold" :class="autopostForm.platforms.includes('threads') ? 'text-black' : 'text-slate-600'">Threads</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 mb-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-sm">Jadwalkan Opsi Posting</h4>
                                            <p class="text-xs text-slate-500 mt-1">Gunakan jadwal atau post sekarang</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_scheduled" value="1" x-model="autopostForm.is_scheduled" class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0194F3]"></div>
                                        </label>
                                    </div>

                                    <div x-show="autopostForm.is_scheduled" x-transition class="pt-4 border-t border-slate-200">
                                        <label class="block text-xs font-bold text-slate-500 mb-2">Pilih Tanggal & Waktu (WIB)</label>
                                        <input type="datetime-local" name="scheduled_at" x-model="autopostForm.scheduled_at" class="w-full rounded-xl border-slate-300 text-sm focus:border-[#0194F3]" :required="autopostForm.is_scheduled">
                                    </div>
                                </div>

                                <button type="submit" class="w-full py-4 text-white font-bold rounded-xl shadow-lg transition text-base" :class="autopostForm.is_scheduled ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/30' : 'bg-orange-500 hover:bg-orange-600 shadow-orange-500/30'" x-text="autopostForm.is_scheduled ? 'Jadwalkan Posting Ini' : 'Publish Sekali Klik Sekarang'"></button>
                            </div>

                            {{-- RIGHT COLUMN: PREVIEW --}}
                            <div class="w-full lg:w-[400px] bg-slate-100 p-8 flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-[#0194F3]/10 to-purple-500/10"></div>
                                
                                {{-- Phone Mockup --}}
                                <div class="relative w-full max-w-[320px] bg-white rounded-[32px] shadow-2xl border-[6px] border-slate-800 overflow-hidden" style="aspect-ratio: 9/19;">
                                    {{-- Status Bar --}}
                                    <div class="h-6 w-full bg-white flex justify-between items-center px-4 pt-1">
                                        <div class="text-[10px] font-bold">9:41</div>
                                        <div class="flex gap-1">
                                            <i data-lucide="wifi" class="w-3 h-3"></i>
                                            <i data-lucide="battery-full" class="w-3 h-3"></i>
                                        </div>
                                    </div>

                                    {{-- IG Feed Header --}}
                                    <div class="flex items-center gap-3 p-3 border-b border-slate-100">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 overflow-hidden">
                                            <img src="{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=fff&background=0194F3' }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</p>
                                            <p class="text-[11px] text-slate-500 leading-none">Sponsored</p>
                                        </div>
                                    </div>

                                    {{-- Post Image --}}
                                    <div class="w-full aspect-square bg-slate-200">
                                        <img :src="autopostForm.media_url || 'https://placehold.co/600x600/e2e8f0/64748b?text=Image+Preview'" class="w-full h-full object-cover">
                                    </div>

                                    {{-- Post Actions --}}
                                    <div class="flex justify-between items-center px-3 py-2">
                                        <div class="flex gap-3">
                                            <i data-lucide="heart" class="w-5 h-5 text-slate-700"></i>
                                            <i data-lucide="message-circle" class="w-5 h-5 text-slate-700"></i>
                                            <i data-lucide="send" class="w-5 h-5 text-slate-700"></i>
                                        </div>
                                        <i data-lucide="bookmark" class="w-5 h-5 text-slate-700"></i>
                                    </div>

                                    {{-- Caption --}}
                                    <div class="px-3 pb-4">
                                        <p class="text-[13px] text-slate-800 line-clamp-6 whitespace-pre-wrap leading-tight" x-text="autopostForm.caption || 'Teks caption akan muncul di sini...'"></p>
                                    </div>

                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@if(!$isPartner)
    </div>
</div>
@endif
@endsection
