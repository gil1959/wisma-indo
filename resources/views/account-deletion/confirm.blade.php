@extends('layouts.front')
@section('title', 'Konfirmasi Penghapusan Akun WismaIndo')
@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm" x-data="{ open: false, busy: false }">
            <h1 class="text-2xl font-extrabold text-slate-900 mb-3">Konfirmasi penghapusan akun</h1>
            <p class="text-slate-600 mb-6">Akun: <strong>{{ $accountName }}</strong></p>
            @include('account-deletion.policy')
            @if($errors->any())<p role="status" class="mt-4 text-sm text-red-700">{{ $errors->first() }}</p>@endif
            <button type="button" @click="open = true; $nextTick(() => $refs.confirmation.focus())" class="mt-6 px-6 py-3 bg-red-700 hover:bg-red-800 text-white font-bold rounded-xl">Lanjutkan penghapusan</button>
            <div x-show="open" x-cloak @keydown.escape.window="if (!busy) open = false" style="z-index:100" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60">
                <form method="POST" action="{{ $action }}" @submit="busy = true" role="dialog" aria-modal="true" aria-labelledby="final-delete-title" class="bg-white rounded-3xl p-6 w-full max-w-lg shadow-xl">
                    @csrf
                    <h2 id="final-delete-title" class="text-xl font-bold text-slate-900">Hapus akun secara permanen?</h2>
                    <p class="mt-3 text-sm text-slate-600">Tindakan ini tidak dapat dibatalkan. Ketik HAPUS untuk melanjutkan.</p>
                    <label for="confirmation" class="block mt-4 text-sm font-bold text-slate-700">Konfirmasi penghapusan</label>
                    <input id="confirmation" x-ref="confirmation" name="confirmation" required pattern="HAPUS" autocomplete="off" class="mt-2 w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 bg-white">
                    <label class="flex gap-3 mt-4 text-sm text-slate-600"><input name="consent" type="checkbox" value="1" required class="mt-1 rounded">Saya memahami akun dan data terkait akan dihapus.</label>
                    <div class="flex flex-wrap justify-end gap-3 mt-6">
                        <button type="button" :disabled="busy" @click="open = false" class="px-5 py-3 border border-slate-200 rounded-xl font-semibold text-slate-700">Batal</button>
                        <button type="submit" :disabled="busy" class="px-5 py-3 bg-red-700 text-white rounded-xl font-bold disabled:opacity-50" x-text="busy ? 'Menghapus akun' : 'Hapus akun permanen'">Hapus akun permanen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
