<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::orderBy('created_at', 'desc')->get();
        return view('admin.promos.index', compact('promos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:promos,code',
            'type' => 'required|in:percentage,nominal',
            'value' => 'required|numeric|min:1',
        ]);

        Promo::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'is_active' => true,
        ]);

        return back()->with('success', 'Promo berhasil ditambahkan.');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return back()->with('success', 'Promo berhasil dihapus.');
    }
}
