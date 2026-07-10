<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SimulasiController extends Controller
{
    public function index()
    {
        return view('front.pages.simulasi');
    }
}
