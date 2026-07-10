<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type'); // photo|video|null
        $base = Documentation::query();

        if ($type && in_array($type, ['photo', 'video'])) {
            $base->where('type', $type);
        }

        $tourItems = (clone $base)->where('category', 'tour')
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'tour_page')
            ->withQueryString();

        $shipItems = (clone $base)->where('category', 'ship')
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'ship_page')
            ->withQueryString();

        $umrahItems = (clone $base)->where('category', 'umrah')
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'umrah_page')
            ->withQueryString();

        return view('admin.documentations.index', compact('type', 'tourItems', 'shipItems', 'umrahItems'));
    }

    public function create()
    {
        return view('admin.documentations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => ['required', 'in:tour,ship,umrah'],
            'type' => ['required', 'in:photo,video'],
            'title' => ['nullable', 'string', 'max:120'],
            'source' => ['required', 'in:upload,link'],

            // upload
            'files' => ['required_if:source,upload', 'array'],
            'files.*' => ['file', 'max:51200'],

            // link
            'embed_links' => ['nullable', 'required_if:source,link', 'string'],


            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $created = 0;

        // UPLOAD MODE
        if ($data['source'] === 'upload') {
            foreach ($request->file('files', []) as $file) {
                $dir = $data['type'] === 'photo'
                    ? 'documentations/photos'
                    : 'documentations/videos';

                $path = $file->store($dir, 'public');

                $doc = Documentation::create([
                    'category' => $data['category'],
                    'type' => $data['type'],
                    'title' => $data['title'],
                    'file_path' => $path,
                    'is_active' => $request->boolean('is_active', true),
                    'sort_order' => $data['sort_order'] ?? 0,
                ]);

                \App\Jobs\Translate\DocumentationToEn::dispatch($doc->id)->onQueue('translations');

                $created++;
            }
        }

        // LINK MODE
        if ($data['source'] === 'link') {
            $lines = preg_split("/\r\n|\n|\r/", $data['embed_links']);

            foreach ($lines as $link) {
                $link = trim($link);
                if (!$link) continue;

                // kalau iframe → ambil src
                if (stripos($link, '<iframe') !== false) {
                    preg_match('/src=["\']([^"\']+)/i', $link, $m);
                    $link = $m[1] ?? '';
                }

                if (!preg_match('#^https?://#i', $link)) continue;

                $doc = Documentation::create([
                    'category' => $data['category'],
                    'type' => $data['type'],
                    'title' => $data['title'],
                    'file_path' => $link,
                    'is_active' => $request->boolean('is_active', true),
                    'sort_order' => $data['sort_order'] ?? 0,
                ]);

                \App\Jobs\Translate\DocumentationToEn::dispatch($doc->id)->onQueue('translations');

                $created++;
            }
        }

        return redirect()->route('admin.documentations.index')
            ->with('success', "Berhasil menambahkan {$created} dokumentasi.");
    }


    public function edit(Documentation $documentation)
    {
        return view('admin.documentations.edit', compact('documentation'));
    }

    public function update(Request $request, Documentation $documentation)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'source' => ['nullable', 'in:upload,link'],
            'replace_file' => ['nullable', 'file', 'max:51200'],
            'embed_link' => ['nullable', 'string', 'max:5000'],

        ]);

        $documentation->title = $data['title'] ?? $documentation->title;
        $documentation->is_active = $request->boolean('is_active', $documentation->is_active);
        $documentation->sort_order = $data['sort_order'] ?? $documentation->sort_order;

        // GANTI KE LINK
        if (($data['source'] ?? null) === 'link' && !empty($data['embed_link'])) {
            $link = trim($data['embed_link']);

            if (stripos($link, '<iframe') !== false) {
                preg_match('/src=["\']([^"\']+)/i', $link, $m);
                $link = $m[1] ?? '';
            }

            if (preg_match('#^https?://#i', $link)) {
                if (!$documentation->is_external) {
                    Storage::disk('public')->delete($documentation->file_path);
                }
                $documentation->file_path = $link;
            }
        }

        // GANTI KE FILE
        if (($data['source'] ?? null) === 'upload' && $request->hasFile('replace_file')) {
            if (!$documentation->is_external) {
                Storage::disk('public')->delete($documentation->file_path);
            }

            $dir = $documentation->type === 'photo'
                ? 'documentations/photos'
                : 'documentations/videos';

            $path = $request->file('replace_file')->store($dir, 'public');
            $documentation->file_path = $path;
        }

        $documentation->save();

        \App\Jobs\Translate\DocumentationToEn::dispatch($documentation->id)->onQueue('translations');

        return redirect()->route('admin.documentations.index')
            ->with('success', 'Dokumentasi berhasil diperbarui.');
    }


    public function destroy(Documentation $documentation)
    {
        if (!$documentation->is_external) {
            Storage::disk('public')->delete($documentation->file_path);
        }
        $documentation->delete();


        return back()->with('success', 'Dokumentasi dihapus.');
    }
}
