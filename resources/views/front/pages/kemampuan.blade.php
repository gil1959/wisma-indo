@extends('layouts.front')

@section('title', 'Cek Kemampuan Cicilan KPR - ' . ($siteSettings['brand_name'] ?? 'Rumaindo'))

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4" style="font-family: 'Inter', sans-serif;">CEK KEMAMPUAN CICILAN</h1>
            <p class="text-slate-600 max-w-2xl mx-auto text-sm md:text-base">
                Hitung batas maksimal harga properti yang bisa Anda beli berdasarkan penghasilan dan pengeluaran bulanan Anda.
            </p>
        </div>

        <div x-data="kemampuanKPR()" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- DATA KEUANGAN PANEL --}}
            <div class="lg:col-span-1 bg-white rounded-3xl shadow-sm border border-slate-100 p-6 md:p-8">
                <h2 class="text-lg font-extrabold text-slate-800 mb-6 uppercase tracking-wide">DATA KEUANGAN ANDA</h2>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Penghasilan Bersih (Per Bulan) *</label>
                        <input type="text" x-model="incomeStr" @input="formatInput('incomeStr')" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3] focus:ring-opacity-20 text-slate-700 font-semibold" placeholder="Contoh: 10.000.000">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Total Cicilan Lain (Per Bulan)</label>
                        <input type="text" x-model="debtStr" @input="formatInput('debtStr')" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3] focus:ring-opacity-20 text-slate-700 font-semibold" placeholder="Contoh: 2.000.000">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Uang Muka (DP) Disiapkan</label>
                        <input type="text" x-model="dpStr" @input="formatInput('dpStr')" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3] focus:ring-opacity-20 text-slate-700 font-semibold" placeholder="Contoh: 50.000.000">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Suku Bunga (%)</label>
                            <input type="number" step="0.1" x-model.number="interest" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3] focus:ring-opacity-20 text-slate-700 font-semibold" placeholder="7">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Tenor (Tahun)</label>
                            <input type="number" x-model.number="tenor" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3] focus:ring-opacity-20 text-slate-700 font-semibold" placeholder="15">
                        </div>
                    </div>
                </div>
            </div>

            {{-- HASIL PANEL --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- KOTAK BIRU HASIL --}}
                <div class="bg-[#EFF6FF] rounded-3xl p-6 md:p-8 flex flex-col items-center text-center justify-center border border-blue-100">
                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">MAKSIMAL HARGA PROPERTI</div>
                    <div class="text-4xl md:text-5xl font-extrabold text-[#0194F3]" x-text="formatRp(maxPrice)">Rp 0</div>
                    <div class="mt-4 text-xs text-slate-500 font-semibold">*Termasuk Uang Muka yang Anda siapkan.</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-100 flex flex-col items-center text-center">
                        <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">PLAFON KPR MAKSIMAL</div>
                        <div class="text-2xl font-extrabold text-slate-800" x-text="formatRp(maxLoan)">Rp 0</div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-100 flex flex-col items-center text-center relative">
                        <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">MAX. ANGSURAN PER BULAN</div>
                        <div class="text-2xl font-extrabold" :class="maxInstallment <= 0 && debt > 0 ? 'text-red-500' : 'text-slate-800'" x-text="formatRp(maxInstallment)">Rp 0</div>
                        <div x-show="maxInstallment <= 0 && debt > 0" class="mt-2 text-xs font-bold text-red-500 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100 absolute -bottom-5" style="display: none;">
                            Beban utang Anda terlalu tinggi (Melebihi 40% Gaji)
                        </div>
                    </div>
                </div>

                {{-- CALL TO ACTION --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mt-6">
                    <div class="p-6 md:p-8 text-center">
                        <h2 class="text-lg font-extrabold text-slate-800 uppercase tracking-wide mb-3">CARI PROPERTI SESUAI KEMAMPUAN</h2>
                        <p class="text-slate-500 text-sm mb-6 max-w-lg mx-auto">Sistem menyarankan harga properti di bawah batas maksimal Anda agar profil keuangan tetap aman dan disetujui Bank.</p>
                        
                        <a :href="maxPrice > 0 ? '{{ route('properti') }}?max_price=' + maxPrice : '#'" 
                           class="inline-flex justify-center items-center gap-2 px-8 py-3.5 bg-[#0194F3] hover:bg-blue-600 hover:-translate-y-0.5 text-white font-bold rounded-full shadow-md hover:shadow-lg transition-all duration-200"
                           :class="{ 'opacity-50 pointer-events-none': maxPrice <= 0 }">
                            Cari Properti Harga Segini 
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kemampuanKPR', () => ({
        incomeStr: '',
        debtStr: '',
        dpStr: '',
        interest: 7,
        tenor: 15,

        formatInput(field) {
            let val = this[field].replace(/\D/g, '');
            if (val === '') {
                this[field] = '';
                return;
            }
            this[field] = new Intl.NumberFormat('id-ID').format(parseInt(val));
        },

        get income() {
            return parseInt(this.incomeStr.replace(/\D/g, '')) || 0;
        },
        
        get debt() {
            return parseInt(this.debtStr.replace(/\D/g, '')) || 0;
        },

        get dp() {
            return parseInt(this.dpStr.replace(/\D/g, '')) || 0;
        },

        get maxInstallment() {
            let maxInst = (this.income * 0.4) - this.debt;
            return maxInst > 0 ? maxInst : 0;
        },

        get maxLoan() {
            if (this.maxInstallment <= 0 || this.tenor <= 0) return 0;
            
            let r = (this.interest / 100) / 12;
            let n = this.tenor * 12;
            
            if (r === 0) return this.maxInstallment * n;
            
            let maxL = this.maxInstallment * ( (1 - Math.pow(1 + r, -n)) / r );
            return Math.floor(maxL);
        },

        get maxPrice() {
            return this.maxLoan + this.dp;
        },

        formatRp(val) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
        }
    }))
})
</script>
@endpush
@endsection
