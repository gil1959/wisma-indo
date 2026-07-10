@extends('layouts.admin')

@section('title', 'Pengaturan Pembayaran')
@section('page-title', 'Pembayaran')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Pengaturan Pembayaran</h2>
        <p class="mt-1 text-sm text-slate-600">
            Atur rekening manual dan konfigurasi gateway pembayaran.
        </p>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            <div class="font-bold">Berhasil</div>
            <div class="text-sm mt-1">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="font-bold">Gagal</div>
            <div class="text-sm mt-1">{{ session('error') }}</div>
        </div>
    @endif
<div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden mb-6">
  <div class="px-5 py-4 border-b border-slate-200">
    <p class="text-sm font-extrabold text-slate-800">Kode Unik Transfer Manual</p>
    <p class="text-xs text-slate-500 mt-1">Total transfer = total invoice + kode unik (contoh: 1.500.000 → 1.500.198).</p>
  </div>

  <form action="{{ route('admin.payments.unique-code-setting') }}" method="POST" class="p-5 grid gap-4 md:grid-cols-12">
    @csrf
    @method('PUT')

    <div class="md:col-span-3">
      <label class="block text-sm font-extrabold text-slate-800 mb-1">Min</label>
      <input type="number" name="manual_unique_code_min" min="1" max="999"
             value="{{ old('manual_unique_code_min', $settings['manual_unique_code_min'] ?? 1) }}"
             class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
      @error('manual_unique_code_min')
        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="md:col-span-3">
      <label class="block text-sm font-extrabold text-slate-800 mb-1">Max</label>
      <input type="number" name="manual_unique_code_max" min="1" max="999"
             value="{{ old('manual_unique_code_max', $settings['manual_unique_code_max'] ?? 999) }}"
             class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
      @error('manual_unique_code_max')
        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="md:col-span-6 flex items-end justify-end">
      <button type="submit"
              class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
              style="background:#0194F3;"
              onmouseover="this.style.background='#0186DB'"
              onmouseout="this.style.background='#0194F3'">
        Simpan
      </button>
    </div>
  </form>
