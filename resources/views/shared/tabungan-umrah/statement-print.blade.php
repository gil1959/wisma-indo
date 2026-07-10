<!doctype html>
@php $isEn = app()->getLocale() === 'en'; @endphp
<html lang="{{ $isEn ? 'en' : 'id' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>{{ $isEn ? 'Umrah Savings Statement' : 'Rekening Koran Tabungan Umrah' }} - {{ $siteSettings['seo_site_title'] ?? config('app.name') }}</title>
    {{-- Fonts + Theme CSS (sama seperti layout user/partner/admin) --}}
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700;800&display=swap">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <style>
        body {
            font-family: Nunito, ui-sans-serif, system-ui;
        }

        @page {
            size: A4;
            margin: 12mm;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .print-shadowless {
                box-shadow: none !important;
            }

            .print-borderless {
                border: 0 !important;
            }

            .print-bg-white {
                background: #fff !important;
            }
        }
    </style>
</head>

<body class="bg-slate-50 print-bg-white">
    <div class="max-w-5xl mx-auto px-4 py-6">

        {{-- Actions --}}
        <div class="no-print flex items-center justify-end gap-2 mb-4">
            <button onclick="window.close()"
                class="px-4 py-2 rounded-2xl border border-slate-200 bg-white text-slate-800 font-extrabold hover:bg-slate-50">
                Tutup
            </button>
            <button onclick="window.print()"
                class="px-4 py-2 rounded-2xl bg-[#0194F3] text-white font-extrabold hover:opacity-90">
                Print
            </button>
        </div>

        {{-- Paper --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm print-shadowless print-borderless overflow-hidden">

            {{-- Header --}}
            <div class="p-6 border-b border-slate-200 flex items-start justify-between gap-6">
                <div class="flex items-center gap-4 min-w-0">
                    <img
                        src="{{ $siteSettings['site_logo'] ?? asset('images/logo.png') }}"
                        alt="{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}"
                        class="h-10 w-auto object-contain" />

                    <div class="min-w-0">
                        <div class="text-lg font-extrabold text-slate-900 leading-tight">
                            {{ $isEn ? 'Umrah Savings Statement' : 'Rekening Koran Tabungan Umrah' }}
                        </div>
                        <div class="text-sm text-slate-500 font-bold">
                            {{ $isEn ? 'Savings movements are based on transactions in the system' : 'Mutasi tabungan berdasarkan transaksi pada sistem' }}
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Statement No.' : 'Nomor Statement' }}</div>
                    <div class="text-sm font-extrabold text-slate-900">{{ $statementNo ?? '-' }}</div>

                    <div class="mt-2 text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Printed' : 'Dicetak' }}</div>
                    <div class="text-sm font-extrabold text-slate-900">
                        {{ now()->format('d/m/Y H:i') }} <span class="text-slate-400"></span> {{ $contextLabel ?? '—' }}
                    </div>
                </div>
            </div>

            {{-- Identity + Summary --}}
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="border border-slate-200 rounded-2xl p-4">
                        <div class="text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Customer Data' : 'Data Nasabah' }}</div>

                        <div class="mt-3 grid grid-cols-1 gap-2">
                            <div>
                                <div class="text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Name' : 'Nama' }}</div>
                                <div class="text-sm font-extrabold text-slate-900">{{ $account->full_name ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-slate-500 font-extrabold">WhatsApp</div>
                                <div class="text-sm font-extrabold text-slate-900">{{ $account->whatsapp ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Savings Type' : 'Jenis Tabungan' }}</div>
                                <div class="text-sm font-extrabold text-slate-900">
                                    {{ ($account->saving_type ?? null) === 'haji_furoda' ? 'Haji Furoda' : 'Umroh Reguler' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-2xl p-4">
                        <div class="text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Period & Summary' : 'Periode & Ringkasan' }}</div>

                        <div class="mt-3 grid grid-cols-1 gap-2">
                            <div>
                                <div class="text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Period' : 'Periode' }}</div>
                                <div class="text-sm font-extrabold text-slate-900">
                                    {{ ($from ?? now()->startOfMonth())->format('d/m/Y') }}
                                    <span class="text-slate-400">—</span>
                                    {{ ($to ?? now()->endOfMonth())->format('d/m/Y') }}
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div class="border border-slate-200 rounded-2xl p-3">
                                    <div class="text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Opening Balance' : 'Saldo Awal' }}</div>
                                    <div class="mt-1 text-sm font-extrabold text-slate-900">
                                        Rp {{ number_format((int)($openingBalance ?? 0), 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="border border-slate-200 rounded-2xl p-3">
                                    <div class="text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Closing Balance' : 'Saldo Akhir' }}</div>
                                    <div class="mt-1 text-sm font-extrabold text-slate-900">
                                        Rp {{ number_format((int)($totals['closing_balance'] ?? 0), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                                <div class="text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Total Credit (approved)' : 'Total Kredit (approved)' }}</div>(approved)
                            </div>
                            <div class="text-sm font-extrabold text-slate-900">
                                Rp {{ number_format((int)($totals['total_credit_approved'] ?? 0), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Table --}}
            <div class="mt-6 border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs text-slate-500 font-extrabold">
                            <th class="p-3 w-[150px]">{{ $isEn ? 'Date' : 'Tanggal' }}</th>
                            <th class="p-3">{{ $isEn ? 'Information' : 'Keterangan' }}</th>
                            <th class="p-3 w-[120px]">Ref</th>
                            <th class="p-3 w-[110px] text-right">{{ $isEn ? 'Debit' : 'Debit' }}</th>
                            <th class="p-3 w-[110px] text-right">{{ $isEn ? 'Credit' : 'Kredit' }}</th>
                            <th class="p-3 w-[130px] text-right">{{ $isEn ? 'Balance' : 'Saldo' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">

                        {{-- Saldo awal row --}}
                        <tr>
                            <td class="p-3 text-slate-600 font-bold">{{ ($from ?? now())->format('d/m/Y') }}</td>
                            <td class="p-3 font-extrabold text-slate-900">{{ $isEn ? 'Opening Balance' : 'Saldo Awal' }}</td>
                            <td class="p-3 text-slate-500 font-bold">—</td>
                            <td class="p-3 text-right font-bold">Rp 0</td>
                            <td class="p-3 text-right font-bold">Rp 0</td>
                            <td class="p-3 text-right font-extrabold text-slate-900">
                                Rp {{ number_format((int)($openingBalance ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>

                        @php $count = 0; @endphp
                        @foreach(($rows ?? collect()) as $r)
                        @php
                        $count++;
                        $st = strtolower($r['status'] ?? '');
                        $badge = 'bg-slate-100 text-slate-700';
                        if ($st === 'approved') $badge = 'bg-emerald-50 text-emerald-700';
                        elseif ($st === 'pending') $badge = 'bg-amber-50 text-amber-700';
                        elseif ($st === 'rejected') $badge = 'bg-rose-50 text-rose-700';
                        @endphp

                        <tr>
                            <td class="p-3 text-slate-600 font-bold">{{ $r['date'] ?? '—' }}</td>
                            <td class="p-3">
                                <div class="font-extrabold text-slate-900">{{ $r['desc'] ?? '—' }}</div>
                                @if($st)
                                <div class="mt-2 inline-flex px-2 py-1 rounded-full text-[11px] font-extrabold {{ $badge }}">
                                    {{ strtoupper($st) }}
                                </div>
                                @endif
                            </td>
                            <td class="p-3 text-slate-500 font-bold">{{ $r['ref'] ?? '—' }}</td>
                            <td class="p-3 text-right font-bold">
                                Rp {{ number_format((int)($r['debit'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-right font-bold">
                                Rp {{ number_format((int)($r['credit'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-right font-extrabold text-slate-900">
                                Rp {{ number_format((int)($r['balance'] ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach

                        @if($count === 0)
                        <tr>
                            <td colspan="6" class="p-4 text-slate-500 font-bold">
                                {{ $isEn ? 'There are no transactions in this period.' : 'Tidak ada transaksi pada periode ini.' }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Notes + Sign --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-slate-200 rounded-2xl p-4">
                    <div class="text-xs text-slate-500 font-extrabold"> {{ $isEn ? 'TNotesNotes' : ' Catatan' }}</div>
                    <div class="mt-2 text-sm text-slate-600 font-bold leading-relaxed">
                        {{ $isEn
    ? 'Running balance only changes for transactions with status '
    : 'Saldo berjalan hanya berubah oleh transaksi berstatus '
}}
                        <span class="font-extrabold text-slate-900">approved</span>.
                        Transaksi pending/ditolak ditampilkan untuk kebutuhan audit.
                    </div>
                </div>

                <div class="border border-slate-200 rounded-2xl p-4">
                    <div class="text-xs text-slate-500 font-extrabold">{{ $isEn ? 'Printed by' : 'Dicetak oleh' }}</div>
                    <div class="mt-8 text-sm font-extrabold text-slate-900">
                        {{ $printedBy->name ?? '-' }}
                    </div>
                    <div class="mt-6 text-xs text-slate-500 font-bold">
                        {{ $isEn ? 'Signature: ________________________' : 'Tanda tangan: ________________________' }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    </div>
</body>

</html>