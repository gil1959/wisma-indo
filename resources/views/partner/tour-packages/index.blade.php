@extends('partner.layouts.app')

@section('title', 'Paket Tour')
@section('page-title', 'Paket Tour')

@section('content')
<div class="space-y-5">

    <div class="flex items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Tour Packages</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola paket tour milik kamu (menunggu review admin).</p>
        </div>

        <a href="{{ route('partner.tour-packages.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
           style="background:#0194F3;"
           onmouseover="this.style.background='#0186DB'"
           onmouseout="this.style.background='#0194F3'">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add New Package
        </a>
    </div>

  

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full text-left">
                <thead class="bg-slate-50">
                <tr class="text-xs font-extrabold text-slate-600">
                    <th class="px-5 py-3 w-[140px]">Thumbnail</th>
                    <th class="px-5 py-3">Title</th>
                    <th class="px-5 py-3 w-[160px]">Destination</th>
                    <th class="px-5 py-3 w-[140px]">Status</th>
                    <th class="px-5 py-3 text-right w-[190px]">Actions</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                @forelse($packages as $p)
                    <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4">
                            <div class="h-16 w-24 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                                @if($p->thumbnail_path)
                                    <img src="{{ asset('storage/' . $p->thumbnail_path) }}"
                                         class="h-full w-full object-cover"
                                         alt="{{ $p->title }}">
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="font-extrabold text-slate-900">{{ $p->title }}</div>
                            <div class="text-xs text-slate-500">/{{ $p->slug }}</div>
                        </td>

                        <td class="px-5 py-4">
                            {{ $p->destination ?? '-' }}
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

                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('partner.tour-packages.edit', $p->id) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                                    <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Edit
                                </a>

                                <form action="{{ route('partner.tour-packages.destroy', $p->id) }}"
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
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl border grid place-items-center"
                                 style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                                <i data-lucide="map" class="w-6 h-6" style="color:#0194F3;"></i>
                            </div>
                            <div class="mt-3 font-extrabold text-slate-900">Belum ada paket</div>
                            <div class="mt-1 text-sm text-slate-600">Klik “Add New Package” untuk mulai bikin paket.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($packages, 'links'))
        <div>
            {{ $packages->links() }}
        </div>
    @endif

</div>
@endsection
