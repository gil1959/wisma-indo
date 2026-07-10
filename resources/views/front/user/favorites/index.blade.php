@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-slate-800 mb-8">Iklan Favorit</h1>
        
        <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-rose-50 text-rose-300 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="heart" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Favorit</h3>
            <p class="text-slate-500 mb-6">Anda belum menandai iklan apapun sebagai favorit.</p>
        </div>
    </div>
</div>
@endsection
