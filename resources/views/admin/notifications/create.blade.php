@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Kirim Notifikasi</h1>
            <p class="text-slate-500 text-sm">Kirim pesan pemberitahuan kepada user.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('admin.notifications.store') }}" method="POST" enctype="multipart/form-data" x-data="{ target: 'all', imagePreview: null, fileChosen(e) { if(e.target.files.length) this.imagePreview = URL.createObjectURL(e.target.files[0]) } }">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Penerima</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="target" value="all" x-model="target" class="w-4 h-4 text-[#0194F3] border-slate-300 focus:ring-[#0194F3]">
                        <span class="text-sm font-medium text-slate-700">Semua User</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="target" value="specific" x-model="target" class="w-4 h-4 text-[#0194F3] border-slate-300 focus:ring-[#0194F3]">
                        <span class="text-sm font-medium text-slate-700">Pilih User Tertentu</span>
                    </label>
                </div>
            </div>

            <div x-show="target === 'specific'" class="mb-6" x-collapse>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih User</label>
                <select name="user_ids[]" id="user-select" class="w-full" multiple>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_ids') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Notifikasi</label>
                <input type="text" name="title" value="{{ old('title') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition" required placeholder="Contoh: Promo Spesial Weekend!">
                @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6" wire:ignore>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pesan Notifikasi</label>
                <textarea name="message" id="editor" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-[#0194F3] focus:ring-1 focus:ring-[#0194F3] outline-none transition h-40" placeholder="Ketik pesan Anda di sini...">{{ old('message') }}</textarea>
                @error('message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-8">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Gambar / Foto (Opsional)</label>
                
                <div class="flex items-start gap-4">
                    <label class="flex flex-col items-center justify-center w-32 h-32 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition relative overflow-hidden">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i data-lucide="image-plus" class="w-8 h-8 text-slate-400 mb-2"></i>
                            <p class="text-xs text-slate-500 text-center px-2">Klik untuk<br>Pilih Foto</p>
                        </div>
                        <template x-if="imagePreview">
                            <div class="absolute inset-0 bg-white">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                            </div>
                        </template>
                        <input type="file" name="image" accept="image/*" class="hidden" @change="fileChosen">
                    </label>
                    <div class="text-xs text-slate-500 mt-2">
                        Format didukung: JPG, PNG, JPEG.<br>
                        Ukuran maksimal: 2MB.<br>
                        Rekomendasi rasio: 16:9 atau 1:1.
                    </div>
                </div>
                @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 rounded-lg font-semibold text-white bg-[#0194F3] hover:bg-blue-600 transition flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i> Kirim Notifikasi
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--multiple {
        min-height: 44px;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        padding: 4px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #0194F3;
        box-shadow: 0 0 0 1px #0194F3;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 2px 8px;
        color: #475569;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    $(document).ready(function() {
        $('#user-select').select2({
            placeholder: "Cari nama atau email user..."
        });

        tinymce.init({
            selector: '#editor',
            plugins: 'lists link textcolor',
            toolbar: 'undo redo | bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist | link',
            menubar: false,
            branding: false,
            height: 250,
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            }
        });
    });
</script>
@endpush
@endsection
