@extends('layouts.front')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-2xl">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="upload-cloud" class="w-8 h-8 text-[#0194F3]"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-800 mb-2">Upload Bukti Transfer</h1>
                <p class="text-slate-500">Silakan transfer sesuai nominal dan upload bukti transfer Anda di bawah ini.</p>
            </div>

            <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 mb-8 text-center">
                <p class="text-sm text-slate-500 mb-2">Total yang harus ditransfer:</p>
                @if($transaction->unique_code)
                    <div class="text-3xl font-black text-[#0194F3] mb-1">Rp {{ number_format($transaction->amount + $transaction->unique_code, 0, ',', '.') }}</div>
                    <p class="text-xs text-orange-500 font-bold mb-4 bg-orange-50 inline-block px-3 py-1 rounded-full">
                        Termasuk kode unik: +{{ $transaction->unique_code }}
                    </p>
                @else
                    <div class="text-3xl font-black text-[#0194F3] mb-4">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                @endif
                
                <p class="text-sm font-semibold text-slate-700 mb-1">Ke Rekening Admin (Manual Transfer)</p>
                <div class="mt-4 text-left border-t border-slate-200 pt-4">
                    @php
                        $offlineMethods = \App\Models\OfflinePaymentMethod::where('is_active', true)->get();
                    @endphp
                    @foreach($offlineMethods as $method)
                    <div class="bg-white p-4 rounded-lg border border-slate-100 mb-3 shadow-sm">
                        <div class="font-bold text-slate-800">{{ $method->name }}</div>
                        <div class="font-mono text-[#0194F3] text-lg">{{ $method->account_number }}</div>
                        <div class="text-sm text-slate-600">A/N: {{ $method->account_name }}</div>
                        @if($method->swift_code)
                        <div class="text-xs text-slate-500 mt-1">SWIFT: <span class="font-mono">{{ $method->swift_code }}</span></div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 mt-4">Pastikan transfer tepat hingga 3 digit terakhir sesuai total di atas.</p>
            </div>

            <form action="{{ route('listing_promotions.store_proof', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Foto Bukti Transfer</label>
                    <input type="file" name="payment_proof" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*" required>
                    @error('payment_proof') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" class="w-full py-4 bg-[#0194F3] hover:bg-blue-600 text-white font-bold text-lg rounded-xl transition shadow-lg shadow-[#0194F3]/30">
                    Kirim Bukti Transfer
                </button>
            </form>
            
        </div>
        
    </div>
</div>
@endsection
