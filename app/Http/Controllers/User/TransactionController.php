<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = \App\Models\TopupTransaction::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->latest()
            ->paginate(10);
            
        return view('front.user.transactions.index', compact('transactions'));
    }
}
