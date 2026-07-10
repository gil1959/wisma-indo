@extends('layouts.admin')

@section('title', 'Produk Partner')
@section('page-title', 'Produk Partner')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <div class="text-xs font-extrabold text-slate-500">Admin / Partner</div>
            <div class="text-xl sm:text-2xl font-extrabold text-slate-900">Produk Partner</div>
            <div class="mt-1 text-sm text-slate-600">Approve / reject / nonaktif produk yang dibuat partner.</div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            <div class="font-extrabold">Success</div>
            <div class="text-sm mt-1">{{ session('success') }}</div>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-3">
                <label class="block text-xs font-extrabold text-slate-600 mb-1">Type</label>
                <select name="type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="" {{ empty($filters['type']) ? 'selected' : '' }}>All</option>
                    <option value="tour" {{ ($filters['type'] ?? '')==='tour' ? 'selected' : '' }}>Tour</option>
                    <option value="rentcar" {{ ($filters['type'] ?? '')==='rentcar' ? 'selected' : '' }}>Rent Car</option>
                    <option value="ship" {{ ($filters['type'] ?? '')==='ship' ? 'selected' : '' }}>Ship</option>
                    <option value="restoran" {{ ($filters['type'] ?? '')==='restoran' ? 'selected' : '' }}>Restoran</option>
                    <option value="hotel" {{ ($filters['type'] ?? '')==='hotel' ? 'selected' : '' }}>Hotel/Vila</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="block text-xs font-extrabold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="" {{ empty($filters['status']) ? 'selected' : '' }}>All</option>
                    <option value="pending" {{ ($filters['status'] ?? '')==='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ ($filters['status'] ?? '')==='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ ($filters['status'] ?? '')==='rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="disabled" {{ ($filters['status'] ?? '')==='disabled' ? 'selected' : '' }}>Disabled</option>
                </select>
            </div>

            <div class="md:col-span-4">
                <label class="block text-xs font-extrabold text-slate-600 mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                       placeholder="Cari title / slug">
            </div>

            <div class="md:col-span-2 flex gap-2">
                <button class="w-full rounded-xl px-4 py-2 text-sm font-extrabold text-white"
                        style="background:#0194F3;">
                    Filter
                </button>
                <a href="{{ route('admin.partners.products.index') }}"
                   class="w-full rounded-xl px-4 py-2 text-sm font-extrabold border border-slate-200 bg-white text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full text-left text-sm">
                <thead class="bg-slate-50">
                <tr class="text-xs font-extrabold text-slate-600">
                    <th class="px-4 py-3 w-[120px]">Type</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3 w-[140px]">Status</th>
                    <th class="px-4 py-3 w-[100px]">Active</th>
                    <th class="px-4 py-3 w-[180px]">Created</th>
                    <th class="px-4 py-3 text-right w-[160px]">Action</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($items as $it)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-extrabold text-slate-800">
                            {{ strtoupper($it['type']) }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-extrabold text-slate-900">{{ $it['title'] }}</div>
                            <div class="text-xs text-slate-500">/{{ $it['slug'] }}</div>
                            @if(!empty($it['note']))
                                <div class="mt-2 text-xs text-slate-600 line-clamp-2">
                                    Note: {{ $it['note'] }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full border px-3 py-1 text-xs font-extrabold"
                                  style="border-color:rgba(148,163,184,0.35); background:rgba(148,163,184,0.12); color:#334155;">
                                {{ strtoupper($it['status'] ?? '-') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($it['is_active'])
                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-extrabold"
                                      style="border-color:rgba(16,185,129,0.25); background:rgba(16,185,129,0.10); color:#065f46;">
                                    YES
                                </span>
                            @else
                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-extrabold"
                                      style="border-color:rgba(239,68,68,0.25); background:rgba(239,68,68,0.10); color:#7f1d1d;">
                                    NO
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            {{ optional($it['created_at'])->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ $it['edit_url'] }}"
                               class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50">
                                Edit & Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                            Tidak ada produk partner.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
