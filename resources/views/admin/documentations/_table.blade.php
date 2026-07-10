    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-extrabold text-slate-600">
                        <th class="px-5 py-3 w-[180px]">Preview</th>
                        <th class="px-5 py-3">Judul</th>
                        <th class="px-5 py-3 w-[140px]">Tipe</th>
                        <th class="px-5 py-3 w-[140px]">Status</th>
                        <th class="px-5 py-3 w-[110px]">Urutan</th>
                        <th class="px-5 py-3 text-right w-[200px]">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $d)
                        <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                @if($d->type === 'photo')
                                    <img src="{{ $d->url }}"
                                         alt="{{ $d->title ?? 'Dokumentasi' }}"
                                         class="h-[72px] w-[132px] rounded-xl border border-slate-200 object-cover">
                                @else
                                    <div class="h-[72px] w-[132px] rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center">
                                        <i data-lucide="video" class="w-6 h-6" style="color:#0194F3;"></i>
                                    </div>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">
                                    {{ $d->title ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-1 break-all">
                                    {{ $d->file_path }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold"
                                      style="background: rgba(1,148,243,0.10); border:1px solid rgba(1,148,243,0.20); color:#055a93;">
                                    <i data-lucide="{{ $d->type === 'photo' ? 'image' : 'video' }}" class="w-3.5 h-3.5"></i>
                                    {{ strtoupper($d->type) }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                @if($d->is_active)
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold"
                                          style="background: rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.20); color:#166534;">
                                        <span class="h-2 w-2 rounded-full" style="background:#22c55e;"></span>
                                        AKTIF
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold"
                                          style="background: rgba(239,68,68,0.10); border:1px solid rgba(239,68,68,0.20); color:#991b1b;">
                                        <span class="h-2 w-2 rounded-full" style="background:#ef4444;"></span>
                                        NONAKTIF
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-700">
                                    {{ $d->sort_order ?? 0 }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.documentations.edit', $d->id) }}"
                                       class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                       style="background:#0194F3"
                                       onmouseover="this.style.filter='brightness(0.95)'"
                                       onmouseout="this.style.filter='none'">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.documentations.destroy', $d->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus dokumentasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                                style="background:#ef4444"
                                                onmouseover="this.style.background='#dc2626'"
                                                onmouseout="this.style.background='#ef4444'">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                                Belum ada data dokumentasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
