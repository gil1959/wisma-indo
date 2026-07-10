@extends('layouts.admin')

@section('title', 'Rental')
@section('page-title', 'Rental')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Rent Car Packages</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola paket rental mobil.</p>
        </div>

        <a href="{{ route('admin.rent-car-packages.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
            style="background:#0194F3;"
            onmouseover="this.style.background='#0186DB'"
            onmouseout="this.style.background='#0194F3'">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add New Package
        </a>
    </div>
    {{-- Filter --}}
    <form method="GET" action="{{ url()->current() }}"
        class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
        <div class="grid gap-3 md:grid-cols-12 items-end">

            <div class="md:col-span-5">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">Pencarian</label>
                <input type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari judul / slug..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">Kategori</label>
                <select name="category" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                    <option value="">Semua</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category')==$cat->id)>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                    <option value="">Semua</option>
                    <option value="active" @selected(request('status')==='active' )>Aktif</option>
                    <option value="inactive" @selected(request('status')==='inactive' )>Nonaktif</option>
                </select>
            </div>

            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="w-full rounded-xl px-4 py-2.5 text-sm font-extrabold text-white"
                    style="background:#0194F3;"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'">
                    Terapkan
                </button>

                <a href="{{ url()->current() }}"
                    class="w-full rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-center">
                    Reset
                </a>
            </div>

        </div>
    </form>

    {{-- Flash --}}
    @if(session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
        <div class="font-extrabold">Berhasil</div>
        <div class="text-sm mt-1">{{ session('success') }}</div>
    </div>
    @endif
    @if(session('error'))
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
        <div class="font-extrabold">Gagal</div>
        <div class="text-sm mt-1">{{ session('error') }}</div>
    </div>
    @endif

    {{-- Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-extrabold text-slate-600">
                        <th class="px-5 py-3 w-[140px]">Thumbnail</th>
                        <th class="px-5 py-3">Title</th>
                        <th class="px-4 py-2 text-left font-semibold">Price / Hour</th>
                        <th class="px-5 py-3 w-[140px]">Status</th>
                        <th class="px-5 py-3">Features</th>
                        <th class="px-5 py-3 text-right w-[190px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($packages as $p)
                    <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4">
                            <div class="h-16 w-24 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                                <img src="{{ asset('storage/' . $p->thumbnail_path) }}"
                                    class="h-full w-full object-cover"
                                    alt="{{ $p->title }}">
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="font-extrabold text-slate-900">{{ $p->title }}</div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="font-extrabold" style="color:#0194F3;">
                                Rp {{ number_format($p->price_per_day, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-slate-500">per hour</div>
                        </td>

                        <td class="px-5 py-4">
                            @if($p->is_active)
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                style="background: rgba(16,185,129,0.10); border-color: rgba(16,185,129,0.25); color:#065f46;">
                                <span class="h-2 w-2 rounded-full" style="background:#10b981;"></span>
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                style="background: rgba(148,163,184,0.18); border-color: rgba(148,163,184,0.35); color:#475569;">
                                <span class="h-2 w-2 rounded-full" style="background:#94a3b8;"></span>
                                Inactive
                            </span>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            <div class="space-y-1">
                                @foreach(($p->features ?? []) as $f)
                                <div class="flex items-center gap-2">
                                    @if(!empty($f['available']))
                                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                                    @else
                                    <i data-lucide="x-circle" class="w-4 h-4 text-red-400"></i>
                                    @endif
                                    <span class="text-slate-700">{{ $f['name'] ?? '-' }}</span>
                                </div>
                                @endforeach
                                @if(empty($p->features) || count($p->features) === 0)
                                <span class="text-xs text-slate-500">—</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.rent-car-packages.edit', $p->id) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                                    <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Edit
                                </a>

                                <form action="{{ route('admin.rent-car-packages.destroy', $p->id) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Delete this package?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                        style="background:#ef4444"
                                        onmouseover="this.style.background='#dc2626'"
                                        onmouseout="this.style.background='#ef4444'">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl border grid place-items-center"
                                style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                                <i data-lucide="car" class="w-6 h-6" style="color:#0194F3;"></i>
                            </div>
                            <div class="mt-3 font-extrabold text-slate-900">Belum ada paket rental</div>
                            <div class="mt-1 text-sm text-slate-600">Klik “Add New Package” untuk mulai bikin paket.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination kalau paginate --}}
    @if(method_exists($packages, 'links'))
    <div>
        {{ $packages->links() }}
    </div>
    @endif

</div>
@endsection