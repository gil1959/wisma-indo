@extends('layouts.front')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <div class="flex items-center gap-3 mb-8">
            <i data-lucide="bell" class="w-8 h-8 text-[#0194F3]"></i>
            <h1 class="text-3xl font-extrabold text-slate-800">Notifikasi</h1>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            @forelse($notifications as $notification)
                <a href="{{ route('user.notifications.show', $notification->id) }}" class="block p-5 border-b border-slate-100 hover:bg-slate-50 transition {{ $notification->unread() ? 'bg-blue-50/30' : '' }}">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 {{ $notification->unread() ? 'bg-[#0194F3] text-white' : 'bg-slate-100 text-slate-500' }}">
                            <i data-lucide="mail" class="w-6 h-6"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="flex justify-between items-start gap-2 mb-1">
                                <h3 class="font-bold {{ $notification->unread() ? 'text-slate-900' : 'text-slate-700' }}">
                                    {{ $notification->data['title'] ?? 'Pesan Baru' }}
                                </h3>
                                <span class="text-xs text-slate-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm text-slate-500 line-clamp-2">
                                {!! strip_tags($notification->data['message'] ?? '') !!}
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="py-16 text-center">
                    <div class="w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="bell-off" class="w-10 h-10 text-slate-400"></i>
                    </div>
                    <p class="text-slate-500 font-medium">Belum ada notifikasi untuk Anda.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
        
    </div>
</div>
@endsection
