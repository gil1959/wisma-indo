@extends('partner.layouts.app')

@section('title', 'Withdraw Requests')
@section('page-subtitle', 'Riwayat Request')
@section('page-title', 'Withdraw Requests')

@section('content')
<div class="space-y-5">

  @if (session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 text-sm font-bold">
      {{ session('success') }}
    </div>
  @endif

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <div class="text-xl font-extrabold text-slate-900">Daftar Request Penarikan</div>
        <div class="mt-1 text-sm text-slate-600">Pending / Approved / Rejected tetap tampil. Bisa hapus.</div>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('partner.withdraw.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-extrabold text-slate-800 hover:bg-slate-50">
          <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
          Kembali
        </a>
      </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
      @php
        $tabs = [
          'all' => 'Semua',
          'pending' => 'Pending',
          'approved' => 'Approved',
          'rejected' => 'Rejected',
        ];
      @endphp
      @foreach ($tabs as $k => $label)
        <a href="{{ route('partner.withdraw.requests', ['status' => $k]) }}"
           class="rounded-full border px-4 py-2 text-xs font-extrabold {{ $status === $k ? 'bg-sky-50 border-sky-200 text-sky-700' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>

    <div class="mt-4 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-xs uppercase text-slate-500">
            <th class="py-2 pr-4 font-extrabold">Tanggal</th>
            <th class="py-2 pr-4 font-extrabold">Jumlah</th>
            <th class="py-2 pr-4 font-extrabold">Status</th>
            <th class="py-2 pr-4 font-extrabold">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse ($items as $r)
            <tr>
              <td class="py-3 pr-4 font-bold text-slate-800">{{ $r->created_at->format('d M Y H:i') }}</td>
              <td class="py-3 pr-4 font-extrabold text-slate-900">Rp {{ number_format($r->amount, 0, ',', '.') }}</td>
              <td class="py-3 pr-4">
                @php
                  $badge = $r->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : ($r->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200');
                @endphp
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $badge }}">
                  {{ strtoupper($r->status) }}
                </span>
              </td>
              <td class="py-3 pr-4">
                <div class="flex items-center gap-3">
                  <a href="{{ route('partner.withdraw.show', $r->id) }}" class="text-sky-600 font-extrabold hover:underline">Detail</a>

                  <form method="POST" action="{{ route('partner.withdraw.destroy', $r->id) }}"
                        onsubmit="return confirm('Hapus request ini? Jika status masih pending, saldo akan otomatis kembali ke saldo tersedia.')">
                    @csrf
                    @method('DELETE')
                    <button class="text-rose-600 font-extrabold hover:underline">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="py-6 text-center text-slate-500 font-bold">Belum ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $items->links() }}
    </div>
  </div>

</div>
@endsection