</div>

    {{-- =======================
        BANK TRANSFER MANUAL
    ======================= --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="font-extrabold text-slate-900">Bank Transfer Manual</div>
            <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-extrabold"
                  style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                <i data-lucide="landmark" class="w-4 h-4" style="color:#0194F3;"></i>
                Rekening Manual
            </span>
        </div>

        <div class="p-5 space-y-5">

            {{-- Add Bank --}}
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                <div class="font-extrabold text-slate-900">Tambah Rekening</div>
                <p class="mt-1 text-sm text-slate-600">Isi data rekening bank untuk pembayaran manual.</p>

                <form method="POST" action="{{ route('admin.bank.add') }}" class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-3">
                    @csrf

                    <div class="md:col-span-4">
                        <label class="block text-sm font-bold text-slate-800 mb-1">Nama Bank</label>
                        <input name="bank_name" required
                               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                               placeholder="Contoh: BCA">
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-bold text-slate-800 mb-1">No Rekening</label>
                        <input name="account_number" required
                               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                               placeholder="Contoh: 1234567890">
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-bold text-slate-800 mb-1">Atas Nama</label>
                        <input name="account_holder" required
                               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                               placeholder="Contoh: Bintang Wisata">
                    </div>
                    <div class="md:col-span-4">
                         <label class="block text-sm font-bold text-slate-800 mb-1">SWIFT Code (Opsional)</label>
                         <input name="swift_code"
                          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                             placeholder="Contoh: BMRIIDJA atau BMRIIDJAXXX">
                            <p class="mt-1 text-xs text-slate-500">Dipakai untuk transfer internasional (wire transfer).</p>
                    </div>


                    <div class="md:col-span-12 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                                style="background:#0194F3;"
                                onmouseover="this.style.background='#0186DB'"
                                onmouseout="this.style.background='#0194F3'">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Tambah
                        </button>
                    </div>
                </form>
            </div>

            {{-- List Banks --}}
            @php
                $manualBanks = $methods->where('type', 'manual');
            @endphp

            <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[900px] w-full">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-extrabold text-slate-600">
                                <th class="px-5 py-3 w-[70px]">#</th>
                                <th class="px-5 py-3">Bank</th>
                                <th class="px-5 py-3">No Rekening</th>
                                <th class="px-5 py-3">Atas Nama</th>
                                <th class="px-5 py-3">SWIFT</th>
                                <th class="px-5 py-3 w-[160px]">Status</th>
                                <th class="px-5 py-3 w-[130px]">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                        @forelse($manualBanks as $m)
                            <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                                <td class="px-5 py-4 font-extrabold text-slate-900">{{ $m->id }}</td>
                                <td class="px-5 py-4 font-bold text-slate-900">
                                    {{ $m->bank_name ?? $m->method_name }}
                                </td>
                                <td class="px-5 py-4">{{ $m->account_number }}</td>
                                <td class="px-5 py-4">{{ $m->account_holder }}</td>
                                <td class="px-5 py-4">
                                    @if(!empty($m->swift_code))
                                        <code class="text-xs rounded-lg bg-slate-100 px-2 py-1 text-slate-700">{{ $m->swift_code }}</code>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">

                                    @if($m->is_active)
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border border-emerald-200 bg-emerald-50 text-emerald-800">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border border-slate-200 bg-white text-slate-700">
                                            <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <form method="POST"
                                          action="{{ route('admin.bank.delete', $m->id) }}"
                                          onsubmit="return confirm('Yakin hapus bank ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                                style="background:#ef4444"
                                                onmouseover="this.style.background='#dc2626'"
                                                onmouseout="this.style.background='#ef4444'">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                               <td colspan="7" class="px-5 py-12 text-center">

                                    <div class="mx-auto h-12 w-12 rounded-2xl border grid place-items-center"
                                         style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                                        <i data-lucide="inbox" class="w-6 h-6" style="color:#0194F3;"></i>
                                    </div>
                                    <div class="mt-3 font-extrabold text-slate-900">Belum ada rekening manual</div>
                                    <div class="mt-1 text-sm text-slate-600">Tambahkan rekening untuk pembayaran transfer manual.</div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>

    {{-- =======================
        PAYMENT GATEWAY
    ======================= --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <div class="font-extrabold text-slate-900">Payment Gateway</div>
           <div class="text-sm text-slate-600 mt-1">
    DOKU / TriPay / Midtrans / Xendit / iPaymu.
</div>

        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($gateways as $g)
                    @php
                        $cred = is_array($g->credentials) ? $g->credentials : [];
                        $modeVal = $cred['mode'] ?? 'sandbox';
                        $isActive = (bool) $g->is_active;
                    @endphp

                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-extrabold text-slate-900">{{ $g->label }}</div>
                                <div class="mt-1">
                                    @if($isActive)
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border border-emerald-200 bg-emerald-50 text-emerald-800">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border border-slate-200 bg-white text-slate-700">
                                            <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="h-10 w-10 rounded-2xl grid place-items-center border shrink-0"
                                 style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                                <i data-lucide="credit-card" class="w-5 h-5" style="color:#0194F3;"></i>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.payments.toggleGateway', $g->id) }}" class="mt-4 space-y-3">
                            @csrf

                            {{-- Mode --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-800 mb-1">Mode</label>
                                <select name="mode" required
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                    <option value="sandbox" {{ $modeVal === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                    <option value="production" {{ $modeVal === 'production' ? 'selected' : '' }}>Production</option>
                                </select>
                                <div class="text-xs text-slate-500 mt-1">
                                    Sandbox untuk testing, Production untuk live.
                                </div>
                            </div>

                            {{-- TriPay --}}
                            @if($g->name === 'tripay')
                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-1">API Key</label>
                                    <input name="api_key" autocomplete="off"
                                           value="{{ $cred['api_key'] ?? '' }}"
                                           placeholder="Tripay API Key"
                                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-1">Private Key</label>
                                    <input name="private_key" autocomplete="off"
                                           value="{{ $cred['private_key'] ?? '' }}"
                                           placeholder="Tripay Private Key"
                                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-1">Merchant Code</label>
                                    <input name="merchant_code" autocomplete="off"
                                           value="{{ $cred['merchant_code'] ?? '' }}"
                                           placeholder="Tripay Merchant Code"
                                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                </div>

                                <div class="text-xs text-slate-500">
                                    Wajib: mode, api_key, private_key, merchant_code
                                </div>
                            @endif

                            {{-- DOKU --}}
                            @if($g->name === 'doku')
                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-1">Client ID</label>
                                    <input name="client_id" autocomplete="off"
                                           value="{{ $cred['client_id'] ?? '' }}"
                                           placeholder="DOKU Client ID"
                                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-1">Secret Key</label>
                                    <input name="secret_key" autocomplete="off"
                                           value="{{ $cred['secret_key'] ?? '' }}"
                                           placeholder="DOKU Secret Key"
                                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                </div>

                                <div class="text-xs text-slate-500">
                                    Wajib: mode, client_id, secret_key
                                </div>
                            @endif

                            {{-- Midtrans --}}
                            @if($g->name === 'midtrans')
                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-1">Server Key</label>
                                    <input name="server_key" autocomplete="off"
                                           value="{{ $cred['server_key'] ?? '' }}"
                                           placeholder="Midtrans Server Key"
                                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-1">Client Key</label>
                                    <input name="client_key" autocomplete="off"
                                           value="{{ $cred['client_key'] ?? '' }}"
                                           placeholder="Midtrans Client Key"
                                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                </div>

                                <div class="text-xs text-slate-500">
                                    Wajib: mode, server_key, client_key
                                </div>
                            @endif

                            {{-- XENDIT --}}
                                @if($g->name === 'xendit')
                                    <div>
                                        <label class="block text-sm font-bold text-slate-800 mb-1">Secret Key</label>
                                        <input name="secret_key" autocomplete="off"
                                            value="{{ $cred['secret_key'] ?? '' }}"
                                            placeholder="Xendit Secret API Key"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-800 mb-1">Callback Token</label>
                                        <input name="callback_token" autocomplete="off"
                                            value="{{ $cred['callback_token'] ?? '' }}"
                                            placeholder="Xendit Callback Token"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                    </div>

                                    <div class="text-xs text-slate-500">
                                        Wajib: mode, secret_key, callback_token
                                    </div>
                                @endif
                                    {{-- IPAYMU --}}
                                    @if($g->name === 'ipaymu')
                                        <div>
                                            <label class="block text-sm font-bold text-slate-800 mb-1">VA Number</label>
                                            <input name="va" autocomplete="off"
                                                value="{{ $cred['va'] ?? '' }}"
                                                placeholder="iPaymu VA Number"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-800 mb-1">API Key</label>
                                            <input name="api_key" autocomplete="off"
                                                value="{{ $cred['api_key'] ?? '' }}"
                                                placeholder="iPaymu API Key"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            Wajib: mode, va, api_key
                                        </div>
                                    @endif

                                    {{-- PAYPAL --}}
@if($g->name === 'paypal')
  <div>
    <label class="block text-sm font-bold text-slate-800 mb-1">Client ID</label>
    <input name="client_id" autocomplete="off"
      value="{{ $cred['client_id'] ?? '' }}"
      placeholder="PayPal Client ID"
      class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
  </div>

  <div>
    <label class="block text-sm font-bold text-slate-800 mb-1">Client Secret</label>
    <input name="client_secret" autocomplete="off"
      value="{{ $cred['client_secret'] ?? '' }}"
      placeholder="PayPal Client Secret"
      class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
  </div>

  <div>
    <label class="block text-sm font-bold text-slate-800 mb-1">Webhook ID (opsional)</label>
    <input name="webhook_id" autocomplete="off"
      value="{{ $cred['webhook_id'] ?? '' }}"
      placeholder="PayPal Webhook ID"
      class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
  </div>

  <div class="text-xs text-slate-500">
    Wajib: mode, client_id, client_secret
  </div>
@endif



                            <input type="hidden" name="enable" value="{{ $isActive ? 0 : 1 }}">

                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                                    style="background: {{ $isActive ? '#ef4444' : '#16a34a' }};"
                                    onmouseover="this.style.background='{{ $isActive ? '#dc2626' : '#15803d' }}'"
                                    onmouseout="this.style.background='{{ $isActive ? '#ef4444' : '#16a34a' }}'">
                                <i data-lucide="{{ $isActive ? 'x-circle' : 'check-circle' }}" class="w-4 h-4"></i>
                                {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>

                            <div class="text-xs text-slate-500">
                                Channels tersimpan: {{ is_array($g->channels) ? count($g->channels) : 0 }}
                                @if($g->channels_synced_at)
                                    (sync: {{ $g->channels_synced_at->format('Y-m-d H:i') }})
                                @endif
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
@endsection
