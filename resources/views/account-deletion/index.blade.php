@extends('layouts.front')
@section('title', 'Hapus Akun WismaIndo')
@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Hapus Akun WismaIndo</h1>
        <p class="text-slate-500 mb-8">Ajukan penghapusan tanpa harus memasang aplikasi.</p>
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
            @include('account-deletion.policy')
            <h2 class="mt-8 text-lg font-bold text-slate-900">Cara menghapus akun</h2>
            <ol class="list-decimal pl-5 mt-3 space-y-2 text-sm text-slate-600">
                <li>Masukkan email yang terdaftar pada akun WismaIndo, termasuk email akun Google jika digunakan.</li>
                <li>Buka email konfirmasi. Tautan berlaku selama 30 menit.</li>
                <li>Tinjau data, ketik HAPUS, lalu setujui penghapusan melalui popup konfirmasi.</li>
            </ol>
            @if(session('deletion_status'))<div role="status" class="mt-6 p-4 rounded-xl bg-blue-50 text-slate-800">{{ session('deletion_status') }}</div>@endif
            @if($errors->any())<p role="status" class="mt-4 text-sm text-red-700">{{ $errors->first() }}</p>@endif
            <form method="POST" action="{{ route('account-deletion.request') }}" class="mt-6 space-y-4">
                @csrf
                <div><label for="deletion-email" class="block text-sm font-bold text-slate-700 mb-2">Email akun</label>
                <input id="deletion-email" name="email" type="email" autocomplete="email" required maxlength="255" value="{{ old('email') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 bg-white focus:ring-[#0194F3]"></div>
                <label class="flex gap-3 items-start text-sm text-slate-600"><input name="consent" type="checkbox" value="1" required class="mt-1 rounded">Saya ingin menerima konfirmasi untuk menghapus akun dan data terkait.</label>
                <button type="submit" class="bg-[#0194F3] hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-xl">Kirim konfirmasi email</button>
            </form>
        </div>
    </div>
</div>
@endsection
