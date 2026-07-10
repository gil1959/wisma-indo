@extends('layouts.admin')


@section('title', 'Client Logos')

@section('content')
<div class="admin-container py-6">
  <div class="flex items-center justify-between mb-5">
    <div>
      <h1 class="text-2xl font-extrabold">Client Logos</h1>
      <p class="text-slate-600 text-sm">Kelola logo “Kepercayaan Pelanggan” di halaman Home.</p>
    </div>

    <a href="{{ route('admin.client-logos.create') }}" class="btn btn-primary">
      <i data-lucide="plus" class="w-4 h-4"></i> Tambah Logo
    </a>
  </div>

  @if(session('success'))
    <div class="alert-success mb-4">{{ session('success') }}</div>
  @endif

  <div class="card overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr class="text-left text-slate-600">
            <th class="p-4">Logo</th>
            <th class="p-4">Nama</th>
            <th class="p-4">URL</th>
            <th class="p-4">Urutan</th>
            <th class="p-4">Status</th>
            <th class="p-4 text-right">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse($logos as $logo)
            <tr>
              <td class="p-4">
                <img src="{{ asset('storage/'.$logo->image_path) }}" alt="{{ $logo->name }}" class="h-10 object-contain">
              </td>
              <td class="p-4 font-semibold">{{ $logo->name }}</td>
              <td class="p-4">
                @if($logo->url)
                  <a class="text-azure underline" href="{{ $logo->url }}" target="_blank" rel="noopener">
                    {{ \Illuminate\Support\Str::limit($logo->url, 40) }}
                  </a>
                @else
                  <span class="text-slate-400">-</span>
                @endif
              </td>
              <td class="p-4">{{ $logo->sort_order }}</td>
              <td class="p-4">
                @if($logo->is_active)
                  <span class="badge-success">Aktif</span>
                @else
                  <span class="badge-muted">Nonaktif</span>
                @endif
              </td>
              <td class="p-4">
                <div class="flex justify-end gap-2">
                  <a href="{{ route('admin.client-logos.edit', $logo->id) }}" class="btn btn-ghost">
                    <i data-lucide="pencil" class="w-4 h-4"></i> Edit
                  </a>

                  <form action="{{ route('admin.client-logos.destroy', $logo->id) }}" method="POST"
                        onsubmit="return confirm('Hapus logo ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">
                      <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td class="p-6 text-center text-slate-500" colspan="6">Belum ada logo</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
