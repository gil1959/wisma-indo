<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index()
    {
        return view('front.user.listings.index');
    }

    public function create(Request $request)
    {
        $kategori = $request->query('kategori', 'properti');
        return view('front.user.listings.create', compact('kategori'));
    }
}
