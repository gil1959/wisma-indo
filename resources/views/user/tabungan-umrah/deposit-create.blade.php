@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $isEn ? 'Add Deposit' : 'Tambah Setoran')
@section('page-title', $isEn ? 'Add Deposit' : 'Tambah Setoran')
@section('page-subtitle', $isEn ? 'Enter amount and upload payment proof' : 'Masukkan nominal dan upload bukti pembayaran')


@section('content')
@if($errors->any())
<div class="mb-4 alert-error">
    <div class="font-semibold mb-1">{{ $isEn ? 'There are errors:' : 'Terdapat error:' }}</div>

    <ul class="list-disc pl-5 space-y-1">
        @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card p-5 relative overflow-hidden max-w-3xl">
    <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
        style="background: radial-gradient(circle, rgba(1,148,243,0.16) 0%, transparent 65%);"></div>

    <div class="flex items-start justify-between gap-3 relative">
        <div>
            <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Deposit Form' : 'Form Setoran' }}</h2>
            <p class="text-sm text-slate-500 mt-1">{{ $isEn ? 'Choose destination account, enter amount, then upload proof.' : 'Pilih rekening tujuan, masukkan nominal, lalu upload bukti.' }}</p>
        </div>
        <span class="pill pill-azure shrink-0">
            <i data-lucide="credit-card" class="w-4 h-4"></i>
            {{ $isEn ? 'Deposit' : 'Setoran' }}
        </span>
    </div>

    <form method="POST" action="{{ route('user.tabungan-umrah.deposits.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4 relative">
        @csrf

        <div>
            <label class="label">{{ $isEn ? 'Destination Account' : 'Rekening Tujuan' }}</label>


            @if($methods->count() === 0)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <div class="font-extrabold text-amber-700">{{ $isEn ? 'No active bank accounts' : 'Belum ada rekening aktif' }}</div>

            </div>
            @else
            <div class="space-y-3">
                @foreach($methods as $m)
                <label class="block rounded-2xl border border-slate-200 bg-white p-4 cursor-pointer hover:bg-slate-50">
                    <div class="flex items-start gap-3">
                        <input
                            type="radio"
                            name="payment_method_id"
                            value="{{ $m->id }}"
                            class="mt-1"
                            {{ (string)old('payment_method_id') === (string)$m->id ? 'checked' : '' }}
                            required>

                        <div class="min-w-0 flex-1">
                            {{-- NAMA BANK --}}
                            <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Bank Name' : 'Nama Bank' }}</div>
                            <div class="mt-1 text-sm font-extrabold text-slate-900">
                                {{ $m->bank_name }}
                            </div>

                            {{-- NO REKENING + COPY --}}
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Account No.' : 'No Rekening' }}</div>
                                    <div class="mt-1 text-sm font-extrabold text-slate-900 break-all">
                                        {{ $m->account_number }}
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-ghost !px-3 !py-2 text-xs shrink-0"
                                    onclick="copyText('{{ $m->account_number }}')"
                                    aria-label="Salin nomor rekening">
                                    Salin
                                </button>
                            </div>

                            {{-- ATAS NAMA --}}
                            <div class="mt-3">
                                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Account Holder' : 'Atas Nama' }}</div>
                                <div class="mt-1 text-sm font-extrabold text-slate-900">
                                    {{ $m->account_holder }}
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="text-xs text-slate-500 mt-2">
                {{ $isEn ? 'Select one destination account before submitting.' : 'Pilih salah satu rekening tujuan sebelum submit setoran.' }}

            </div>
            @endif

            @error('payment_method_id')
            <div class="text-sm text-rose-600 mt-2">{{ $message }}</div>
            @enderror
        </div>


        <div>
            <label class="label">{{ $isEn ? 'Deposit Amount' : 'Jumlah Setoran' }}</label>
            <input type="number" name="amount" min="1000" class="input" value="{{ old('amount') }}" placeholder={{ $isEn ? 'Enter amount' : 'Masukkan nominal' }} required>
        </div>

        <div>
            <label class="label">{{ $isEn ? 'Upload Payment Proof' : 'Upload Bukti Pembayaran' }}</label>
            <input type="file" name="proof_image" class="input" accept="image/*" required>
            <div class="text-xs text-slate-500 mt-1">{{ $isEn ? 'Image format, max 4MB.' : 'Format gambar, max 4MB.' }}</div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between pt-2">
            <a href="{{ route('user.tabungan-umrah.index') }}" class="btn btn-ghost">
                <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
                {{ $isEn ? 'Back' : 'Kembali' }}
            </a>

            <button type="submit" class="btn btn-primary">
                <i data-lucide="send" class="w-4 h-4"></i>
                {{ $isEn ? 'Submit Deposit' : 'Submit Setoran' }}
            </button>
        </div>
    </form>
</div>
<script>
    function copyText(text) {
        if (!text) return;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => toastCopy()).catch(() => fallbackCopy(text));
        } else {
            fallbackCopy(text);
        }
    }

    function fallbackCopy(text) {
        const el = document.createElement('textarea');
        el.value = text;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        toastCopy();
    }

    function toastCopy() {
        let t = document.getElementById('copyToast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'copyToast';
            t.className = 'fixed bottom-6 right-6 z-50 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 text-sm font-extrabold shadow-sm';
            t.innerText = 'Nomor rekening tersalin';
            document.body.appendChild(t);
        }
        t.style.opacity = '1';
        clearTimeout(window.__copyToastTimer);
        window.__copyToastTimer = setTimeout(() => {
            t.style.opacity = '0';
        }, 1200);
    }
</script>

@endsection