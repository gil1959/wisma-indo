@extends('layouts.admin')

@section('title', 'Tambah Paket Rental')
@section('page-title', 'Tambah Paket Rental')

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

    <form action="{{ route('admin.rent-car-packages.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        @include('admin.rentcar._form', ['buttonText' => 'Create Package'])
    </form>

</div>
@include('admin.partials.wysiwyg')

@endsection
