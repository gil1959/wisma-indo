@extends('layouts.admin')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Detail User</h2>
            <p class="mt-1 text-sm text-slate-600">Informasi lengkap user</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user) }}"
               class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
               style="background:#0194F3;">
                Edit
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2.5 rounded-2xl font-extrabold border border-slate-200 text-slate-700 hover:bg-slate-50">
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-xs font-extrabold text-slate-500 uppercase">Nama</div>
                <div class="mt-1 text-slate-900 font-semibold whitespace-pre-line">{{ $user->name }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-500 uppercase">Email</div>
                <div class="mt-1 text-slate-900 font-semibold whitespace-pre-line">{{ $user->email }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-500 uppercase">No HP</div>
                <div class="mt-1 text-slate-900 font-semibold whitespace-pre-line">{{ $user->phone ?? '-' }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-500 uppercase">Email Verified</div>
                <div class="mt-1 text-slate-900 font-semibold ">
                    {{ $user->email_verified_at ? $user->email_verified_at->format('d M Y H:i') : 'Belum' }}
                </div>
            </div>
            <div>
                <div class="text-xs font-extrabold text-slate-500 uppercase">Role</div>
                <div class=" text-slate-900 font-semibold mt-1">
                    {{ strtoupper($user->roles->pluck('name')->first() ?? '-') }}
                </div>
            </div>

            <div class="md:col-span-2 mt-4 pt-4 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <div class="text-xs font-extrabold text-slate-500 uppercase">Sisa Kuota Gratis Saat Ini</div>
                    <div class="text-xl font-black text-slate-900">
                        {{ $user->quota && $user->quota->listing_quota !== -1 ? $user->quota->listing_quota . ' Iklan' : ($user->quota && $user->quota->listing_quota === -1 ? 'Unlimited' : '0 Iklan') }}
                    </div>
                    <div class="mt-1 text-slate-900 font-semibold flex items-center gap-2">
                        @if($user->quota && $user->quota->has_free_quota)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Status: Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Status: Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-2">
                    <form action="{{ route('admin.users.add_quota', $user) }}" method="POST" id="form-add-quota">
                        @csrf
                        <input type="hidden" name="amount" id="add-quota-amount" value="0">
                        <button type="button" onclick="promptAddQuota()" class="px-4 py-2 text-sm font-bold rounded-xl transition shadow-sm bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200">
                            Tambahkan Kuota
                        </button>
                    </form>

                    <form action="{{ route('admin.users.subtract_quota', $user) }}" method="POST" id="form-subtract-quota">
                        @csrf
                        <input type="hidden" name="amount" id="subtract-quota-amount" value="0">
                        <button type="button" onclick="promptSubtractQuota({{ $user->quota ? $user->quota->listing_quota : 0 }})" class="px-4 py-2 text-sm font-bold rounded-xl transition shadow-sm bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200">
                            Kurangi Kuota
                        </button>
                    </form>

                    <form action="{{ route('admin.users.toggle_quota', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-bold rounded-xl transition shadow-sm {{ $user->quota && $user->quota->has_free_quota ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-[#0194F3] text-white hover:bg-blue-600' }}">
                            {{ $user->quota && $user->quota->has_free_quota ? 'Nonaktifkan Fitur' : 'Aktifkan Fitur' }}
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <div>
            <div class="text-xs font-extrabold text-slate-500 uppercase">Alamat</div>
            <div class="mt-1 text-slate-900 font-semibold ">
                {{ $user->address ?? $user->full_address ?? '-' }}
            </div>
        </div>

        

        <div class="pt-3 border-t border-slate-200 text-xs text-slate-500">
            Dibuat: {{ optional($user->created_at)->format('d M Y H:i') }}  Update: {{ optional($user->updated_at)->format('d M Y H:i') }}
        </div>
    </div>

</div>
@push('scripts')
<script>
    function promptAddQuota() {
        Swal.fire({
            title: 'Tambahkan Kuota',
            text: 'Masukkan jumlah kuota iklan gratis yang ingin ditambahkan:',
            input: 'number',
            inputAttributes: {
                min: 1,
                step: 1
            },
            showCancelButton: true,
            confirmButtonText: 'Tambahkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981', // emerald-500
            inputValidator: (value) => {
                if (!value || value < 1) {
                    return 'Jumlah kuota harus lebih dari 0!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('add-quota-amount').value = result.value;
                document.getElementById('form-add-quota').submit();
            }
        });
    }

    function promptSubtractQuota(maxQuota) {
        if (maxQuota <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak dapat dikurangi',
                text: 'User ini tidak memiliki kuota yang tersisa.',
                confirmButtonColor: '#0194F3'
            });
            return;
        }

        Swal.fire({
            title: 'Kurangi Kuota',
            text: 'Masukkan jumlah kuota iklan yang ingin dikurangi (Maks: ' + maxQuota + '):',
            input: 'number',
            inputAttributes: {
                min: 1,
                max: maxQuota,
                step: 1
            },
            showCancelButton: true,
            confirmButtonText: 'Kurangi',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#f43f5e', // rose-500
            inputValidator: (value) => {
                if (!value || value < 1) {
                    return 'Jumlah harus lebih dari 0!'
                }
                if (value > maxQuota) {
                    return 'Tidak bisa mengurangi lebih dari sisa kuota (' + maxQuota + ')!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('subtract-quota-amount').value = result.value;
                document.getElementById('form-subtract-quota').submit();
            }
        });
    }
</script>
@endpush
@endsection
