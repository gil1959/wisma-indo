<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\UmrahPackage;
use App\Models\UmrahCategory;

class UmrahController extends Controller
{
    public function index()
    {
        $categories = UmrahCategory::orderBy('name')->get();
        $packages = UmrahPackage::with(['category', 'tiers'])
            ->where('is_active', 1)
            ->latest()
            ->paginate(12);

        return view('front.umrah.index', compact('packages','categories'));
    }

    public function show(UmrahPackage $umrahPackage)
    {
        $package = $umrahPackage->load(['photos','tiers','category','reviews']);
        return view('front.umrah.show', compact('package'));
    }
}
