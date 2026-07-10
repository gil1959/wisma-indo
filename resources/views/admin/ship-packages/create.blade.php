@extends('layouts.admin')

@section('page-title','Tambah Paket Sewa Kapal')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
  <form action="{{ route('admin.ship-packages.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.ship-packages._form', ['package' => null, 'buttonText' => 'Create'])
  </form>
</div>
@endsection
@include('admin.partials.wysiwyg')