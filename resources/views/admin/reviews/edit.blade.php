@extends('layouts.admin')

@section('title', 'Edit Komentar Paket')
@section('page-title', 'Edit Komentar Paket')

@section('content')
@php
    // tampil WIB di input datetime-local
    $createdAtWib = optional($review->created_at)
        ? $review->created_at->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i')
        : '';

    $primary = '#0194F3';
@endphp

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm font-extrabold text-slate-900">Edit Komentar Paket</div>
            <div class="mt-1 text-xs text-slate-500">
                Ubah isi komentar dan tanggal review (input ditampilkan WIB).
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.reviews.index', ['status' => $review->status]) }}"
               class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                Kembali
            </a>

            <a href="{{ route('admin.reviews.index') }}"
               class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                Daftar Reviews
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <div class="text-sm font-extrabold text-emerald-800">Berhasil</div>
            <div class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Errors --}}
    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3">
            <div class="text-sm font-extrabold text-rose-800">Validasi gagal</div>
            <ul class="mt-2 list-disc pl-5 text-xs text-rose-700 space-y-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Form card --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-extrabold text-slate-900">Form Edit</div>
                        <div class="mt-0.5 text-xs text-slate-500">Pastikan komentar tidak kosong dan tanggal valid.</div>
                    </div>

                    <div class="text-xs text-slate-500">
                        ID: <span class="font-extrabold text-slate-800">{{ $review->id }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.reviews.update', $review) }}" class="p-5 space-y-5">
                    @csrf
                    @method('PATCH')

                    {{-- Komentar --}}
                    <div>
                        <label for="comment" class="block text-[11px] font-extrabold text-slate-700 mb-2">
                            Komentar <span class="text-rose-600">*</span>
                        </label>
                        <textarea id="comment"
                                  name="comment"
                                  rows="6"
                                  class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400
                                         focus:outline-none focus:ring-2 focus:ring-slate-200"
                                  placeholder="Tulis komentar review...">{{ old('comment', $review->comment) }}</textarea>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                            <span>Maks 1000 karakter.</span>
                            <span>Status:
                                <span class="font-extrabold text-slate-800">{{ ucfirst($review->status) }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label for="created_at" class="block text-[11px] font-extrabold text-slate-700 mb-2">
                            Tanggal Review (WIB) <span class="text-rose-600">*</span>
                        </label>
                        <input type="datetime-local"
                               id="created_at"
                               name="created_at"
                               value="{{ old('created_at', $createdAtWib) }}"
                               class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800
                                      focus:outline-none focus:ring-2 focus:ring-slate-200">
                        <div class="mt-2 text-xs text-slate-500">
                            Disimpan ke DB sebagai UTC (normal Laravel), inputnya WIB.
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex flex-col sm:flex-row gap-2 pt-1">
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold text-white transition"
                                style="background: {{ $primary }};"
                                onmouseover="this.style.filter='brightness(0.95)'"
                                onmouseout="this.style.filter='none'">
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('admin.reviews.index', ['status' => $review->status]) }}"
                           class="inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Side info + quick actions --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Info card --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <div class="text-sm font-extrabold text-slate-900">Info Review</div>
                    <div class="mt-0.5 text-xs text-slate-500">Ringkasan data yang sedang diedit.</div>
                </div>

                <div class="p-5 space-y-3 text-xs">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500">Status</span>
                        <span class="inline-flex items-center rounded-xl px-3 py-1 font-extrabold border
                            @if($review->status === 'approved') bg-emerald-50 text-emerald-700 border-emerald-200
                            @elseif($review->status === 'rejected') bg-rose-50 text-rose-700 border-rose-200
                            @else bg-amber-50 text-amber-700 border-amber-200
                            @endif">
                            {{ ucfirst($review->status) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500">Dibuat (UTC)</span>
                        <span class="font-extrabold text-slate-800">
                            {{ optional($review->created_at)->setTimezone('UTC')->format('Y-m-d H:i') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500">Dibuat (WIB)</span>
                        <span class="font-extrabold text-slate-800">
                            {{ optional($review->created_at)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500">Update (WIB)</span>
                        <span class="font-extrabold text-slate-800">
                            {{ optional($review->updated_at)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i') }}
                        </span>
                    </div>

                    @if(!empty($review->reviewable_type) && !empty($review->reviewable_id))
                        <div class="pt-3 border-t border-slate-200">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-slate-500">Item</span>
                                <span class="font-extrabold text-slate-800 text-right">
                                    {{ class_basename($review->reviewable_type) }} #{{ $review->reviewable_id }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-slate-200 text-slate-600 leading-relaxed">
                        <span class="font-extrabold text-slate-900">Catatan:</span>
                        Mengubah tanggal review mengubah urutan “terbaru”. Jangan dipakai buat manipulasi timeline.
                    </div>
                </div>
            </div>

            {{-- Quick actions card --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <div class="text-sm font-extrabold text-slate-900">Quick Action</div>
                    <div class="mt-0.5 text-xs text-slate-500">Aksi cepat langsung dari halaman edit.</div>
                </div>

                <div class="p-5 space-y-2">
                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">
                            Approve
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition">
                            Decline
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.reviews.delete', $review) }}"
                          onsubmit="return confirm('Yakin mau hapus review ini? Ini tidak bisa dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                            Hapus Review
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
