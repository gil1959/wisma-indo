@extends('layouts.admin')

@section('title', 'Edit Paket Umrah')
@section('page-title', 'Edit Paket Umrah')

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

    <form action="{{ route('admin.umrah-packages.update', $package->id) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-4">
        @csrf
        @method('PUT')

        @include('admin.umrah-packages._form', ['package' => $package, 'categories' => $categories])

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.umrah-packages.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                Kembali
            </a>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-extrabold text-white transition"
                    style="background:#0194F3;"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'">
                <i data-lucide="save" class="w-4 h-4"></i>
                Update Paket
            </button>
        </div>
    </form>

</div>

{{-- Hidden form khusus delete photo (biar gak nested form) --}}
<form id="deletePhotoForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    window.__bwDeletePhoto = function(actionUrl) {
        if (!confirm('Hapus foto ini?')) return;
        const f = document.getElementById('deletePhotoForm');
        f.action = actionUrl;
        f.submit();
    }
</script>

@include('admin.partials.wysiwyg')
@endsection

