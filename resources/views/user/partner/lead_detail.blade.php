@extends('user.layouts.app')
@section('title', 'Detail CRM Lead')

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
        <a href="{{ route('partner.leads') }}" class="hover:text-[#0194F3]">Lead Pembeli</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span class="text-slate-800 font-semibold">Detail CRM</span>
    </div>

    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                {{ $lead->name }}
                @if($lead->source == 'referral_admin')
                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs rounded-lg flex items-center gap-1 font-semibold border border-amber-200" title="Referral Prioritas dari Pusat"><i data-lucide="star" class="w-3.5 h-3.5 fill-amber-500 text-amber-500"></i> Referral Pusat</span>
                @endif
            </h2>
            <p class="text-slate-500 mt-1 flex items-center gap-2">
                <i data-lucide="phone" class="w-4 h-4"></i> {{ $lead->phone }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $lead->phone)) }}?text=Halo%20{{ urlencode($lead->name) }}%2C%20saya%20menghubungi%20Anda%20terkait%20minat%20Anda%20pada%20properti%20*{{ urlencode($lead->listing ? $lead->listing->title : 'WismaIndo') }}*%20di%20Wismaindo." target="_blank" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl flex items-center gap-2 transition">
                <i data-lucide="message-circle" class="w-4 h-4"></i> Hubungi Pembeli
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- BAGIAN KIRI: FORM & TIMELINE --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Update Status & Note Box --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-[#0194F3]"></i> Update Progres CRM
                </h3>
                
                <form action="{{ route('partner.leads.activity', $lead->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ubah Status Pipeline</label>
                        <select name="status" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20 font-medium">
                            @foreach(['Lead Baru', 'Follow Up', 'Survey', 'Negosiasi', 'Booking Fee', 'Approved', 'Akad', 'Closing'] as $status)
                                <option value="{{ $status }}" {{ $lead->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan Aktivitas</label>
                        <textarea name="note" rows="3" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="Tulis catatan (contoh: Pembeli setuju untuk survey hari minggu jam 10 pagi...)"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0194F3] hover:bg-blue-600 text-white font-semibold transition flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Aktivitas
                        </button>
                    </div>
                </form>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-[#0194F3]"></i> Riwayat & Catatan
                </h3>
                
                <div class="relative border-l border-slate-200 ml-3 space-y-6 pb-4">
                    @forelse($lead->activities as $activity)
                        <div class="relative pl-6">
                            <div class="absolute -left-1.5 top-1 w-3 h-3 rounded-full bg-[#0194F3] ring-4 ring-white"></div>
                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded capitalize">{{ $activity->status }}</span>
                                    <span class="text-xs text-slate-400 font-medium">{{ $activity->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                @if($activity->note)
                                    <p class="text-sm text-slate-700 leading-relaxed">{{ $activity->note }}</p>
                                @else
                                    <p class="text-sm text-slate-400 italic">Status diubah tanpa catatan.</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="relative pl-6">
                            <div class="absolute -left-1.5 top-1 w-3 h-3 rounded-full bg-slate-300 ring-4 ring-white"></div>
                            <p class="text-sm text-slate-500 italic">Belum ada riwayat aktivitas.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- BAGIAN KANAN: INFO LEAD & PROPERTI --}}
        <div class="space-y-6">
            
            {{-- Info Pembeli --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-4 pb-3 border-b border-slate-100">Informasi Pembeli</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-slate-500 mb-0.5">Nama</p>
                        <p class="font-semibold text-slate-800">{{ $lead->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-0.5">No WhatsApp</p>
                        <p class="font-semibold text-slate-800">{{ $lead->phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-0.5">Email</p>
                        <p class="font-semibold text-slate-800">{{ $lead->email ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-0.5">Pesan Awal</p>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm text-slate-700 mt-1">
                            {{ $lead->message ?: 'Tidak ada pesan awal.' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Properti --}}
            @if($lead->listing)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-4 pb-3 border-b border-slate-100">Properti Diminati</h3>
                <div class="flex gap-3 mb-3">
                    <div class="w-16 h-16 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                        @if($lead->listing->images->count() > 0)
                            <img src="{{ asset($lead->listing->images->first()->image_path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i data-lucide="image" class="w-6 h-6"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-800 line-clamp-2 mb-1">{{ $lead->listing->title }}</h4>
                        <p class="text-xs font-bold text-[#0194F3]">Rp {{ number_format($lead->listing->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                <a href="{{ $lead->listing->url }}" target="_blank" class="block text-center w-full py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl border border-slate-200 transition">
                    Lihat Iklan
                </a>
            </div>
            @endif

        </div>

    </div>
</div>
@endsection
