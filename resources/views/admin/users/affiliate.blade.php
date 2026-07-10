@extends('layouts.admin')

@section('title', 'User Affiliate')
@section('page-title', 'User Affiliate')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">User Affiliate</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola user affiliate: data performa + set komisi.</p>
        </div>

        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ $q }}"
                   placeholder="Cari nama/email/phone..."
                   class="w-64 max-w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
            <button class="px-4 py-2.5 rounded-2xl font-extrabold border border-slate-200 text-slate-700 hover:bg-slate-50">
                Cari
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200">
            <div class="text-sm font-extrabold text-slate-900">Daftar User</div>
            <div class="mt-1 text-xs text-slate-500 font-semibold">
                Tips: user non-affiliate tetap tampil (kalau dicari), biar admin bisa langsung aktifkan.
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">User</th>
                        <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Affiliate</th>
                        <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Komisi</th>
                        <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Links</th>
                        <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Clicks</th>
                        <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Conv</th>
                        <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Orders</th>
                        <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Commission</th>
                        <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse($users as $u)
                        @php
                            $s = $stats[$u->id] ?? [
                                'links'=>0,'clicks'=>0,'conversions'=>0,'orders'=>0,
                                'comm_pending'=>0,'comm_approved'=>0,'comm_paid'=>0
                            ];
                            $isAff = (bool) ($u->is_affiliate ?? false);
                            $type = $u->affiliate_commission_type ?? 'percent';
                            $val  = $u->affiliate_commission_value ?? 0;
                        @endphp

                        <tr>
                            <td class="px-4 py-4">
                                <div class="font-extrabold text-slate-900">{{ $u->name }}</div>
                                <div class="text-xs text-slate-500 font-semibold">{{ $u->email }}</div>
                                @if(!empty($u->phone))
                                    <div class="text-xs text-slate-500 font-semibold">{{ $u->phone }}</div>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full border text-xs font-extrabold"
                                      style="background: {{ $isAff ? 'rgba(16,185,129,0.10)' : 'rgba(148,163,184,0.10)' }};
                                             border-color: {{ $isAff ? 'rgba(16,185,129,0.25)' : 'rgba(148,163,184,0.25)' }};
                                             color: {{ $isAff ? '#065f46' : '#334155' }};">
                                    {{ $isAff ? 'ACTIVE' : 'OFF' }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-extrabold text-slate-900">
                                    {{ strtoupper($type) }}:
                                    @if($type === 'percent')
                                        {{ rtrim(rtrim(number_format((float)$val,2,'.',''), '0'), '.') }}%
                                    @else
                                        Rp {{ number_format((float)$val, 0, ',', '.') }}
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 font-semibold">Set per user</div>
                            </td>

                            <td class="px-4 py-4 font-semibold text-slate-800">{{ number_format($s['links']) }}</td>
                            <td class="px-4 py-4 font-semibold text-slate-800">{{ number_format($s['clicks']) }}</td>
                            <td class="px-4 py-4 font-semibold text-slate-800">{{ number_format($s['conversions']) }}</td>
                            <td class="px-4 py-4 font-semibold text-slate-800">{{ number_format($s['orders']) }}</td>

                            <td class="px-4 py-4">
                                <div class="text-xs text-slate-500 font-semibold">Pending</div>
                                <div class="font-extrabold text-slate-900">Rp {{ number_format($s['comm_pending'], 0, ',', '.') }}</div>

                                <div class="mt-2 text-xs text-slate-500 font-semibold">Approved</div>
                                <div class="font-extrabold text-slate-900">Rp {{ number_format($s['comm_approved'], 0, ',', '.') }}</div>

                                <div class="mt-2 text-xs text-slate-500 font-semibold">Paid</div>
                                <div class="font-extrabold text-slate-900">Rp {{ number_format($s['comm_paid'], 0, ',', '.') }}</div>
                            </td>

                            <td class="px-4 py-4">
                                <form method="POST" action="{{ route('admin.users.affiliate.update', $u) }}" class="space-y-2">
                                    @csrf

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="is_affiliate" value="1" {{ $isAff ? 'checked' : '' }}>
                                        <span class="text-xs font-extrabold text-slate-700">Aktifkan</span>
                                    </label>

                                    <select name="affiliate_commission_type"
                                            class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-800">
                                        <option value="percent" {{ $type==='percent' ? 'selected' : '' }}>Percent</option>
                                        <option value="fixed" {{ $type==='fixed' ? 'selected' : '' }}>Fixed</option>
                                    </select>

                                    <input type="number" step="0.01" name="affiliate_commission_value" value="{{ $val }}"
                                           class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-800"
                                           placeholder="10 / 50000">

                                    <button class="w-full px-3 py-2 rounded-2xl font-extrabold text-white"
                                            style="background:#0194F3;">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-slate-500">
                                Data user tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
