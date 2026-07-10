@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('content')
<div class="space-y-5">
  <div class="flex items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Affiliate Coupons</h1>
      <p class="mt-1 text-sm text-slate-600">
        {{ $isEn
      ? 'Pick a coupon from admin, create an alias (custom name), then use it when creating an Affiliate Link.'
      : 'Pilih coupon dari admin, buat alias (nama custom), lalu gunakan coupon tersebut saat membuat Link Affiliate.'
  }}
      </p>
    </div>
    <a href="{{ route('user.affiliate.links.create') }}"
      class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
      style="background:#0194F3;">
      {{ $isEn ? '+ Create Link' : '+ Buat Link' }}
    </a>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
  <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-sm font-semibold">
    {{ session('success') }}
  </div>
  @endif

  @if($errors->any())
  <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 text-sm">
    <div class="font-extrabold mb-2">{{ $isEn ? 'Input errors:' : 'Ada kesalahan input:' }}</div>
    <ul class="list-disc pl-5 space-y-1">
      @foreach($errors->all() as $e)
      <li class="font-semibold">{{ $e }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  {{-- Create Alias Coupon --}}
  <div class="bg-white p-5 rounded-2xl border border-slate-200">
    <div class="flex items-start justify-between gap-3">
      <div>
        <div class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Add Coupon to Account' : 'Tambah Coupon ke Akun' }}</div>
        <div class="mt-1 text-xs text-slate-600">
          {{ $isEn
      ? 'This does not change the admin promo. You only create an alias for easier selection when creating a link.'
      : 'Coupon ini tidak mengubah data promo admin. Kamu hanya membuat alias agar mudah dipilih saat Create Link.'
  }}
        </div>
      </div>
    </div>

    <form method="POST" action="{{ route('user.affiliate.coupons.store') }}" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
      @csrf

      <div class="md:col-span-2">
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Select Coupon ' : 'Pilih Coupon' }}/label>
          <select name="promo_id" required
            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
            <option value="">{{ $isEn ? 'Select' : 'Pilih' }}</option>
            @foreach($promos as $p)
            <option value="{{ $p->id }}">
              {{ $p->code }} - {{ $p->name ?? 'Promo' }}
            </option>
            @endforeach
          </select>
      </div>

      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Alias Name' : 'Nama Alias' }}</label>
        <input name="alias_name" required value="{{ old('alias_name') }}"
          placeholder="{{ $isEn ? 'Example: IG Reels Coupon' : 'Contoh: Coupon IG Reels' }}"
          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
        <div class="mt-2 text-xs text-slate-500">{{ $isEn ? 'Alias will appear in the dropdown when creating a link.' : 'Alias akan muncul di dropdown saat Create Link.' }}</div>
      </div>

      <div class="md:col-span-3 flex items-center justify-end">
        <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
          style="background:#0194F3;">
          {{ $isEn ? 'Save Coupon' : 'Simpan Coupon' }}
        </button>
      </div>
    </form>
  </div>

  {{-- My Coupons --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200">
      <div class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'My Coupons' : 'My Coupons' }}</div>
      <div class="text-xs text-slate-600 mt-1">{{ $isEn ? 'List of coupons you saved (aliases). Use them when creating a link.' : 'Daftar coupon yang sudah kamu simpan (alias). Gunakan saat Create Link.' }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Alias</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Promo Code</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Promo Name</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Created</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-200">
          @forelse($myCoupons as $c)
          <tr class="align-top">
            <td class="px-4 py-3">
              <div class="font-extrabold text-slate-900">{{ $c->alias_name }}</div>
            </td>
            <td class="px-4 py-3 font-extrabold text-slate-900">{{ $c->promo?->code ?? '-' }}</td>
            <td class="px-4 py-3 font-semibold text-slate-800">{{ $c->promo?->name ?? 'Promo' }}</td>
            <td class="px-4 py-3 text-xs text-slate-600">
              {{ optional($c->created_at)->format('d M Y, H:i') }}
            </td>
          </tr>
          @empty
          <tr>
            <td class="px-4 py-6 text-slate-600" colspan="4">{{ $isEn ? 'No saved coupons yet.' : 'Belum ada coupon tersimpan.' }}</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4">
      {{ $myCoupons->links() }}
    </div>
  </div>
</div>
@endsection