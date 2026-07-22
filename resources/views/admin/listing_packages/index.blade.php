@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Paket Promosi (Sundulan & Premium)</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola daftar paket promosi iklan yang bisa dibeli pengguna.</p>
        </div>
        <a href="{{ route('admin.listing-packages.create') }}" class="px-4 py-2 bg-[#0194F3] text-white rounded-lg font-medium hover:bg-blue-600 transition flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Paket
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-800 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Nama Paket</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Value (Kali)</th>
                        <th class="px-6 py-4">Harga</th>
                        <th class="px-6 py-4">Diskon Label</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($packages as $pkg)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $pkg->name }}</td>
                            <td class="px-6 py-4 text-slate-800">
                                @if($pkg->type == 'sundul')
                                    <span class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs rounded-full font-medium">Sundul</span>
                                @else
                                    <span class="px-2 py-1 bg-amber-50 text-amber-700 text-xs rounded-full font-medium">Premium</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $pkg->amount }}</td>
                            <td class="px-6 py-4 text-emerald-600 font-bold">Rp {{ number_format($pkg->price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($pkg->discount_label)
                                    <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded">{{ $pkg->discount_label }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($pkg->is_active)
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full font-medium">Aktif</span>
                                @else
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded-full font-medium">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.listing-packages.edit', $pkg->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg text-xs font-semibold transition">
                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i> Edit
                                </a>
                                
                                <div x-data="{ showModal: false }" class="inline-block ml-2">
                                    <button @click="showModal = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-semibold transition">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                    </button>
                                    
                                    <!-- Modal Konfirmasi Hapus Alpine JS -->
                                    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-slate-900 bg-opacity-50 transition-opacity" aria-hidden="true"></div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div x-show="showModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <div class="sm:flex sm:items-start">
                                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                            <i data-lucide="alert-triangle" class="h-6 w-6 text-red-600"></i>
                                                        </div>
                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                            <h3 class="text-lg leading-6 font-medium text-slate-900" id="modal-title">Hapus Paket</h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus paket promosi ini? Tindakan ini tidak dapat dibatalkan.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <form action="{{ route('admin.listing-packages.destroy', $pkg->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                            Ya, Hapus
                                                        </button>
                                                    </form>
                                                    <button @click="showModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                Belum ada paket promosi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
