@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('content')

<div class="max-w-3xl space-y-6">
  <div class="bg-white border border-slate-200 rounded-2xl p-6">
    <h1 class="text-2xl font-extrabold text-slate-900">{{ $isEn ? 'Affiliate Program' : 'Program Affiliate' }}</h1>
    <p class="mt-2 text-sm text-slate-600">
      {{ $isEn ? 'Affiliate features can be used only after your account is approved by admin.' : 'Fitur affiliate hanya bisa digunakan setelah akun kamu disetujui admin.' }}
    </p>

    @if(empty($status) || $status === 'none')

    <div class="mt-6 p-4 rounded-2xl border border-slate-200 bg-slate-50">
      <div class="text-sm font-bold text-slate-900">{{ $isEn ? 'Status: Not Applied Yet' : 'Status: Belum Mengajukan' }}</div>
      <div class="text-xs text-slate-600 mt-1">{{ $isEn ? 'Apply first so admin can review your account.' : 'Ajukan dulu supaya admin bisa review akun kamu.' }}</div>
    </div>

    <form method="POST" action="{{ route('user.affiliate.apply.submit') }}" class="mt-6 space-y-4">
      @csrf
      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Reason / Promotion Plan' : 'Alasan / Rencana Promosi' }}</label>
        <textarea name="reason" rows="5" required
          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800"
          placeholder="{{ $isEn ? 'Example: I have a travel audience, will promote via TikTok & Instagram, focus on Bali Tour product...' : 'Contoh: Saya punya audience travel, akan promosi via TikTok & Instagram, fokus produk Tour Bali...' }}">{{ old('reason') }}</textarea>
        @error('reason') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Main Channel (optional)' : 'Channel Utama (opsional)' }}</label>
        <input name="channel" value="{{ old('channel') }}"
          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800"
          placeholder="{{ $isEn ? 'Example: TikTok, Instagram, Website, WhatsApp Group' : 'Contoh: TikTok, Instagram, Website, WhatsApp Group' }}">
        @error('channel') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
        {{ $isEn ? 'Submit Application' : 'Ajukan Persetujuan' }}
      </button>
    </form>
    @elseif($status === 'pending')
    <div class="mt-6 p-4 rounded-2xl border border-amber-200 bg-amber-50">
      <div class="text-sm font-bold text-amber-900">{{ $isEn ? 'Status: Waiting for Admin Approval' : 'Status: Menunggu Persetujuan Admin' }}</div>
      <div class="text-xs text-amber-800 mt-1">
        {{ $isEn ? 'Your application is being reviewed.' : 'Pengajuan kamu sedang direview.' }}
        @if($requested_at) {{ $isEn ? 'Submitted' : 'Diajukan' }}: {{ $requested_at->format('d M Y H:i') }} @endif
      </div>
    </div>
    @elseif($status === 'declined')
    <div class="mt-6 p-4 rounded-2xl border border-red-200 bg-red-50">
      <div class="text-sm font-bold text-red-900">{{ $isEn ? 'Status: Declined' : 'Status: Ditolak' }}</div>
      <div class="text-xs text-red-800 mt-1">
        @if($reviewed_at) {{ $isEn ? 'Reviewed' : 'Direview' }}: {{ $reviewed_at->format('d M Y H:i') }} @endif
      </div>
      @if($note)
      <div class="mt-3 text-xs text-slate-700 whitespace-pre-line bg-white border border-slate-200 rounded-2xl p-3">
        {{ $note }}
      </div>
      @endif

      <form method="POST" action="{{ route('user.affiliate.apply.submit') }}" class="mt-4">
        @csrf
        <input type="hidden" name="reason" value="{{ $isEn ? 'Re-apply: I will complete the requirements & clarify the promotion strategy.' : 'Re-apply: saya akan melengkapi syarat & memperjelas strategi promosi.' }}">

        <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
          {{ $isEn ? 'Re-Apply' : 'Ajukan Ulang' }}
        </button>
      </form>
    </div>
    @endif
  </div>
</div>
@endsection