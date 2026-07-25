@extends('layouts.admin')

@section('title', 'Halaman Statis')
@section('page-title', 'Halaman Statis')

@section('content')
<div class="space-y-5" x-data="{
    deleteModal: false,
    formToSubmit: null,

    openDelete(form) {
        this.formToSubmit = form;
        this.deleteModal = true;
    },
    submitDelete() {
        this.formToSubmit.submit();
    }
}">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Kelola Halaman</h2>
            <p class="mt-1 text-sm text-slate-600">Buat dan kelola halaman statis (Tentang Kami, dll)</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
            Tambah Halaman
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-extrabold text-slate-600 uppercase">
                        <th class="px-5 py-3">Judul Halaman</th>
                        <th class="px-5 py-3">URL Slug</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($pages as $p)
                        <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4 font-extrabold text-slate-900">
                                {{ $p->title }}
                            </td>
                            <td class="px-5 py-4 text-slate-500">
                                /page/{{ $p->slug }}
                            </td>
                            <td class="px-5 py-4">
                                @if($p->is_active)
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(16,185,129,0.10); border-color: rgba(16,185,129,0.25); color:#059669;">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(244,63,94,0.08); border-color: rgba(244,63,94,0.25); color:#e11d48;">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('page.show', $p->slug) }}" target="_blank"
                                       class="px-3 py-2 rounded-xl font-extrabold text-slate-700 border border-slate-200 hover:bg-slate-50">
                                        Lihat
                                    </a>
                                    <a href="{{ route('admin.pages.edit', $p) }}"
                                       class="px-3 py-2 rounded-xl font-extrabold text-white"
                                       style="background:#0194F3;">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.pages.destroy', $p) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="openDelete($el.closest('form'))"
                                                class="px-3 py-2 rounded-xl font-extrabold text-white"
                                                style="background:#ef4444;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">
                                Belum ada halaman yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-200">
            {{ $pages->links() }}
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <template x-teleport="body">
        <div x-show="deleteModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div x-show="deleteModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteModal = false"></div>
            <div x-show="deleteModal" x-transition class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center z-10">
                <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Hapus Halaman?</h3>
                <p class="text-slate-500 mb-6 text-sm">Halaman ini akan dihapus secara permanen dari sistem.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="deleteModal = false" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="button" @click="submitDelete()" class="px-4 py-2 rounded-xl font-bold bg-rose-600 text-white hover:bg-rose-700 transition">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
