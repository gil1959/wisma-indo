@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">

  <div class="flex items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Popup Widget</h1>
      <p class="text-sm text-slate-600 mt-1">Atur konten & rule popup (page target, device, frekuensi, dll).</p>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
      <div class="flex items-start gap-3">
        <div class="mt-0.5">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 border border-emerald-200">✓</span>
        </div>
        <div class="text-sm">
          <div class="font-semibold">Berhasil</div>
          <div class="text-emerald-800">{{ session('success') }}</div>
        </div>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.settings.popup.save') }}" enctype="multipart/form-data"
        class="rounded-3xl border border-slate-200 bg-white shadow-sm">
    @csrf

    <div class="p-6 border-b border-slate-200">
      <div class="flex items-center justify-between">
        <div class="font-semibold text-slate-900">Status</div>
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="is_enabled" value="1" class="rounded border-slate-300"
                 {{ old('is_enabled', $widget->is_enabled) ? 'checked' : '' }}>
          <span class="text-sm text-slate-700">Aktif</span>
        </label>
      </div>
      @error('is_enabled')<div class="text-sm text-red-600 mt-2">{{ $message }}</div>@enderror
    </div>

    <div class="p-6 space-y-8">

      <section>
        <h2 class="text-lg font-bold text-slate-900">Konten</h2>
        <div class="mt-4 grid grid-cols-1 gap-4">
          <div>
            <label class="text-sm font-medium text-slate-700">Nama (internal)</label>
            <input name="name" value="{{ old('name', $widget->name) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
            @error('name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="text-sm font-medium text-slate-700">Judul</label>
            <input name="title" value="{{ old('title', $widget->title) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
            @error('title')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>

          @php $fmt = old('body_format', $widget->body_format ?? 'html'); @endphp

<div>
  <label class="text-sm font-medium text-slate-700">Format Isi</label>
  <select name="body_format" id="bw-body-format"
          class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
    <option value="html" {{ $fmt==='html' ? 'selected' : '' }}>HTML</option>
    <option value="text" {{ $fmt==='text' ? 'selected' : '' }}>Teks biasa</option>
  </select>
  @error('body_format')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
</div>

<div id="bw-body-html-wrap">
  <label class="text-sm font-medium text-slate-700">Isi (HTML)</label>
  <textarea name="body_html" rows="6"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 font-mono text-sm">{{ old('body_html', $widget->body_html) }}</textarea>
  <p class="text-xs text-slate-500 mt-1">Boleh pakai tag sederhana: &lt;p&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;br&gt;.</p>
  @error('body_html')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
</div>

<div id="bw-body-text-wrap">
  <label class="text-sm font-medium text-slate-700">Isi (Teks biasa)</label>
  <textarea name="body_text" rows="6"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('body_text', $widget->body_text) }}</textarea>
  <p class="text-xs text-slate-500 mt-1">Akan dirender aman (di-escape). Enter akan jadi baris baru.</p>
  @error('body_text')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
</div>

<script>
  (function () {
    var sel = document.getElementById('bw-body-format');
    var htmlWrap = document.getElementById('bw-body-html-wrap');
    var textWrap = document.getElementById('bw-body-text-wrap');
    if (!sel || !htmlWrap || !textWrap) return;

    function sync() {
      var v = sel.value || 'html';
      if (v === 'text') {
        htmlWrap.style.display = 'none';
        textWrap.style.display = '';
      } else {
        htmlWrap.style.display = '';
        textWrap.style.display = 'none';
      }
    }
    sel.addEventListener('change', sync);
    sync();
  })();
