@extends('layouts.admin')

@section('title', 'Kirim Notifikasi')
@section('page-title', 'Kirim Notifikasi')
@section('page-subtitle', 'Broadcast ke user / mitra (partner)')

@section('content')
<div class="space-y-5" x-data="notifSender()">

    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Kirim Notifikasi</h2>
            <p class="mt-1 text-sm text-slate-600">Admin dapat mengirim notifikasi ke role tertentu, semua atau user tertentu.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
            <div class="font-extrabold mb-2">Periksa input:</div>
            <ul class="list-disc ml-5 text-sm font-semibold">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.notifications.store') }}"
          class="rounded-2xl border border-slate-200 bg-white p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Kirim ke Role</label>
                <select name="target_role" x-model="targetRole"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                    <option value="user">User</option>
                    <option value="partner">Mitra (Partner)</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Mode Pengiriman</label>
                <select name="send_mode" x-model="sendMode"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                    <option value="all">Kirim ke Semua</option>
                    <option value="selected">Pilih Tertentu</option>
                </select>
            </div>
        </div>

        <div x-show="sendMode==='selected'" x-cloak>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Pilih Penerima</label>

            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="font-extrabold text-slate-900 mb-2">Daftar User</div>
                    <select name="recipient_ids[]" multiple size="10"
                            x-show="targetRole==='user'" x-cloak
                            class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                        @endforeach
                    </select>

                    <select name="recipient_ids[]" multiple size="10"
                            x-show="targetRole==='partner'" x-cloak
                            class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
                        @foreach($partners as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                        @endforeach
                    </select>

                    <div class="text-xs text-slate-500 mt-2">
                        Tips: tahan Ctrl/Cmd untuk pilih banyak.
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50">
                    <div class="font-extrabold text-slate-900 mb-2">Catatan</div>
                    <ul class="text-sm text-slate-700 font-semibold list-disc ml-5 space-y-1">
                        <li>Notif masuk ke bell icon (panel user/mitra).</li>
                        <li>Kalau user setuju permission push, akan muncul push notif di device/browser.</li>
                        <li>Kalau push ditolak, tetap masuk notifikasi in-app.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Isi Pesan</label>
            <textarea name="message" rows="5"
                      class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800"
                      placeholder="Tulis pesan notifikasi...">{{ old('message') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit"
                    class="px-5 py-3 rounded-2xl font-extrabold text-white"
                    style="background:#0194F3;">
                Kirim Notifikasi
            </button>
        </div>
    </form>
</div>

<script>
function notifSender(){
    return {
        targetRole: "{{ old('target_role','user') }}",
        sendMode: "{{ old('send_mode','all') }}",
    }
}
</script>
@endsection
