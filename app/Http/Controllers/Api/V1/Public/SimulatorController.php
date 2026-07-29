<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SimulatorController extends Controller
{
    public function kpr(Request $request)
    {
        $request->validate([
            'property_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0', // in percentage per year
            'tenor_years' => 'required|integer|min:1',
        ]);

        $price = $request->property_price;
        $dp = $request->down_payment;
        $principal = $price - $dp;
        
        $annualInterest = $request->interest_rate;
        $monthlyInterest = ($annualInterest / 100) / 12;
        
        $months = $request->tenor_years * 12;

        if ($monthlyInterest > 0) {
            $monthlyInstallment = $principal * ($monthlyInterest * pow(1 + $monthlyInterest, $months)) / (pow(1 + $monthlyInterest, $months) - 1);
        } else {
            $monthlyInstallment = $principal / $months;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'principal' => $principal,
                'monthly_installment' => round($monthlyInstallment),
                'total_payment' => round($monthlyInstallment * $months),
                'total_interest' => round(($monthlyInstallment * $months) - $principal),
            ]
        ]);
    }

    public function kemampuan(Request $request)
    {
        $request->validate([
            'monthly_income' => 'required|numeric|min:0',
            'other_installments' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'tenor_years' => 'required|integer|min:1',
        ]);

        $income = $request->monthly_income;
        $otherInstallments = $request->other_installments;
        
        // Usually banks allow 30% - 40% of income for total installments
        $maxInstallmentCapacity = ($income * 0.35) - $otherInstallments;
        
        if ($maxInstallmentCapacity <= 0) {
            return response()->json([
                'success' => true,
                'data' => [
                    'max_installment' => 0,
                    'max_property_price' => 0,
                    'message' => 'Your income capacity is insufficient for a new installment.'
                ]
            ]);
        }

        $annualInterest = $request->interest_rate;
        $monthlyInterest = ($annualInterest / 100) / 12;
        $months = $request->tenor_years * 12;

        if ($monthlyInterest > 0) {
            // P = C * ((1 + r)^n - 1) / (r * (1 + r)^n)
            $maxPrincipal = $maxInstallmentCapacity * (pow(1 + $monthlyInterest, $months) - 1) / ($monthlyInterest * pow(1 + $monthlyInterest, $months));
        } else {
            $maxPrincipal = $maxInstallmentCapacity * $months;
        }

        // Assuming DP is 20%
        $maxPropertyPrice = $maxPrincipal / 0.8;

        return response()->json([
            'success' => true,
            'data' => [
                'max_installment' => round($maxInstallmentCapacity),
                'max_principal' => round($maxPrincipal),
                'estimated_max_property_price_with_20_percent_dp' => round($maxPropertyPrice),
            ]
        ]);
    }
}
