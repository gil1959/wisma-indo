@extends('layouts.admin')

@section('title', 'Detail Pengajuan Agen Partner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.partner_registrations.index') }}" class="p-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Detail Pengajuan Agen Partner</h2>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Informasi Pemohon</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-slate-500 mb-1">Nama Lengkap</div>
                        <div class="font-semibold text-slate-800">{{ $registration->user->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 mb-1">Email</div>
                        <div class="font-semibold text-slate-800">{{ $registration->user->email }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 mb-1">Nomor Telepon / WA</div>
                        <div class="font-semibold text-slate-800">{{ $registration->user->phone ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 mb-1">Tanggal Pengajuan</div>
                        <div class="font-semibold text-slate-800">{{ $registration->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Dokumen Terlampir</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($registration->ktp_file)
                        <div class="border border-slate-200 rounded-xl p-3 flex items-center justify-between hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                    <i data-lucide="file-text" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-slate-800">KTP</div>
                                    <div class="text-xs text-slate-500">Wajib</div>
                                </div>
                            </div>
                            <a href="{{ asset($registration->ktp_file) }}" target="_blank" class="px-3 py-1 bg-slate-100 text-slate-600 rounded text-xs font-semibold hover:bg-slate-200 transition">Lihat</a>
                        </div>
                    @endif
                    
                    @if($registration->foto_file)
                        <div class="border border-slate-200 rounded-xl p-3 flex items-center justify-between hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                    <i data-lucide="image" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-slate-800">Foto Diri</div>
                                    <div class="text-xs text-slate-500">Wajib</div>
                                </div>
                            </div>
                            <a href="{{ asset($registration->foto_file) }}" target="_blank" class="px-3 py-1 bg-slate-100 text-slate-600 rounded text-xs font-semibold hover:bg-slate-200 transition">Lihat</a>
                        </div>
                    @endif

                    @if($registration->npwp_file)
                        <div class="border border-slate-200 rounded-xl p-3 flex items-center justify-between hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-slate-100 text-slate-600 rounded-lg">
                                    <i data-lucide="file-text" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-slate-800">NPWP</div>
                                    <div class="text-xs text-slate-500">Opsional</div>
                                </div>
                            </div>
                            <a href="{{ asset($registration->npwp_file) }}" target="_blank" class="px-3 py-1 bg-slate-100 text-slate-600 rounded text-xs font-semibold hover:bg-slate-200 transition">Lihat</a>
                        </div>
                    @endif

                    @if($registration->lisensi_file)
                        <div class="border border-slate-200 rounded-xl p-3 flex items-center justify-between hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-slate-100 text-slate-600 rounded-lg">
                                    <i data-lucide="award" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-slate-800">Lisensi Agen</div>
                                    <div class="text-xs text-slate-500">Opsional</div>
                                </div>
                            </div>
                            <a href="{{ asset($registration->lisensi_file) }}" target="_blank" class="px-3 py-1 bg-slate-100 text-slate-600 rounded text-xs font-semibold hover:bg-slate-200 transition">Lihat</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Status Pengajuan</h3>
                
                <div class="mb-6 flex flex-col items-center justify-center p-4 rounded-xl border
                    {{ ($registration->user && $registration->user->is_suspended && $registration->user->suspended_until > now()) ? 'bg-red-50 border-red-200' : '' }}
                    {{ $registration->status === 'pending' ? 'bg-amber-50 border-amber-200' : '' }}
                    {{ $registration->status === 'approved' && !($registration->user && $registration->user->is_suspended && $registration->user->suspended_until > now()) ? 'bg-emerald-50 border-emerald-200' : '' }}
                    {{ $registration->status === 'rejected' ? 'bg-red-50 border-red-200' : '' }}">
                    
                    <div class="text-sm text-slate-500 mb-1">Status Saat Ini:</div>
                    <div class="text-xl font-black uppercase tracking-wider
                        {{ ($registration->user && $registration->user->is_suspended && $registration->user->suspended_until > now()) ? 'text-red-600' : '' }}
                        {{ $registration->status === 'pending' ? 'text-amber-600' : '' }}
                        {{ $registration->status === 'approved' && !($registration->user && $registration->user->is_suspended && $registration->user->suspended_until > now()) ? 'text-emerald-600' : '' }}
                        {{ $registration->status === 'rejected' ? 'text-red-600' : '' }}">
                        {{ ($registration->user && $registration->user->is_suspended && $registration->user->suspended_until > now()) ? 'SUSPENDED' : $registration->status }}
                    </div>
                </div>

                @if($registration->status === 'rejected' && $registration->rejection_note)
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl text-sm text-red-800">
                        <div class="font-bold mb-1">Alasan Penolakan:</div>
                        {{ $registration->rejection_note }}
                    </div>
                @endif

                @if($registration->status === 'pending')
                    <div class="space-y-3">
                        <form id="approve-form-{{ $registration->id }}" action="{{ route('admin.partner_registrations.approve', $registration->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                        <button type="button" onclick="approvePartner({{ $registration->id }})" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition flex items-center justify-center gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i> Setujui Pengajuan
                        </button>
                        
                        <button type="button" onclick="rejectPartner({{ $registration->id }})" class="w-full py-3 bg-white border-2 border-red-500 text-red-500 hover:bg-red-50 font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <i data-lucide="x-circle" class="w-5 h-5"></i> Tolak Pengajuan
                        </button>

                        <form id="reject-form-{{ $registration->id }}" action="{{ route('admin.partner_registrations.reject', $registration->id) }}" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="reason" id="reject-reason-{{ $registration->id }}">
                        </form>
                    </div>
                    </div>
                @elseif($registration->status === 'approved')
                    <div class="space-y-3 mt-4">
                        @if($registration->user && $registration->user->is_suspended && $registration->user->suspended_until > now())
                            <form id="unsuspend-form-{{ $registration->id }}" action="{{ route('admin.partner_registrations.unsuspend', $registration->id) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            <button type="button" onclick="unsuspendPartner({{ $registration->id }})" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition flex items-center justify-center gap-2">
                                <i data-lucide="play-circle" class="w-5 h-5"></i> Cabut Suspend
                            </button>
                        @else
                            <form id="suspend-form-{{ $registration->id }}" action="{{ route('admin.partner_registrations.suspend', $registration->id) }}" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="duration" id="suspend-duration-{{ $registration->id }}">
                            </form>
                            <button type="button" onclick="suspendPartner({{ $registration->id }})" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 transition flex items-center justify-center gap-2">
                                <i data-lucide="pause-circle" class="w-5 h-5"></i> Suspend Partner
                            </button>
                        @endif

                        <form id="delete-form-{{ $registration->id }}" action="{{ route('admin.partner_registrations.destroy', $registration->id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                        <button type="button" onclick="deletePartner({{ $registration->id }})" class="w-full py-3 bg-white border-2 border-red-500 text-red-500 hover:bg-red-50 font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <i data-lucide="trash-2" class="w-5 h-5"></i> Hapus Akses Partner
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function rejectPartner(id) {
        Swal.fire({
            title: 'Tolak Pengajuan?',
            text: "Berikan alasan penolakan. Alasan ini akan dikirimkan ke email pendaftar.",
            input: 'textarea',
            inputPlaceholder: 'Alasan penolakan (misal: Foto KTP tidak jelas)...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) {
                    return 'Anda harus mengisi alasan penolakan!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('reject-reason-' + id).value = result.value;
                document.getElementById('reject-form-' + id).submit();
            }
        })
    }

    function approvePartner(id) {
        Swal.fire({
            title: 'Setujui Pengajuan?',
            text: "Akun ini akan mendapatkan akses sebagai Agen Partner.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('approve-form-' + id).submit();
            }
        })
    }

    function suspendPartner(id) {
        Swal.fire({
            title: 'Suspend Partner?',
            text: "Masukkan durasi suspend untuk partner ini dalam hitungan hari (misal: 1, 3, 7, 30). Masukkan 9999 untuk suspend permanen.",
            input: 'number',
            inputAttributes: {
                min: 1
            },
            inputPlaceholder: 'Durasi (Hari)',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Suspend',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value || value < 1) {
                    return 'Anda harus memasukkan durasi suspend yang valid (minimal 1)!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('suspend-duration-' + id).value = result.value;
                document.getElementById('suspend-form-' + id).submit();
            }
        })
    }

    function deletePartner(id) {
        Swal.fire({
            title: 'Hapus Akses Partner?',
            text: "Status partner akan dicabut dan akun dikembalikan menjadi user biasa. Aksi ini tidak dapat dikembalikan!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    function unsuspendPartner(id) {
        Swal.fire({
            title: 'Cabut Suspend (Unsuspend)?',
            text: "Status suspend partner ini akan dicabut dan dapat kembali menggunakan akses layaknya partner normal.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Cabut Suspend!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('unsuspend-form-' + id).submit();
            }
        })
    }
</script>
@endsection
