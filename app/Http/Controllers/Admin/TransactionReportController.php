<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\TopupTransaction;
use App\Models\PartnerSubscription;
use App\Models\ListingTransaction; // if it exists, wait, earlier I saw ListingTransaction used in User\TransactionController

class TransactionReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();
        $status = $request->status;

        $topupsQuery = TopupTransaction::with('user')
            ->whereBetween('created_at', [$startDate, $endDate]);
            
        $partnerSubsQuery = PartnerSubscription::with(['user', 'package'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('amount', '>', 0); // Exclude free
            
        if ($status) {
            $topupsQuery->where('status', $status);
            $partnerSubsQuery->where('status', $status == 'success' ? 'active' : $status);
        }

        $topups = $topupsQuery->get()->map(function ($item) {
            return (object) [
                'id' => 'TOPUP-' . $item->id,
                'created_at' => $item->created_at,
                'user' => $item->user->name ?? 'Unknown',
                'type' => 'Top Up Kuota (' . $item->amount . ' listing)',
                'amount' => $item->price,
                'status' => $item->status,
                'payment_method' => 'N/A' // Topup model doesn't have it explicitly stored? Let's assume Transfer
            ];
        });

        $partnerSubs = $partnerSubsQuery->get()->map(function ($item) {
            return (object) [
                'id' => 'PARTNER-' . $item->id,
                'created_at' => $item->created_at,
                'user' => $item->user->name ?? 'Unknown',
                'type' => 'Langganan Partner (' . $item->package->name . ')',
                'amount' => $item->amount,
                'status' => $item->status == 'active' ? 'success' : $item->status,
                'payment_method' => $item->payment_method ?? 'Transfer'
            ];
        });

        $transactions = collect($topups)->merge($partnerSubs)->sortByDesc('created_at');

        $totalIncome = $transactions->where('status', 'success')->sum('amount');
        $totalPending = $transactions->where('status', 'pending')->sum('amount');
        
        // Paginate manually since it's a collection
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 50;
        $paginatedTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions->forPage($page, $perPage),
            $transactions->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        if ($request->has('print')) {
            return view('admin.reports.print', compact('transactions', 'startDate', 'endDate', 'totalIncome', 'totalPending'));
        }

        return view('admin.reports.index', compact('paginatedTransactions', 'startDate', 'endDate', 'totalIncome', 'totalPending', 'status'));
    }
}
