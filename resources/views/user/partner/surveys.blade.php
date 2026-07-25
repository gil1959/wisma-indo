@extends('user.layouts.app')
@section('title', 'Jadwal Survey')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Jadwal Survey</h2>
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
                        <th class="px-6 py-4 font-semibold">Waktu Survey</th>
                        <th class="px-6 py-4 font-semibold">Pesan</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($surveys as $survey)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('listing.show', $survey->listing->slug) }}" class="font-bold text-[#0194F3] hover:underline" target="_blank">{{ $survey->listing->title }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $survey->name }}</div>
                                <div class="text-slate-500 text-xs">{{ $survey->phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($survey->survey_date)->format('d M Y') }}</div>
                                <div class="text-slate-500 text-xs">{{ $survey->survey_time }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-600 max-w-xs truncate" title="{{ $survey->message }}">{{ $survey->message ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                                    {{ $survey->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-200' : '' }}
                                    {{ $survey->status === 'confirmed' ? 'bg-blue-50 text-blue-600 border border-blue-200' : '' }}
                                    {{ $survey->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : '' }}
                                    {{ $survey->status === 'cancelled' ? 'bg-red-50 text-red-600 border border-red-200' : '' }}">
                                    {{ $survey->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $survey->phone)) }}?text=Halo%20{{ urlencode($survey->name) }}%2C%20saya%20menghubungi%20Anda%20terkait%20jadwal%20survey%20Anda%20pada%20properti%20*{{ urlencode($survey->listing->title) }}*%20di%20Wismaindo." target="_blank" class="inline-flex items-center justify-center h-8 px-3 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white transition text-xs font-bold" title="Hubungi Pembeli">
                                        <i data-lucide="message-circle" class="w-3.5 h-3.5 mr-1"></i> WA
                                    </a>
                                    <form action="{{ route('partner.surveys.status', $survey->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="text-sm border-slate-200 rounded-lg p-1.5 focus:ring focus:ring-blue-200 focus:border-blue-400" style="padding-right: 2rem;">
                                            <option value="pending" {{ $survey->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="confirmed" {{ $survey->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="completed" {{ $survey->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ $survey->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada jadwal survey.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($surveys->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $surveys->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
