@extends('layouts.admin')

@section('title', 'Edit Paket MICE')
@section('page-title', 'Edit Paket MICE')

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

    <form action="{{ route('admin.mice-packages.update', $package->id) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-4">
        @csrf
        @method('PUT')

        @include('admin.mice-packages._form', ['package' => $package, 'categories' => $categories])

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.mice-packages.index') }}"
               class="rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50">
                Kembali
            </a>

            <button type="submit"
                    class="rounded-xl px-4 py-2.5 text-sm font-extrabold text-white"
                    style="background:#0194F3;">
                Update
            </button>
        </div>
    </form>

    {{-- Hidden form khusus delete photo (BIAR GAK NESTED FORM) --}}
    <form id="deletePhotoForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

</div>

@include('admin.partials.wysiwyg')

<script>
    window.__bwDeletePhoto = function(actionUrl) {
        if (!confirm('Hapus foto ini?')) return;
        const f = document.getElementById('deletePhotoForm');
        f.action = actionUrl;
        f.submit();
    }
</script>
@endsection
