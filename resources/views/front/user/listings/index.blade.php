@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <h1 class="text-3xl font-bold text-slate-800">Iklan Saya</h1>
            
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-pasang-iklan'))" class="px-6 py-2 bg-[#0194F3] text-white font-bold rounded-xl hover:bg-blue-600 transition flex items-center gap-2">
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
                                    <a href="{{ route('iklan.saya.edit', $item->id) }}" class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-[#0194F3] hover:text-white transition" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('iklan.saya.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus iklan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-rose-500 hover:text-white transition" title="Hapus">
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
</div>
@endsection
