@extends('layouts.admin')

@section('content')
<div class="space-y-5">
    @if(session('success'))
    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
        <div class="font-extrabold">Sukses</div>
        <div class="text-sm mt-1">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
        <div class="font-extrabold">Gagal</div>
        <div class="text-sm mt-1">{{ session('error') }}</div>
    </div>
@endif

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Pengajuan Partner</h1>
            <p class="mt-1 text-sm text-slate-600">Daftar pendaftar partner yang menunggu verifikasi admin.</p>
        </div>

        <form class="flex flex-col sm:flex-row gap-2" method="GET">
            <select name="status"
                    class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-extrabold text-slate-800 bg-white">
                <option value="pending"  {{ $status==='pending'?'selected':'' }}>Pending</option>
                <option value="approved" {{ $status==='approved'?'selected':'' }}>Approved</option>
                <option value="rejected" {{ $status==='rejected'?'selected':'' }}>Rejected</option>
            </select>

            <input name="q" value="{{ $q }}" placeholder="Cari nama/email/telepon"
                   class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 bg-white">

            <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#0194F3;">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Pendaftar</th>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Kontak</th>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Diajukan</th>
                    <th class="text-right px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($applications as $a)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-4 py-3">
                            <div class="font-extrabold text-slate-900">{{ $a->name }}</div>
                            <div class="text-xs text-slate-500 uppercase font-extrabold mt-1">
                                Status: {{ strtoupper($a->status) }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-800">{{ $a->email }}</div>
                            <div class="text-sm text-slate-600">{{ $a->phone ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-700 font-semibold">
                            {{ optional($a->submitted_at)->format('d M Y H:i') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.partners.applications.show', $a->id) }}"
                               class="px-4 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-slate-600" colspan="4">
                            Tidak ada data.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $applications->links() }}</div>
</div>
@endsection
