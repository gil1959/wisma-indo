<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function dijual()
    {
        return view('front.listings.index', ['type' => 'dijual']);
    }

    public function disewakan()
    {
        return view('front.listings.index', ['type' => 'disewakan']);
    }

    public function properti()
    {
        return view('front.listings.index', ['type' => 'properti']);
    }

    public function barangJasa()
    {
        return view('front.listings.index', ['type' => 'barang-jasa']);
    }
}
