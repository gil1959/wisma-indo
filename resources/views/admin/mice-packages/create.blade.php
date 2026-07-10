@extends('layouts.admin')

@section('title', 'Tambah Paket MICE')
@section('page-title', 'Tambah Paket MICE')

@section('content')
<div class="space-y-5">

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="font-extrabold">Ada error</div>
            <ul class="mt-2 list-disc pl-5 text-sm space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.mice-packages.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-4">
        @csrf

        @include('admin.mice-packages._form', ['package' => null, 'categories' => $categories])

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.mice-packages.index') }}"
               class="rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50">
                Kembali
            </a>
            <button type="submit"
                    class="rounded-xl px-4 py-2.5 text-sm font-extrabold text-white"
                    style="background:#0194F3;">
                Simpan
            </button>
        </div>
    </form>

</div>
@include('admin.partials.wysiwyg')
@endsection
