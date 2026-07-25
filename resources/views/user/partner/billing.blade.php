@extends('user.layouts.app')
@section('title', 'Tagihan Bulanan / Paket Iklan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Tagihan & Paket</h2>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- Current Active Package Status --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500 font-semibold mb-1">Paket Aktif Saat Ini</p>
            @if($currentSubscription)
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    {{ $currentSubscription->package->name }}
                    @if($currentSubscription->package->is_free)
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-md text-xs font-bold">DEFAULT</span>
                    @endif
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    Berlaku s/d: <strong class="text-slate-700">{{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('d M Y') : 'Selamanya' }}</strong>
                </p>
                <p class="text-sm text-slate-500">
                    Sisa Kuota: <strong class="text-slate-700">{{ $currentSubscription->package->listing_quota == -1 ? 'Unlimited' : (auth()->user()->quota->listing_quota ?? 0) }} Listing</strong>
                </p>
            @else
                <h3 class="text-xl font-bold text-slate-800 text-red-500">Tidak Ada Paket Aktif</h3>
            @endif
        </div>
        <div class="hidden sm:block">
            <i data-lucide="shield-check" class="w-16 h-16 text-emerald-500/20"></i>
        </div>
    </div>

    {{-- Available Packages (Upgrade options) --}}
    <h3 class="text-lg font-bold text-slate-800 mt-8 mb-4">Upgrade Paket Anda</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($availablePackages as $pkg)
            <div class="bg-white rounded-2xl border {{ $currentSubscription && $currentSubscription->partner_package_id == $pkg->id ? 'border-[#0194F3] ring-4 ring-blue-50' : 'border-slate-200' }} shadow-sm p-6 flex flex-col hover:-translate-y-1 transition duration-300 relative overflow-hidden">
                
                @if($pkg->discount_label)
                    <div class="absolute top-4 right-[-30px] bg-red-500 text-white text-[10px] font-bold py-1 px-8 transform rotate-45 shadow-sm">
                        {{ $pkg->discount_label }}
                    </div>
                @endif
                
                <div class="flex-grow">
                    <h4 class="text-xl font-bold text-slate-800 pr-8">{{ $pkg->name }}</h4>
                    <p class="text-sm text-slate-500 mt-1 mb-4 min-h-[40px]">{{ Str::limit($pkg->description, 60) }}</p>
                    
                    <div class="mb-4">
                        @if($pkg->original_price)
                            <div class="text-sm text-slate-400 line-through mb-1">Rp {{ number_format($pkg->original_price, 0, ',', '.') }}</div>
                        @endif
                        <div class="text-3xl font-black text-[#0194F3]">
                            Rp {{ number_format($pkg->price, 0, ',', '.') }}
                            <span class="text-sm font-normal text-slate-500">/ {{ $pkg->duration_days }} hari</span>
                        </div>
                    </div>

                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2 text-sm text-slate-700 font-medium">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500 shrink-0"></i>
                            <span>Kuota: {{ $pkg->listing_quota == -1 ? 'Unlimited' : $pkg->listing_quota . ' Listing' }}</span>
                        </li>
                        @if($pkg->bonus)
                        <li class="flex items-start gap-2 text-sm text-slate-700 font-medium">
                            <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
                            <span>Bonus <strong class="text-emerald-600">+{{ $pkg->bonus }}</strong> Iklan</span>
                        </li>
                        @endif
                        @if(!empty($pkg->benefits) && is_array($pkg->benefits))
                            @foreach($pkg->benefits as $benefit)
                                <li class="flex items-start gap-2 text-sm text-slate-700 font-medium">
                                    <i data-lucide="check" class="w-5 h-5 text-emerald-500 shrink-0"></i>
                                    <span>{{ $benefit }}</span>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                
                @if($currentSubscription && $currentSubscription->partner_package_id == $pkg->id)
                    <button disabled class="w-full py-3 bg-slate-100 text-slate-500 rounded-xl font-bold cursor-not-allowed">
                        Sedang Aktif
                    </button>
                @else
                    <a href="{{ route('partner.billing.checkout', $pkg) }}" class="block text-center w-full py-3 bg-[#0194F3] hover:bg-blue-600 text-white rounded-xl font-bold transition">
                        {{ $pkg->button_text ?? 'Beli Paket Ini' }}
                    </a>
                @endif
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-8 border border-slate-200 shadow-sm text-center">
                <p class="text-slate-500">Belum ada paket berbayar yang tersedia.</p>
            </div>
        @endforelse
    </div>

    {{-- Billing History --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-8">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Riwayat Tagihan & Pembayaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-6 py-4 font-semibold">TANGGAL</th>
                        <th class="px-6 py-4 font-semibold">PAKET</th>
                        <th class="px-6 py-4 font-semibold">TOTAL HARGA</th>
                        <th class="px-6 py-4 font-semibold">STATUS</th>
                        <th class="px-6 py-4 font-semibold">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subscriptions as $sub)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-slate-600">{{ $sub->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $sub->package->name }}</td>
                            <td class="px-6 py-4 font-bold text-[#0194F3]">Rp {{ number_format($sub->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($sub->status == 'active')
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-bold">AKTIF</span>
                                @elseif($sub->status == 'pending')
                                    <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-xs font-bold">MENUNGGU PEMBAYARAN</span>
                                @elseif($sub->status == 'expired')
                                    <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold">EXPIRED</span>
                                @else
                                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold uppercase">{{ $sub->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                  @php
                                      $adminWa = \App\Models\Setting::getValue('footer_whatsapp');
                                      // Make sure it starts with 62
                                      if ($adminWa && str_starts_with($adminWa, '0')) {
                                          $adminWa = '62' . substr($adminWa, 1);
                                      }
                                      if ($adminWa) {
                                          $adminWa = preg_replace('/[^0-9]/', '', $adminWa);
                                      }
                                  @endphp
                                @if($sub->status == 'pending')
                                    @if($sub->payment_method == 'offline' && !$sub->payment_proof)
                                        <div class="flex flex-col items-start gap-2">
                                            <a href="{{ route('partner.billing.upload_proof', $sub->id) }}" class="text-sm font-bold text-blue-600 hover:underline">Upload Bukti</a>
                                            @if($adminWa)
                                                <a href="https://wa.me/{{ $adminWa }}?text=Halo%20Admin,%20saya%20ingin%20konfirmasi%20pembayaran%20Paket%20Langganan%20Partner%20sebesar%20Rp{{ number_format($sub->amount, 0, '', '') }}" target="_blank" class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded hover:bg-green-600 transition">
                                                    <i data-lucide="message-circle" class="w-3 h-3"></i> Konfirmasi WA
                                                </a>
                                            @endif
                                        </div>
                                    @elseif($sub->payment_method != 'offline' && $sub->payment_url)
                                        <a href="{{ $sub->payment_url }}" target="_blank" class="text-sm font-bold text-blue-600 hover:underline">Bayar Sekarang</a>
                                    @else
                                        <div class="flex flex-col items-start gap-2">
                                            <span class="text-slate-400 text-sm">Menunggu Konfirmasi</span>
                                            @if($adminWa)
                                                <a href="https://wa.me/{{ $adminWa }}?text=Halo%20Admin,%20saya%20sudah%20upload%20bukti%20pembayaran%20Paket%20Langganan%20Partner.%20Mohon%20segera%20diproses." target="_blank" class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded hover:bg-green-600 transition">
                                                    <i data-lucide="message-circle" class="w-3 h-3"></i> Konfirmasi WA
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada riwayat tagihan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
