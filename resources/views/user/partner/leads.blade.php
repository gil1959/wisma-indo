@extends('user.layouts.app')
@section('title', 'Lead Pembeli')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Lead Pembeli</h2>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Properti</th>
                        <th class="px-6 py-4 font-semibold">Kontak</th>
                        <th class="px-6 py-4 font-semibold">Pesan</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                @if($lead->listing)
                                    <a href="{{ route('listing.show', $lead->listing->slug) }}" class="font-bold text-[#0194F3] hover:underline" target="_blank">{{ $lead->listing->title }}</a>
                                @else
                                    <span class="font-bold text-slate-500 text-xs">Pencarian Umum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 flex items-center gap-2">
                                    {{ $lead->name }}
                                    @if($lead->source == 'referral_admin')
                                        <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[10px] rounded flex items-center gap-0.5" title="Referral Prioritas dari Pusat"><i data-lucide="star" class="w-2.5 h-2.5"></i> Pusat</span>
                                    @endif
                                </div>
                                <div class="text-slate-500 text-xs">{{ $lead->phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-600 max-w-xs truncate" title="{{ $lead->message }}">{{ $lead->message }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                                    {{ $lead->status === 'Closing' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 
                                      ($lead->status === 'Lead Baru' ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-blue-50 text-blue-600 border border-blue-200') }}">
                                    {{ $lead->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $lead->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $lead->phone)) }}?text=Halo%20{{ urlencode($lead->name) }}%2C%20saya%20menghubungi%20Anda%20terkait%20minat%20Anda%20pada%20properti%20*{{ urlencode($lead->listing ? $lead->listing->title : 'WismaIndo') }}*%20di%20Wismaindo." target="_blank" class="inline-flex items-center justify-center h-8 px-3 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white transition text-xs font-bold" title="Hubungi Pembeli">
                                        <i data-lucide="message-circle" class="w-3.5 h-3.5 mr-1"></i> WA
                                    </a>
                                    <a href="{{ route('partner.leads.show', $lead->id) }}" class="inline-flex items-center justify-center h-8 px-3 rounded-lg bg-[#0194F3]/10 text-[#0194F3] hover:bg-[#0194F3] hover:text-white transition text-xs font-bold">
                                        <i data-lucide="eye" class="w-3.5 h-3.5 mr-1"></i> Detail CRM
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada lead pembeli.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
