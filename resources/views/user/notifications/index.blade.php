@extends('user.layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Inbox notifikasi untuk akun kamu')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Notifikasi</h2>
            <p class="mt-1 text-sm font-semibold text-slate-600">
                Semua notifikasi dari sistem & admin akan muncul di sini.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button
                    class="px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm text-slate-800">
                    Tandai semua dibaca
                </button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="font-extrabold text-slate-900">Daftar Notifikasi</div>
            <div class="text-xs font-semibold text-slate-500">
                Total: {{ $notifications->total() }}
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($notifications as $n)
                @php
                    $isUnread = is_null($n->read_at);
                    $title = data_get($n->data, 'title', 'Notifikasi');
                    $message = data_get($n->data, 'message', '');
                    $url = data_get($n->data, 'url', null);
                @endphp

                <div class="p-5 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="mt-0.5 h-10 w-10 rounded-2xl grid place-items-center border"
                             style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                            <i data-lucide="bell" class="w-5 h-5" style="color:#0194F3;"></i>
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <div class="font-extrabold text-slate-900 truncate">{{ $title }}</div>
                                @if($isUnread)
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold"
                                          style="background:rgba(1,148,243,0.12); color:#0194F3;">
                                        Baru
                                    </span>
                                @endif
                            </div>

                            <div class="mt-1 text-sm font-semibold text-slate-700 break-words">
                                {{ $message }}
                            </div>

                            <div class="mt-2 text-xs font-semibold text-slate-500">
                                {{ optional($n->created_at)->format('d M Y H:i') }}
                                @if($isUnread)
                                    • <span class="font-extrabold" style="color:#0194F3;">Belum dibaca</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if($url)
                            <a href="{{ $url }}"
                               class="px-4 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm">
                                Buka
                            </a>
                        @endif

                        @if($isUnread)
                            <form method="POST" action="{{ route('notifications.markRead', $n->id) }}">
                                @csrf
                                <button class="px-4 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm">
                                    Tandai dibaca
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-10 text-center">
                    <div class="text-slate-900 font-extrabold">Belum ada notifikasi</div>
                    <div class="text-sm font-semibold text-slate-600 mt-1">
                        Kalau admin mengirim notifikasi, akan muncul di sini.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="pt-1">
        {{ $notifications->links() }}
    </div>

</div>
@endsection
