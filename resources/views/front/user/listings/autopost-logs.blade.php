@php $isPartner = auth()->check() && auth()->user()->hasRole('partner'); @endphp
@extends($isPartner ? 'user.layouts.app' : 'layouts.front')

@section('content')
@if(!$isPartner)
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
@endif
    <div class="mx-auto w-full">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <h1 class="text-3xl font-bold text-slate-800">Riwayat Autopost</h1>
            <a href="{{ route('iklan.saya') }}" class="px-5 py-2 bg-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-300 transition">Kembali ke Iklan Saya</a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase">Waktu / Jadwal</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase">Iklan</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase">Platform</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase">Status</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr>
                                <td class="p-4 font-mono text-sm text-slate-600">{{ $log->scheduled_at->format('d M Y H:i') }}</td>
                                <td class="p-4 text-sm font-bold text-slate-800 truncate max-w-[200px]">{{ $log->listing->title ?? 'Iklan Dihapus' }}</td>
                                <td class="p-4 text-sm">
                                    @foreach($log->platforms as $plat)
                                        <span class="inline-block px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-[10px] font-bold uppercase mr-1">{{ str_replace('_', ' ', $plat) }}</span>
                                    @endforeach
                                </td>
                                <td class="p-4 text-sm font-bold">
                                    @if($log->status === 'success')
                                        <span class="text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md text-xs">Sukses</span>
                                    @elseif($log->status === 'failed')
                                        <span class="text-red-600 bg-red-50 px-2 py-1 rounded-md text-xs">Gagal</span>
                                    @elseif($log->status === 'processing')
                                        <span class="text-yellow-600 bg-yellow-50 px-2 py-1 rounded-md text-xs">Diproses</span>
                                    @else
                                        <span class="text-slate-600 bg-slate-100 px-2 py-1 rounded-md text-xs">Menunggu</span>
                                    @endif
                                </td>
                                <td class="p-4 text-xs text-slate-500 max-w-[250px] truncate" title="{{ $log->error_message }}">{{ $log->error_message ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500">Belum ada riwayat autopost.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@if(!$isPartner)
    </div>
</div>
@endif
@endsection
