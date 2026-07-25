@extends('layouts.admin')
@section('title', 'Buat Referral Lead')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
        <a href="{{ route('admin.referrals.index') }}" class="hover:text-[#0194F3]">Distribusi Lead</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span class="text-slate-800 font-semibold">Buat Baru</span>
    </div>
    <h1 class="text-2xl font-bold text-slate-800">Buat Referral Lead</h1>
    <p class="text-slate-500 text-sm mt-1">Tambahkan data calon pembeli untuk didistribusikan ke Partner.</p>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm max-w-3xl">
    <form action="{{ route('admin.referrals.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="space-y-5">
            {{-- Partner Tujuan --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Partner Tujuan <span class="text-red-500">*</span></label>
                <select name="partner_id" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" required>
                    <option value="">-- Pilih Partner --</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                    @endforeach
                </select>
                @error('partner_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nama Lead --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Calon Pembeli <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" required placeholder="Contoh: Budi Santoso">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nomor HP --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" required placeholder="Contoh: 081234567890">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="Contoh: budi@email.com">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Pesan/Catatan Awal --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pesan / Catatan Awal Pembeli</label>
                <textarea name="message" rows="3" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20" placeholder="Contoh: Pembeli mencari rumah di area Jakarta Selatan dengan budget 1M.">{{ old('message') }}</textarea>
                @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- ID Listing (Opsional) --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Iklan yang Diminati <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <select name="listing_id" id="listing-select" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20">
                    <option value=""></option>
                    @foreach($listings as $listing)
                        <option value="{{ $listing->id }}" {{ old('listing_id') == $listing->id ? 'selected' : '' }}>
                            {{ $listing->title }} ({{ ucfirst($listing->property_type) }} - Rp {{ number_format($listing->price, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">Kosongkan jika pembeli belum memiliki iklan spesifik yang dituju (Pencarian Umum).</p>
                @error('listing_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('admin.referrals.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0194F3] hover:bg-blue-600 text-white font-semibold transition flex items-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i> Distribusikan ke Partner
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 44px;
        border-radius: 0.75rem;
        border-color: #e2e8f0;
        padding: 0.375rem 0.75rem;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
        right: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #334155;
        line-height: normal;
        padding-left: 0;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #0194F3;
        box-shadow: 0 0 0 3px rgba(1, 148, 243, 0.2);
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#listing-select').select2({
            placeholder: "Pilih Iklan (Ketik untuk mencari...)",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
