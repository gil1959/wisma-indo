@extends('layouts.front')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-3xl">
        
        <div class="mb-6">
            <a href="{{ route('user.notifications.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-[#0194F3] font-medium transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Notifikasi
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            @if(!empty($notification->data['image']))
                <img src="{{ asset($notification->data['image']) }}" alt="Notification Image" class="w-full h-auto max-h-96 object-cover">
            @endif
            
            <div class="p-8">
                <div class="text-sm text-slate-400 mb-2">{{ $notification->created_at->format('d M Y, H:i') }}</div>
                <h1 class="text-2xl font-bold text-slate-900 mb-6">{{ $notification->data['title'] ?? 'Pesan Baru' }}</h1>
                
                <div class="prose prose-slate max-w-none">
                    {!! $notification->data['message'] ?? '' !!}
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
