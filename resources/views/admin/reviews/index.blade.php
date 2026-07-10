@extends('layouts.admin')

@section('title', 'Komentar Paket')
@section('page-title', 'Komentar Paket')

@section('content')
<div class="space-y-5">

    {{-- Header + Tabs --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm font-extrabold text-slate-900">Komentar Paket (Reviews)</div>
            <div class="mt-1 text-xs text-slate-500">Moderasi komentar: approve / reject / hapus.</div>
        </div>
        


        @php
            $current = $status ?? request('status', 'pending');
            $tabs = [
                'pending'  => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ];
            
        @endphp
<a href="{{ route('admin.reviews.create') }}"
   class="inline-flex items-center justify-center rounded-xl bg-[#0194F3] px-4 py-2 text-sm font-bold text-white hover:opacity-95">
    + Tambah Review
</a>
        <div class="inline-flex w-full sm:w-auto rounded-2xl border border-slate-200 bg-white p-1 shadow-sm">
            @foreach($tabs as $k => $label)
                <a href="{{ route('admin.reviews.index', ['status' => $k]) }}"
                   class="flex-1 sm:flex-none text-center px-4 py-2 rounded-xl text-xs font-extrabold transition
                   {{ $current === $k ? 'bg-[#0194F3] text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <div class="text-sm font-extrabold text-emerald-800">Berhasil</div>
            <div class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Card --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Card header --}}
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
            <div class="text-sm font-extrabold text-slate-900">Daftar Reviews</div>
            <div class="text-xs text-slate-500">
                Total: <span class="font-extrabold text-slate-800">{{ $reviews->total() }}</span>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-[11px] font-extrabold text-slate-600">
                        <th class="px-5 py-3">Item</th>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Rating</th>
                        <th class="px-5 py-3">Ulasan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 whitespace-nowrap">Waktu</th>
                        <th class="px-5 py-3 w-[260px]">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                @forelse($reviews as $r)
                    @php
                        $badge = $r->status === 'approved'
                            ? 'bg-emerald-100 text-emerald-800 border-emerald-200'
                            : ($r->status === 'rejected'
                                ? 'bg-rose-100 text-rose-800 border-rose-200'
                                : 'bg-amber-100 text-amber-800 border-amber-200');
                    @endphp

                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-4 align-top">
                            <div class="font-extrabold text-slate-900">{{ class_basename($r->reviewable_type) }}</div>
                            <div class="mt-0.5 text-xs text-slate-500">ID: <span class="font-semibold">{{ $r->reviewable_id }}</span></div>
                        </td>

                        <td class="px-5 py-4 align-top font-semibold text-slate-800">
                            {{ $r->name }}
                        </td>

                        <td class="px-5 py-4 align-top text-slate-700">
                            {{ $r->email }}
                        </td>

                        <td class="px-5 py-4 align-top">
                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-extrabold text-slate-800">
                                {{ $r->rating }}/5
                            </span>
                        </td>

                        <td class="px-5 py-4 align-top max-w-[520px]">
                            <div class="text-xs text-slate-500">
                                IP: <span class="font-semibold">{{ $r->ip_address }}</span>
                            </div>
                            <div class="mt-1 text-slate-800">
                                {{ \Illuminate\Support\Str::limit($r->comment, 160) }}
                            </div>
                        </td>

                        <td class="px-5 py-4 align-top">
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $badge }}">
                                {{ $r->status }}
                            </span>
                        </td>

                        <td class="px-5 py-4 align-top text-xs text-slate-500 whitespace-nowrap">
                            {{ $r->created_at->format('d M Y H:i') }}
                        </td>

                        <td class="px-5 py-4 align-top">
                            <div class="flex flex-wrap gap-2">

                                 <a href="{{ route('admin.reviews.edit', $r->id) }}"
           class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
            Edit
        </a>
                                <form method="POST" action="{{ route('admin.reviews.approve', $r) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-extrabold text-white transition
                                            {{ $r->status === 'approved' ? 'bg-slate-300 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-700' }}"
                                            {{ $r->status === 'approved' ? 'disabled' : '' }}>
                                        Approve
                                    </button>
                                </form>
                                

                                <form method="POST" action="{{ route('admin.reviews.reject', $r) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-extrabold text-white transition
                                            {{ $r->status === 'rejected' ? 'bg-slate-300 cursor-not-allowed' : 'bg-amber-500 hover:bg-amber-600' }}"
                                            {{ $r->status === 'rejected' ? 'disabled' : '' }}>
                                        Decline
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.reviews.delete', $r) }}"
                                      onsubmit="return confirm('Hapus review ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-slate-500">
                            Tidak ada data.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-4 border-t border-slate-200">
            {{ $reviews->links() }}
        </div>
    </div>

</div>
@endsection
