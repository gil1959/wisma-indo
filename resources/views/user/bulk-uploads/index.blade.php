@php $isPartner = auth()->check() && auth()->user()->hasRole('partner'); @endphp
@extends($isPartner ? 'user.layouts.app' : 'layouts.front')

@section('title', 'Riwayat Upload Massal (Excel)')

@section('content')
@if(!$isPartner)
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
@endif
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ $isPartner ? 'py-10' : '' }}" x-data="{ showModal: false, type: 'property' }">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Riwayat Upload Massal</h1>
        <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2 transition">
            <i data-lucide="upload-cloud" class="w-5 h-5"></i>
            Upload Massal Baru
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-sm">
                    <th class="p-4 font-semibold">Tipe Iklan</th>
                    <th class="p-4 font-semibold">Tanggal Upload</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Total / Sukses / Gagal</th>
                    <th class="p-4 font-semibold">Catatan Error</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($uploads as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-4 uppercase font-medium">{{ $item->type }}</td>
                    <td class="p-4">{{ $item->created_at->format('d M Y H:i') }}</td>
                    <td class="p-4">
                        @if($item->status == 'processing' || $item->status == 'pending')
                            <span class="inline-flex px-2 py-1 bg-yellow-100 text-yellow-700 rounded-md text-xs font-bold">Diproses</span>
                        @elseif($item->status == 'completed')
                            <span class="inline-flex px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-bold">Selesai</span>
                        @else
                            <span class="inline-flex px-2 py-1 bg-red-100 text-red-700 rounded-md text-xs font-bold">Gagal</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <span class="text-slate-600">{{ $item->total_rows }}</span> / 
                        <span class="text-green-600 font-bold">{{ $item->processed_rows }}</span> / 
                        <span class="text-red-600 font-bold">{{ $item->failed_rows }}</span>
                    </td>
                    <td class="p-4 max-w-xs truncate text-red-500">
                        @if($item->error_log)
                            <button @click="Swal.fire({title: 'Detail Error', html: '<div style=\'text-align:left;font-size:14px;max-height:300px;overflow-y:auto;\'><ul>@foreach($item->error_log as $err)<li>{{ $err }}</li>@endforeach</ul></div>'})" class="text-blue-500 underline text-xs">Lihat Detail</button>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-400">Belum ada riwayat upload massal.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $uploads->links() }}
        </div>
    </div>

    <!-- Modal Upload -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
        <div @click.away="showModal = false" class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Upload Massal Iklan</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('bulk-uploads.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">1. Pilih Tipe Iklan</label>
                    <select name="type" x-model="type" class="w-full border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                        <option value="property">Properti (Rumah, Tanah, dll)</option>
                        <option value="goods">Barang</option>
                        <option value="services">Jasa</option>
                    </select>
                </div>

                <div class="mb-5 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">2. Download Template</label>
                    <p class="text-xs text-slate-500 mb-3">Download format Excel terbaru sesuai tipe yang Anda pilih. Jangan ubah format baris pertamanya.</p>
                    <a :href="'/bulk-uploads/template/' + type" class="w-full block text-center bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 font-medium py-2 px-4 rounded-lg transition text-sm">
                        Download Template .XLSX
                    </a>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">3. Upload File Excel Anda</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl p-1">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition shadow-md shadow-blue-500/20">
                    Mulai Upload Data
                </button>
            </form>
        </div>
    </div>
</div>
@if(!$isPartner)
</div>
@endif
@endsection
