@extends('user.layouts.app')
@section('title', 'Statistik')

@section('content')
<div class="space-y-6">
    @if(auth()->user()->is_suspended && auth()->user()->suspended_until > now())
    <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-r-xl shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0 text-red-500">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-red-800 font-bold text-lg mb-1">Akun Partner Disuspend</h3>
                <p class="text-red-600">Akun Anda sedang dalam masa penangguhan (suspend) hingga <strong>{{ auth()->user()->suspended_until->format('d M Y H:i') }}</strong>. Anda tidak dapat menggunakan fitur partner lainnya (pasang iklan, melihat lead, jadwal survey, dll) selama masa ini.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Statistik Performa</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#0194F3] flex items-center justify-center shrink-0">
                <i data-lucide="home" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-semibold">Total Iklan</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($totalListings) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <i data-lucide="eye" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-semibold">Total Dilihat</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($totalViews) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-semibold">Total Leads</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($totalLeads) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-semibold">Closing</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($closedLeads) }}</p>
            </div>
        </div>
    </div>
    
    <div class="flex flex-col lg:flex-row gap-6 mt-8">
        {{-- Chart Section --}}
        <div class="w-full lg:w-2/3 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 min-w-0">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 text-lg">Statistik Interaksi</h3>
                <span class="text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1 rounded-full">Bulan Ini</span>
            </div>
            <div class="h-[300px] w-full relative">
                <canvas id="leadsChart"></canvas>
            </div>
        </div>

        {{-- Top Listings Section --}}
        <div class="w-full lg:w-1/3 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 min-w-0">
            <h3 class="font-bold text-slate-800 text-lg mb-6">Iklan Terpopuler</h3>
            <div class="space-y-4">
                @forelse($topListings as $listing)
                <a href="{{ route('iklan.saya.edit', $listing->id) }}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition group">
                    <div class="w-14 h-14 rounded-lg overflow-hidden shrink-0 bg-slate-100">
                        @if($listing->primary_image)
                            <img src="{{ asset($listing->primary_image) }}" alt="Iklan" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                <i data-lucide="image" class="w-6 h-6"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-sm truncate group-hover:text-[#0194F3] transition">{{ $listing->title }}</h4>
                        <div class="text-xs text-slate-500 flex items-center gap-3 mt-1">
                            <span class="flex items-center gap-1"><i data-lucide="eye" class="w-3 h-3"></i> {{ number_format($listing->views) }}</span>
                            <span class="flex items-center gap-1 text-emerald-600 font-medium capitalize">{{ $listing->status }}</span>
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center py-8 text-slate-500">
                    <i data-lucide="inbox" class="w-10 h-10 mx-auto text-slate-300 mb-3"></i>
                    <p class="text-sm">Belum ada iklan.</p>
                </div>
                @endforelse
            </div>
            
            @if($topListings->count() > 0)
            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('iklan.saya') }}" class="text-[#0194F3] text-sm font-bold hover:underline">Lihat Semua Iklan</a>
            </div>
            @endif
        </div>
    </div>

    {{-- Recent Leads Section --}}
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-slate-800 text-lg">Leads Terbaru</h3>
            <a href="{{ route('partner.leads') }}" class="text-sm font-bold text-[#0194F3] hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b text-xs font-extrabold text-slate-700 uppercase">
                    <tr>
                        <th class="px-6 py-4">Nama Pembeli</th>
                        <th class="px-6 py-4">Properti (Iklan)</th>
                        <th class="px-6 py-4">No. HP</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        // Simulate some recent leads for the dashboard
                        $recentLeads = \App\Models\BuyerLead::where('partner_id', auth()->id())
                            ->with('listing')
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse($recentLeads as $lead)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $lead->name }}</td>
                        <td class="px-6 py-4">
                            @if($lead->listing_id)
                                <a href="{{ route('iklan.saya.edit', $lead->listing_id) }}" class="text-[#0194F3] hover:underline truncate max-w-[200px] inline-block">
                                    {{ $lead->listing->title ?? 'Iklan Dihapus' }}
                                </a>
                            @else
                                <span class="text-slate-500 font-semibold text-xs">Pencarian Umum</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $lead->phone }}</td>
                        <td class="px-6 py-4 text-xs text-slate-500">{{ $lead->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4">
                            @if($lead->status === 'new')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-bold">Baru</span>
                            @elseif($lead->status === 'contacted')
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">Dihubungi</span>
                            @elseif($lead->status === 'survey')
                                <span class="px-2.5 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs font-bold">Survey</span>
                            @elseif(str_contains($lead->status, 'closed'))
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">Closing</span>
                            @else
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold capitalize">{{ str_replace('_', ' ', $lead->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="inbox" class="w-8 h-8 text-slate-300 mb-2"></i>
                                Belum ada prospek/leads yang masuk.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('leadsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                datasets: [{
                    label: 'Jadwal Survey',
                    data: {!! json_encode($chartData['surveys']) !!},
                    borderColor: '#0194F3',
                    backgroundColor: 'rgba(1, 148, 243, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Leads (Prospek)',
                    data: {!! json_encode($chartData['leads']) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5],
                            color: '#e2e8f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