</script>


          <div>
            <label class="text-sm font-medium text-slate-700">Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*" class="mt-1 w-full" />
            @if($widget->image_path)
              <div class="mt-2 flex items-center gap-3">
                <img src="{{ $widget->image_path }}" class="h-16 w-16 rounded-xl object-cover border border-slate-200" alt="popup image" />
                <div class="text-xs text-slate-600">Current: <span class="font-mono">{{ $widget->image_path }}</span></div>
              </div>
            @endif
            @error('image')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
        </div>
      </section>

      <section>
        <h2 class="text-lg font-bold text-slate-900">Button</h2>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-medium text-slate-700">Primary Button Text</label>
            <input name="primary_button_text" value="{{ old('primary_button_text', $widget->primary_button_text) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
            @error('primary_button_text')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="text-sm font-medium text-slate-700">Primary Button Link</label>
            <input name="primary_button_link" value="{{ old('primary_button_link', $widget->primary_button_link) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
            @error('primary_button_link')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="text-sm font-medium text-slate-700">Secondary Button Text</label>
            <input name="secondary_button_text" value="{{ old('secondary_button_text', $widget->secondary_button_text) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
            @error('secondary_button_text')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="text-sm font-medium text-slate-700">Secondary Button Link</label>
            <input name="secondary_button_link" value="{{ old('secondary_button_link', $widget->secondary_button_link) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
            @error('secondary_button_link')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
        </div>
      </section>

      <section>
        <h2 class="text-lg font-bold text-slate-900">Targeting</h2>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-medium text-slate-700">Muncul di path (include)</label>
            <textarea name="include_paths_text" rows="6"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 font-mono text-sm">{{ old('include_paths_text', implode("\n", $widget->include_paths ?? [])) }}</textarea>
            <p class="text-xs text-slate-500 mt-1">1 path per baris. Support wildcard: <span class="font-mono">/properti/*</span>, <span class="font-mono">/artikel/*</span>.</p>
            @error('include_paths_text')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="text-sm font-medium text-slate-700">Jangan muncul di path (exclude)</label>
            <textarea name="exclude_paths_text" rows="6"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 font-mono text-sm">{{ old('exclude_paths_text', implode("\n", $widget->exclude_paths ?? [])) }}</textarea>
            <p class="text-xs text-slate-500 mt-1">Exclude menang dari include.</p>
            @error('exclude_paths_text')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="rounded-2xl border border-slate-200 p-4">
            <div class="font-semibold text-slate-900 mb-2">Device</div>
            <label class="flex items-center gap-2 text-sm text-slate-700">
              <input type="checkbox" name="show_on_mobile" value="1" class="rounded border-slate-300"
                     {{ old('show_on_mobile', $widget->show_on_mobile) ? 'checked' : '' }}>
              Mobile
            </label>
            <label class="mt-2 flex items-center gap-2 text-sm text-slate-700">
              <input type="checkbox" name="show_on_desktop" value="1" class="rounded border-slate-300"
                     {{ old('show_on_desktop', $widget->show_on_desktop) ? 'checked' : '' }}>
              Desktop
            </label>
          </div>

          <div class="rounded-2xl border border-slate-200 p-4">
            <div class="font-semibold text-slate-900 mb-2">Behavior</div>

            <label class="text-sm font-medium text-slate-700">Delay (detik)</label>
            <input name="delay_seconds" type="number" min="0" max="120"
                   value="{{ old('delay_seconds', $widget->delay_seconds) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
            @error('delay_seconds')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror

            <label class="text-sm font-medium text-slate-700 mt-3 block">Frekuensi</label>
            <select name="frequency" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              @php $freq = old('frequency', $widget->frequency); @endphp
              <option value="always" {{ $freq==='always'?'selected':'' }}>Selalu</option>
              <option value="once_per_session" {{ $freq==='once_per_session'?'selected':'' }}>Sekali per sesi (tab)</option>
              <option value="once_per_day" {{ $freq==='once_per_day'?'selected':'' }}>Sekali per hari (device)</option>
            </select>
            @error('frequency')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-medium text-slate-700">Start At (opsional)</label>
            <input name="start_at" type="datetime-local"
                   value="{{ old('start_at', optional($widget->start_at)->format('Y-m-d\TH:i')) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
            @error('start_at')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="text-sm font-medium text-slate-700">End At (opsional)</label>
            <input name="end_at" type="datetime-local"
                   value="{{ old('end_at', optional($widget->end_at)->format('Y-m-d\TH:i')) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
            @error('end_at')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
        </div>
      </section>

    </div>

    <div class="p-6 border-t border-slate-200 flex items-center justify-end gap-3">
      <button type="submit"
              class="rounded-2xl bg-slate-900 text-white px-5 py-2.5 text-sm font-semibold hover:bg-slate-800">
        Simpan
      </button>
    </div>

  </form>
</div>
@endsection
