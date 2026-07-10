<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TopupController extends Controller
{
    public function index()
    {
        return view('front.user.topup.index');
    }
}
