@extends('layouts.admin')

@section('content')
<div class="space-y-5">

  {{-- Alert --}}
  @if(session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
      <div class="font-extrabold">Sukses</div>
      <div class="text-sm mt-1">{{ session('success') }}</div>
    </div>
  @endif

  @if(session('error'))
    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
      <div class="font-extrabold">Gagal</div>
      <div class="text-sm mt-1">{{ session('error') }}</div>
    </div>
  @endif

  {{-- Header + Filter --}}
  <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Partner Withdrawal Requests</h1>
    
    </div>

    <form method="GET" action="{{ route('admin.partner_withdrawals.index') }}" class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
      <select name="status"
              class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 bg-white">
        <option value="all" {{ $status==='all'?'selected':'' }}>All</option>
        <option value="pending" {{ $status==='pending'?'selected':'' }}>Pending</option>
        <option value="approved" {{ $status==='approved'?'selected':'' }}>Approved</option>
        <option value="rejected" {{ $status==='rejected'?'selected':'' }}>Rejected</option>
      </select>

      <input name="q" value="{{ $q }}"
             placeholder="Cari partner/email/rekening"
             class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 bg-white w-full sm:w-72">

      <button class="px-5 py-2 rounded-2xl font-extrabold text-white"
              style="background:#0194F3;"
              onmouseover="this.style.background='#0186DB'"
              onmouseout="this.style.background='#0194F3'">
        Filter
      </button>
    </form>
  </div>

  {{-- Table --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50">
          <tr class="text-left text-xs uppercase text-slate-500">
            <th class="px-5 py-3 font-extrabold">#</th>
            <th class="px-5 py-3 font-extrabold">Tanggal</th>
            <th class="px-5 py-3 font-extrabold">Partner</th>
            <th class="px-5 py-3 font-extrabold">Jumlah</th>
            <th class="px-5 py-3 font-extrabold">Status</th>
            <th class="px-5 py-3 font-extrabold">Rekening</th>
            <th class="px-5 py-3 font-extrabold text-right">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-200">
          @forelse($items as $r)
            @php
              $badge = [
                'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
              ][$r->status] ?? 'bg-slate-50 text-slate-800 border-slate-200';
            @endphp

            <tr class="hover:bg-slate-50/60">
              <td class="px-5 py-4 font-extrabold text-slate-900">{{ $r->id }}</td>

              <td class="px-5 py-4 text-slate-800 font-semibold">
                {{ $r->created_at?->format('d M Y H:i') }}
              </td>

              <td class="px-5 py-4">
                <div class="font-extrabold text-slate-900">{{ $r->partner?->name ?? '-' }}</div>
                <div class="text-xs text-slate-600 mt-0.5">{{ $r->partner?->email ?? '-' }}</div>
              </td>

              <td class="px-5 py-4">
                <div class="font-extrabold text-slate-900">Rp {{ number_format($r->amount, 0, ',', '.') }}</div>
              </td>

              <td class="px-5 py-4">
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $badge }}">
                  {{ strtoupper($r->status) }}
                </span>
              </td>

              <td class="px-5 py-4">
                <div class="font-extrabold text-slate-900">{{ $r->bank_name ?? '-' }}</div>
                <div class="text-xs text-slate-600 mt-0.5">
                  {{ $r->account_number ?? '-' }}
                  @if($r->account_holder)
                    <span class="text-slate-500">a.n</span> {{ $r->account_holder }}
                  @endif
                </div>
              </td>

              <td class="px-5 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('admin.partner_withdrawals.show', $r->id) }}"
                     class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-800 hover:bg-slate-50">
                    <i data-lucide="eye" class="w-4 h-4" style="color:#0194F3;"></i>
                    Detail
                  </a>

                  <form method="POST" action="{{ route('admin.partner_withdrawals.destroy', $r->id) }}"
                        onsubmit="return confirm('Hapus request ini?')">
                    @csrf
                    @method('DELETE')
                    <button
                      class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-extrabold text-rose-700 hover:bg-rose-100">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-5 py-10 text-center text-slate-500 font-bold">
                No data.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4">
      {{ $items->links() }}
    </div>
  </div>

</div>
@endsection
