@extends('partner.layouts.app')

@section('title','Notifikasi')
@section('page-title','Notifikasi')
@section('page-subtitle','Inbox notifikasi untuk Mitra')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-end">
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button class="px-4 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm">
                Tandai semua dibaca
            </button>
        </form>
    </div>

    <div class="space-y-3">
        @foreach($notifications as $n)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="font-extrabold text-slate-900">
                        {{ data_get($n->data,'title','Notifikasi') }}
                    </div>
                    <div class="text-sm font-semibold text-slate-700 mt-1">
                        {{ data_get($n->data,'message','') }}
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-semibold">
                        {{ $n->created_at?->format('d M Y H:i') }}
                        @if(is_null($n->read_at))
                            • <span style="color:#0194F3;" class="font-extrabold">Belum dibaca</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if(!is_null(data_get($n->data,'url')))
                        <a href="{{ data_get($n->data,'url') }}"
                           class="px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm">
                            Buka
                        </a>
                    @endif

                    @if(is_null($n->read_at))
                        <form method="POST" action="{{ route('notifications.markRead', $n->id) }}">
                            @csrf
                            <button class="px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm">
                                Tandai dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="pt-2">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
