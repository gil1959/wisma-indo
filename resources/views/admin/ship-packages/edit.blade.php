@extends('layouts.admin')

@section('page-title','Edit Paket Sewa Kapal')

@section('content')
<div class="space-y-5">

  {{-- REVIEW PANEL (paling atas sebelum form) --}}
  @include('admin.partners.products._review_panel', [
      'package' => $package,
      'type' => 'ship'
  ])

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <form action="{{ route('admin.ship-packages.update', $package->id) }}" method="POST" enctype="multipart/form-data">
      @method('PUT')
      @include('admin.ship-packages._form', ['package' => $package, 'buttonText' => 'Update'])
    </form>
  </div>

</div>
@endsection

@include('admin.partials.wysiwyg')
