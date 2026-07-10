<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientLogoController extends Controller
{
    public function index()
    {
        $logos = ClientLogo::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.client-logos.index', compact('logos'));
    }

    public function create()
    {
        return view('admin.client-logos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'logo' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'url' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $path = $request->file('logo')->store('client-logos', 'public');

        ClientLogo::create([
            'name' => $data['name'],
            'image_path' => $path,
            'url' => $data['url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.client-logos.index')->with('success', 'Logo ditambahkan');
    }

    public function edit(ClientLogo $clientLogo)
    {
        return view('admin.client-logos.edit', compact('clientLogo'));
    }

    public function update(Request $request, ClientLogo $clientLogo)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'url' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($clientLogo->image_path);
            $clientLogo->image_path = $request->file('logo')->store('client-logos', 'public');
        }

        $clientLogo->update([
            'name' => $data['name'],
            'url' => $data['url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Logo diupdate');
    }

    public function destroy(ClientLogo $clientLogo)
    {
        Storage::disk('public')->delete($clientLogo->image_path);
        $clientLogo->delete();

        return back()->with('success', 'Logo dihapus');
    }
}
