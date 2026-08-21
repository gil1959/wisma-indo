@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
<div class="space-y-5" x-data="{
    deleteModal: false,
    popupModal: false,
    impersonateModal: false,
    formToSubmit: null,
    userName: '',

    openDelete(form) {
        this.formToSubmit = form;
        this.deleteModal = true;
    },
    submitDelete() {
        this.formToSubmit.submit();
    },

    openImpersonate(form, name) {
        this.formToSubmit = form;
        this.userName = name;
        this.impersonateModal = true;
    },
    submitImpersonate() {
        this.formToSubmit.submit();
    }
}">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Users</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola akun user (search, lihat detail, edit, hapus)</p>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:w-auto">
            <div class="flex items-center gap-2">
                <div class="relative flex-1 sm:w-80">
                    <input type="text"
                           name="q"
                           value="{{ $q }}"
                           placeholder="Cari nama / email / no hp..."
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:ring-0 focus:outline-none">
                </div>
                <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
                        style="background:#0194F3;">
                    Cari
                </button>
            </div>
        </form>
        <div class="flex gap-2">
            <button @click="popupModal = true" class="px-4 py-2.5 rounded-2xl font-extrabold text-slate-700 bg-slate-200 hover:bg-slate-300 transition">
                Edit Pop up User
            </button>
            <a href="{{ route('admin.users.create') }}"
               class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
               style="background:#0194F3;">
               Tambah User
            </a>
        </div>
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
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">No HP</th>
                        <th class="px-5 py-3">Verified</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">{{ $u->name }}</div>
                                <div class="text-xs text-slate-500">{{ $u->email }}</div>
                            </td>
                            <td class="px-5 py-4">
                                {{ $u->phone ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($u->email_verified_at)
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(16,185,129,0.10); border-color: rgba(16,185,129,0.25); color:#059669;">
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(244,63,94,0.08); border-color: rgba(244,63,94,0.25); color:#e11d48;">
                                        Not Verified
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.users.add_quota', $u) }}" method="POST" class="inline-block" id="form-add-50-{{ $u->id }}">
                                        @csrf
                                        <input type="hidden" name="amount" value="50">
                                        <button type="button" onclick="confirmAdd50('form-add-50-{{ $u->id }}', '{{ addslashes($u->name) }}')"
                                                class="px-3 py-2 rounded-xl font-extrabold text-white hover:opacity-90"
                                                style="background:#f59e0b;">
                                            +50 Iklan
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.users.show', $u) }}"
                                       class="px-3 py-2 rounded-xl font-extrabold text-slate-700 border border-slate-200 hover:bg-slate-50">
                                        Detail
                                    </a>

                                    <a href="{{ route('admin.users.edit', $u) }}"
                                       class="px-3 py-2 rounded-xl font-extrabold text-white"
                                       style="background:#0194F3;">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.users.impersonate', $u) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="button" @click="openImpersonate($el.closest('form'), '{{ addslashes($u->name) }}')"
                                                class="px-3 py-2 rounded-xl font-extrabold text-emerald-700 bg-emerald-100 hover:bg-emerald-200">
                                            Login As
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline-block">
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
                                Tidak ada user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div x-show="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div @click.away="deleteModal = false" class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl transform transition-all scale-100 opacity-100">
            <div class="mb-4 flex items-center justify-center w-12 h-12 rounded-full bg-rose-100 text-rose-600 mx-auto">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <h3 class="text-center text-lg font-extrabold text-slate-900 mb-2">Hapus User?</h3>
            <p class="text-center text-sm text-slate-500 mb-6">Tindakan ini tidak bisa dibatalkan. Data user ini akan dihapus permanen.</p>
            <div class="flex gap-3">
                <button @click="deleteModal = false" class="flex-1 rounded-xl bg-slate-100 py-3 text-sm font-bold text-slate-700 hover:bg-slate-200 transition">Batal</button>
                <button @click="submitDelete" class="flex-1 rounded-xl bg-rose-600 py-3 text-sm font-bold text-white hover:bg-rose-700 transition">Hapus</button>
            </div>
        </div>
    </div>

    {{-- Modal Edit Popup User --}}
    <div x-show="popupModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div @click.away="popupModal = false" class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl transform transition-all scale-100 opacity-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-extrabold text-slate-900">Edit Pop up Dashboard User</h3>
                <button @click="popupModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="{{ route('admin.users.update_popup') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Teks Pengumuman (Kosongkan jika ingin menyembunyikan popup)</label>
                    <textarea name="popup_text" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3]">{{ \App\Models\Setting::getValue('user_dashboard_popup') }}</textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="popupModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 text-sm font-bold text-slate-700 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#0194F3] text-sm font-bold text-white shadow-lg hover:bg-[#027dd1] transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL IMPERSONATE --}}
    <template x-teleport="body">
        <div x-show="impersonateModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div x-show="impersonateModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="impersonateModal = false"></div>
            <div x-show="impersonateModal" x-transition class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center z-10">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="log-in" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Login Sebagai User</h3>
                <p class="text-slate-500 mb-6 text-sm">Anda akan login sebagai <strong class="text-slate-700" x-text="userName"></strong>. Anda bisa kembali ke akun Admin nanti.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="impersonateModal = false" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="button" @click="submitImpersonate()" class="px-4 py-2 rounded-xl font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition">Ya, Login</button>
                </div>
            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
    function confirmAdd50(formId, userName) {
        Swal.fire({
            title: 'Tambahkan 50 Kuota?',
            text: "User " + userName + " akan mendapatkan 50 kuota iklan gratis tambahan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0194F3',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Tambahkan'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endpush
@endsection
