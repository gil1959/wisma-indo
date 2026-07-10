@extends('layouts.admin')

@section('title', 'Edit Paket Hotel/Vila')
@section('page-title', 'Edit Paket Hotel/Vila')

@section('content')
<div class="space-y-5">
    @if($package->created_by_partner_id)
        @include('admin.partners.products._review_panel', ['package' => $package, 'type' => 'hotel'])
    @endif


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

    <form action="{{ route('admin.hotel-packages.update', $package->id) }}"
          method="POST"
          enctype="multipart/form-data"
          class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        @method('PUT')
        @include('admin.hotel._form', ['buttonText' => 'Update Package', 'package' => $package])
    </form>

</div>
@include('admin.partials.wysiwyg')

@endsection
