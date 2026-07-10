@if(!empty($package->created_by_partner_id))
<div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 mb-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
    <div class="text-sm font-extrabold text-slate-900">Review Produk Partner</div>
    <div class="text-xs font-extrabold text-slate-700">
      Status: {{ strtoupper($package->partner_review_status ?? '-') }} |
      Active: {{ $package->is_active ? 'YES' : 'NO' }}
    </div>
  </div>

  {{-- Info note terakhir (kalau ada) --}}
  @if(!empty($package->partner_review_note))
    <div class="mt-3 rounded-2xl border border-slate-200 bg-white p-4">
      <div class="text-xs font-extrabold text-slate-600">Catatan Review Terakhir</div>
      <div class="mt-1 text-sm text-slate-800 whitespace-pre-line">{{ $package->partner_review_note }}</div>
      @if(!empty($package->partner_reviewed_at))
        <div class="mt-2 text-[11px] text-slate-500">
          Reviewed at: {{ \Carbon\Carbon::parse($package->partner_reviewed_at)->format('d M Y H:i') }}
        </div>
      @endif
    </div>
  @endif

  <div class="mt-4 grid md:grid-cols-3 gap-3">

    {{-- APPROVE --}}
    <form method="POST" action="{{ route('admin.partners.products.approve', ['type'=>$type,'id'=>$package->id]) }}"
          class="rounded-2xl border border-slate-200 bg-white p-4">
      @csrf
      <div class="text-xs font-extrabold text-slate-700">Approve</div>
      <textarea name="note" rows="2"
                class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-300 focus:ring-0"
                placeholder="Note approve (opsional)"></textarea>
      <button type="submit"
              class="mt-2 w-full px-4 py-2 rounded-2xl font-extrabold text-white"
              style="background:#16a34a;"
              onmouseover="this.style.opacity='0.92'"
              onmouseout="this.style.opacity='1'">
        Approve
      </button>
    </form>

    {{-- REJECT --}}
    <form method="POST" action="{{ route('admin.partners.products.reject', ['type'=>$type,'id'=>$package->id]) }}"
          class="rounded-2xl border border-slate-200 bg-white p-4">
      @csrf
      <div class="text-xs font-extrabold text-slate-700">Reject</div>
      <textarea name="note" rows="2"
                class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-300 focus:ring-0"
                placeholder="Alasan reject (wajib)" required></textarea>
      <button type="submit"
              class="mt-2 w-full px-4 py-2 rounded-2xl font-extrabold text-white"
              style="background:#dc2626;"
              onmouseover="this.style.opacity='0.92'"
              onmouseout="this.style.opacity='1'">
        Reject
      </button>
    </form>

    {{-- DISABLE --}}
    <form method="POST" action="{{ route('admin.partners.products.disable', ['type'=>$type,'id'=>$package->id]) }}"
          class="rounded-2xl border border-slate-200 bg-white p-4">
      @csrf
      <div class="text-xs font-extrabold text-slate-700">Nonaktifkan</div>
      <textarea name="note" rows="2"
                class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-300 focus:ring-0"
                placeholder="Alasan nonaktif (wajib)" required></textarea>
      <button type="submit"
              class="mt-2 w-full px-4 py-2 rounded-2xl font-extrabold text-white"
              style="background:#0f172a;"
              onmouseover="this.style.opacity='0.92'"
              onmouseout="this.style.opacity='1'">
        Nonaktifkan
      </button>
    </form>

  </div>
</div>
@endif
