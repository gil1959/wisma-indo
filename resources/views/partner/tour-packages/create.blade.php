@extends('partner.layouts.app')

@section('title', 'Tambah Paket Tour')
@section('page-title', 'Tambah Paket Tour')

@section('content')
<div class="space-y-5">

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="font-extrabold">Validation Error</div>
            <ul class="mt-2 list-disc pl-5 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('partner.tour-packages.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-4">
        @csrf
        @include('partner.tour-packages._form', ['categories' => $categories])
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('partner.tour-packages.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                Back
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-extrabold text-white transition"
                    style="background:#0194F3;"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'">
                <i data-lucide="save" class="w-4 h-4"></i>
                Create
            </button>
        </div>
    </form>

</div>

@include('admin.partials.wysiwyg')
@endsection
