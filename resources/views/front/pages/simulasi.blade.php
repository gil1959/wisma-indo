@extends('layouts.front')

@section('title', 'Simulasi KPR Rumaindo')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4" style="font-family: 'Inter', sans-serif;">SIMULASI KPR RUMAINDO</h1>
            <p class="text-slate-600 max-w-2xl mx-auto text-sm md:text-base">
                Hitung dan estimasikan angsuran bulanan Kredit Pemilikan Rumah (KPR) Anda secara instan, akurat, dan transparan.
            </p>
        </div>

        <div x-data="kalkulatorKPR()" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- KALKULATOR PANEL --}}
            <div class="lg:col-span-1 bg-white rounded-3xl shadow-sm border border-slate-100 p-6 md:p-8">
                <h2 class="text-lg font-extrabold text-slate-800 mb-6">KALKULATOR KPR</h2>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Harga Properti</label>
                        <input type="number" x-model="hargaProperti" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3] focus:ring-opacity-20 text-slate-700">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Uang Muka (DP)</label>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <input type="number" x-model="dpPercent" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3] focus:ring-opacity-20 text-slate-700" placeholder="0-100">
                            </div>
                            <div class="col-span-2 relative">
                                <input type="number" x-model="dpNominal" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed" readonly>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Pokok Pinjaman</label>
                        <input type="number" x-model="pokokPinjaman" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed font-bold" readonly>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Bunga Tahunan (%)</label>
                            <input type="number" step="0.01" x-model="bungaTahunan" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3] focus:ring-opacity-20 text-slate-700">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase text-right">Bunga Bulanan (%)</label>
                            <input type="text" :value="formatDecimal(bungaBulanan * 100)" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed text-right" readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Tenor Pinjaman (Tahun)</label>
                        <input type="number" x-model="tenorTahun" class="w-full rounded-xl border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3] focus:ring-opacity-20 text-slate-700" placeholder="Contoh: 15">
                    </div>

                    <button @click="hitung()" class="w-full py-3 mt-4 bg-[#0194F3] hover:bg-blue-600 hover:-translate-y-0.5 text-white font-bold rounded-full shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                        Hitung Simulasi 
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
            </div>

            {{-- HASIL PANEL --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- KOTAK BIRU HASIL --}}
                <div class="bg-[#EFF6FF] rounded-3xl p-6 md:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <div>
                        <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Estimasi Angsuran Bulanan</div>
                        <div class="text-4xl md:text-5xl font-extrabold text-[#0194F3]" x-text="formatRupiah(cicilanBulanan)">Rp 0</div>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <div class="bg-white border border-blue-100 text-slate-600 text-sm font-semibold px-4 py-2 rounded-full shadow-sm">
                            Tenor: <span x-text="tenorTahun"></span> Tahun (<span x-text="tenorTahun * 12"></span> Bulan)
                        </div>
                    </div>
                </div>

                {{-- TABEL AMORTISASI --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 md:p-8 border-b border-slate-100">
                        <h2 class="text-lg font-extrabold text-slate-800 uppercase tracking-wide">Tabel Amortisasi Cicilan</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-semibold text-center w-20">Bulan</th>
                                    <th class="px-6 py-4 font-semibold">Pokok Pinjaman</th>
                                    <th class="px-6 py-4 font-semibold">Bunga Bulanan</th>
                                    <th class="px-6 py-4 font-semibold">Pokok Bulanan</th>
                                    <th class="px-6 py-4 font-semibold">Cicilan Bulanan</th>
                                    <th class="px-6 py-4 font-semibold text-right">Sisa Pinjaman</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                                <template x-for="(item, index) in tabelAmortisasi" :key="index">
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 font-semibold text-center" x-text="item.bulan"></td>
                                        <td class="px-6 py-4" x-text="formatNumber(item.pokok_pinjaman)"></td>
                                        <td class="px-6 py-4" x-text="formatNumber(item.bunga_bulanan)"></td>
                                        <td class="px-6 py-4" x-text="formatNumber(item.pokok_bulanan)"></td>
                                        <td class="px-6 py-4 font-bold" x-text="formatNumber(item.cicilan_bulanan)"></td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-900" x-text="formatNumber(item.sisa_pinjaman)"></td>
                                    </tr>
                                </template>
                                <tr x-show="tabelAmortisasi.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">Silakan isi form dan klik Hitung Simulasi untuk melihat tabel amortisasi.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- CATATAN KUNING --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div class="text-sm text-amber-800">
                        <span class="font-bold">Catatan:</span> Hasil simulasi ini merupakan estimasi awal/perkiraan kasar. Nilai suku bunga dan ketentuan cicilan real dapat berbeda-beda tergantung pada kebijakan bank penyedia KPR pilihan Anda serta riwayat kredit individu Anda.
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
    function kalkulatorKPR() {
        return {
            hargaProperti: null,
            dpPercent: 20,
            bungaTahunan: 4,
            tenorTahun: 12,
            cicilanBulanan: 0,
            tabelAmortisasi: [],

            // Computed / Auto calculated variables
            get dpNominal() {
                return Math.round((this.hargaProperti || 0) * (this.dpPercent || 0) / 100);
            },
            get pokokPinjaman() {
                return (this.hargaProperti || 0) - this.dpNominal;
            },
            get bungaBulanan() {
                return (this.bungaTahunan || 0) / 100 / 12;
            },

            formatRupiah(number) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(number || 0);
            },
            formatNumber(number) {
                return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(number || 0);
            },
            formatDecimal(number) {
                return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(number || 0);
            },

            hitung() {
                if (!this.hargaProperti || this.hargaProperti <= 0) return;
                
                let P = this.pokokPinjaman;
                let r = this.bungaBulanan;
                let n = (this.tenorTahun || 0) * 12;

                if (P <= 0 || n <= 0) {
                    this.tabelAmortisasi = [];
                    this.cicilanBulanan = 0;
                    return;
                }

                if (r > 0) {
                    // M = P * r * (1 + r)^n / [(1 + r)^n - 1]
                    let pow = Math.pow(1 + r, n);
                    this.cicilanBulanan = Math.round((P * r * pow) / (pow - 1));
                } else {
                    this.cicilanBulanan = Math.round(P / n);
                }

                // Hitung amortisasi
                let sisa = P;
                let amortisasi = [];

                for (let i = 1; i <= n; i++) {
                    let bunga = Math.round(sisa * r);
                    let pokok = this.cicilanBulanan - bunga;
                    let pokokPinjamanAwalBulan = sisa;
                    
                    sisa = sisa - pokok;
                    if (sisa < 0) sisa = 0; // adjust last month

                    amortisasi.push({
                        bulan: i,
                        pokok_pinjaman: pokokPinjamanAwalBulan,
                        bunga_bulanan: bunga,
                        pokok_bulanan: pokok,
                        cicilan_bulanan: this.cicilanBulanan,
                        sisa_pinjaman: sisa
                    });
                }
                this.tabelAmortisasi = amortisasi;
            }
        }
    }
</script>
@endsection
