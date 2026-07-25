<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('front.pages.dynamic', compact('page'));
    }
}
