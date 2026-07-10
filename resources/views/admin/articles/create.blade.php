@extends('layouts.admin')

@section('title', 'Tambah Artikel')
@section('page-title', 'Tambah Artikel')

@section('content')
<div class="max-w-4xl space-y-5">

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl grid place-items-center border"
                 style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                <i data-lucide="file-plus" class="w-5 h-5" style="color:#0194F3;"></i>
            </div>
            <div>
                <div class="font-extrabold text-slate-900">Tambah Artikel</div>
                <div class="text-xs text-slate-500">Buat artikel baru untuk ditampilkan di website.</div>
            </div>
        </div>

        <form action="{{ route('admin.articles.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="mt-5 space-y-4">
            @include('admin.articles._form')
        </form>
    </div>

</div>
@include('admin.partials.wysiwyg')
@endsection

