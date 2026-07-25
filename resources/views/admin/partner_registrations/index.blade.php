@extends('layouts.admin')

@section('title', 'Pengajuan Agen Partner')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Pengajuan Agen Partner</h2>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama / Kontak</th>
                        <th class="px-6 py-4 font-semibold">Dokumen</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($registrations as $reg)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ optional($reg->user)->name ?? '-' }}</div>
                                <div class="text-slate-500 text-xs">{{ optional($reg->user)->email ?? '-' }}</div>
                                <div class="text-slate-500 text-xs">{{ optional($reg->user)->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($reg->ktp_file)
                                        <a href="{{ asset($reg->ktp_file) }}" target="_blank" class="text-[#0194F3] hover:underline flex items-center gap-1 text-xs"><i data-lucide="file-text" class="w-3 h-3"></i> KTP</a>
                                    @endif
                                    @if($reg->foto_file)
                                        <a href="{{ asset($reg->foto_file) }}" target="_blank" class="text-[#0194F3] hover:underline flex items-center gap-1 text-xs"><i data-lucide="image" class="w-3 h-3"></i> Foto</a>
                                    @endif
                                    @if($reg->npwp_file)
                                        <a href="{{ asset($reg->npwp_file) }}" target="_blank" class="text-[#0194F3] hover:underline flex items-center gap-1 text-xs"><i data-lucide="file-text" class="w-3 h-3"></i> NPWP</a>
                                    @endif
                                    @if($reg->lisensi_file)
                                        <a href="{{ asset($reg->lisensi_file) }}" target="_blank" class="text-[#0194F3] hover:underline flex items-center gap-1 text-xs"><i data-lucide="award" class="w-3 h-3"></i> Lisensi</a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($reg->user && $reg->user->is_suspended && $reg->user->suspended_until > now())
                                <span class="px-2 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-red-50 text-red-600 border border-red-200">
                                    SUSPENDED
                                </span>
                                @else
                                <span class="px-2 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                                    {{ $reg->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-200' : '' }}
                                    {{ $reg->status === 'approved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : '' }}
                                    {{ $reg->status === 'rejected' ? 'bg-red-50 text-red-600 border border-red-200' : '' }}">
                                    {{ $reg->status }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $reg->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($reg->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.partner_registrations.show', $reg->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition" title="Detail">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <form id="approve-form-{{ $reg->id }}" action="{{ route('admin.partner_registrations.approve', $reg->id) }}" method="POST" class="hidden">
                                            @csrf
                                        </form>
                                        <button type="button" onclick="approvePartner({{ $reg->id }})" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition" title="Approve">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </button>
                                        <button type="button" onclick="rejectPartner({{ $reg->id }})" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="Reject">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                    <form id="reject-form-{{ $reg->id }}" action="{{ route('admin.partner_registrations.reject', $reg->id) }}" method="POST" class="hidden">
                                        @csrf
                                        <input type="hidden" name="reason" id="reject-reason-{{ $reg->id }}">
                                    </form>
                                @else
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.partner_registrations.show', $reg->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition inline-block" title="Detail">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        @if($reg->status === 'approved')
                                            @if($reg->user && $reg->user->is_suspended && $reg->user->suspended_until > now())
                                                <button type="button" onclick="unsuspendPartner({{ $reg->id }})" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition" title="Unsuspend Partner">
                                                    <i data-lucide="play-circle" class="w-4 h-4"></i>
                                                </button>
                                                <form id="unsuspend-form-{{ $reg->id }}" action="{{ route('admin.partner_registrations.unsuspend', $reg->id) }}" method="POST" class="hidden">
                                                    @csrf
                                                </form>
                                            @else
                                                <button type="button" onclick="suspendPartner({{ $reg->id }})" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition" title="Suspend Partner">
                                                    <i data-lucide="pause-circle" class="w-4 h-4"></i>
                                                </button>
                                                <form id="suspend-form-{{ $reg->id }}" action="{{ route('admin.partner_registrations.suspend', $reg->id) }}" method="POST" class="hidden">
                                                    @csrf
                                                    <input type="hidden" name="duration" id="suspend-duration-{{ $reg->id }}">
                                                </form>
                                            @endif
                                            <button type="button" onclick="deletePartner({{ $reg->id }})" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="Hapus Partner">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                            <form id="delete-form-{{ $reg->id }}" action="{{ route('admin.partner_registrations.destroy', $reg->id) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada pengajuan partner.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($registrations->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $registrations->links() }}
            </div>
        @endif
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
