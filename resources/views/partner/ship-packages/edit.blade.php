@extends('partner.layouts.app')

@section('page-title','Edit Paket Sewa Kapal')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
  <form action="{{ route('partner.ship-packages.update', $package->id) }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    @include('partner.ship-packages._form', ['package' => $package, 'buttonText' => 'Update'])
  </form>
</div>
@endsection

@include('admin.partials.wysiwyg')
