<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function clearCache(Request $request)
    {
        // Extra safety (walau sudah di middleware)
        abort_unless(auth()->user()?->hasRole('admin'), 403);

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');

        return back()->with('success', 'Cache berhasil dibersihkan.');
    }
}
