@csrf

@if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
        <div class="font-extrabold text-red-700 mb-2">Ada error validasi:</div>
        <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-4">

    <div>
        <label class="block text-sm font-extrabold text-slate-800 mb-1">Judul</label>
        <input type="text"
               name="title"
               value="{{ old('title', $article->title ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               required>
    </div>

    <div>
        <label class="block text-sm font-extrabold text-slate-800 mb-1">Ringkasan</label>
        <textarea name="excerpt"
                  class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                  rows="3">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
        <div class="mt-1 text-xs text-slate-500">Opsional, tampil di listing/preview.</div>
    </div>

    <div>
        <label class="block text-sm font-extrabold text-slate-800 mb-1">Konten</label>
<textarea name="content"
          class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
          rows="10">{{ old('content', $article->content ?? '') }}</textarea>


    </div>
{{-- SEO --}}
<div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
    <div class="sm:col-span-6">
        <label class="block text-sm font-extrabold text-slate-800 mb-1">SEO Title</label>
        <input type="text"
               name="seo_title"
               value="{{ old('seo_title', $article->seo_title ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               placeholder="Judul untuk meta title (opsional)">
    </div>

    <div class="sm:col-span-6">
        <label class="block text-sm font-extrabold text-slate-800 mb-1">SEO Keywords</label>
        <input type="text"
               name="seo_keywords"
               value="{{ old('seo_keywords', $article->seo_keywords ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               placeholder="contoh: paket wisata, bali, liburan keluarga">
        <div class="mt-1 text-xs text-slate-500">Pisahkan dengan koma kalau banyak.</div>
    </div>

    <div class="sm:col-span-12">
        <label class="block text-sm font-extrabold text-slate-800 mb-1">SEO Description</label>
        <textarea name="seo_description"
                  rows="3"
                  class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                  placeholder="Meta description (opsional)">{{ old('seo_description', $article->seo_description ?? '') }}</textarea>
    </div>
</div>

{{-- Tags --}}
<div>
    <label class="block text-sm font-extrabold text-slate-800 mb-1">Tags</label>
    <input type="text"
           name="tags"
           value="{{ old('tags', isset($article) && is_array($article->tags) ? implode(', ', $article->tags) : '') }}"
           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
           placeholder="contoh: bali, pantai, keluarga">
    <div class="mt-1 text-xs text-slate-500">Format: pisahkan dengan koma. Maks 20 tag.</div>
</div>

{{-- Ads Code (Adsense) --}}
<div>
    <label class="block text-sm font-extrabold text-slate-800 mb-1">Ads Code (Adsense)</label>
    <textarea name="ads_code"
              rows="6"
              class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-mono"
              placeholder="Tempel kode iklan adsense di sini (opsional)">{{ old('ads_code', $article->ads_code ?? '') }}</textarea>
    <div class="mt-1 text-xs text-slate-500">
        Hati-hati: ini akan dirender sebagai HTML di halaman artikel.
    </div>
</div>

    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-start">
        <div class="sm:col-span-7">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Cover</label>
            <input type="file"
                   id="cover_image_input"
                   name="cover_image"
                   accept="image/*"
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                   onchange="previewCoverImage(this)">
            <div class="mt-1 text-xs text-slate-500">Opsional. Gunakan JPG/PNG/WEBP.</div>
        </div>

        <div class="sm:col-span-5 flex flex-col gap-2 pt-1">
            {{-- Preview foto existing (saat edit) --}}
            @if(!empty($article->cover_image))
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                    <div class="text-xs font-extrabold text-slate-600 mb-2">Cover Saat Ini</div>
                    <div class="h-28 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                        <img src="{{ asset('storage/' . $article->cover_image) }}"
                             id="cover_preview_existing"
                             class="h-full w-full object-cover"
                             alt="Cover">
                    </div>
                </div>
            @endif
            {{-- Live preview foto baru --}}
            <div id="cover_new_preview_wrap" class="hidden rounded-2xl border border-blue-200 bg-blue-50 p-3">
                <div class="text-xs font-extrabold text-blue-600 mb-2">Preview Foto Baru</div>
                <div class="h-28 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                    <img id="cover_new_preview" src="" class="h-full w-full object-cover" alt="Preview">
                </div>
            </div>
        </div>


        {{-- Publish checkbox --}}
        <div class="sm:col-span-12 flex items-center gap-3 pt-2">
            <input id="is_published"
                   type="checkbox"
                   name="is_published"
                   value="1"
                   class="h-5 w-5 rounded border-slate-300"
                   {{ old('is_published', $article->is_published ?? false) ? 'checked' : '' }}>
            <label for="is_published" class="text-sm font-extrabold text-slate-800">
                Publish
            </label>
        </div>

    </div>

<div class="pt-2 flex flex-col sm:flex-row gap-3 justify-end">
        <a href="{{ route('admin.articles.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
            Kembali
        </a>

        <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                style="background:#0194F3;"
                onmouseover="this.style.background='#0186DB'"
                onmouseout="this.style.background='#0194F3'">
            <i data-lucide="save" class="w-4 h-4"></i>
            Simpan
        </button>
    </div>

</div>

<script>
function previewCoverImage(input) {
    const wrap = document.getElementById('cover_new_preview_wrap');
    const img = document.getElementById('cover_new_preview');
    const existingPreview = document.getElementById('cover_preview_existing');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            wrap.classList.remove('hidden');
            // Dim existing preview jika ada
            if (existingPreview) {
                existingPreview.style.opacity = '0.4';
            }
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        wrap.classList.add('hidden');
        if (existingPreview) existingPreview.style.opacity = '1';
    }
}
</script>
