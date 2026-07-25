@extends('layouts.admin')
@section('title', 'Distribusi Lead (Referral)')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Distribusi Lead (Referral)</h1>
        <p class="text-slate-500 text-sm mt-1">Kelola dan distribusikan calon pembeli ke Partner.</p>
    </div>
    <a href="{{ route('admin.referrals.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#0194F3] hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Buat Referral Lead
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">Pembeli</th>
                    <th class="px-6 py-4">Kontak</th>
                    <th class="px-6 py-4">Partner Tujuan</th>
                    <th class="px-6 py-4">Status / Progres</th>
                    <th class="px-6 py-4">Tgl Dibuat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($leads as $lead)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-800">{{ $lead->name }}</p>
                        @if($lead->listing_id && $lead->listing)
                            <a href="{{ route('listing.show', $lead->listing->slug) }}" target="_blank" class="text-xs text-[#0194F3] hover:underline flex items-center gap-1 mt-1">
                                <i data-lucide="home" class="w-3 h-3"></i> {{ Str::limit($lead->listing->title, 30) }}
                            </a>
                        @else
                            <span class="text-xs text-slate-400 mt-1 block">Lead Umum</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 text-slate-600 mb-1">
                            <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="hover:text-[#0194F3]">{{ $lead->phone }}</a>
                        </div>
                        @if($lead->email)
                        <div class="flex items-center gap-2 text-slate-500 text-xs">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                            {{ $lead->email }}
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 overflow-hidden shrink-0">
                                @if($lead->partner->avatar)
                                    <img src="{{ asset($lead->partner->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="user" class="w-3 h-3"></i>
                                @endif
                            </div>
                            <span class="font-medium text-slate-700">{{ $lead->partner->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold
                            {{ $lead->status == 'Closing' ? 'bg-green-100 text-green-700' : 
                              ($lead->status == 'Lead Baru' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $lead->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500">
                        {{ $lead->created_at->format('d M Y, H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto text-slate-300 mb-3"></i>
                        <p class="font-medium">Belum ada Referral Lead</p>
                        <p class="text-sm">Klik 'Buat Referral Lead' untuk membagikan lead ke Partner.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($leads->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $leads->links() }}
    </div>
    @endif
</div>
@endsection
