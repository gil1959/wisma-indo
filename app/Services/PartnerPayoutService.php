<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\TourPackage;
use App\Models\RentCarPackage;
use App\Models\ShipPackage;
use App\Models\RestoranPackage;
use App\Models\HotelPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartnerPayoutService
{
    public function creditIfEligible(Order $order): void
    {
        // hanya paid + approved
        if ($order->payment_status !== 'paid' || $order->order_status !== 'approved') {
            return;
        }

        // idempotent guard
        if ($order->partner_payout_status === 'credited') {
            return;
        }

        // cari partner dari produk
        $partnerId = $this->resolvePartnerIdFromOrder($order);
        if (!$partnerId) {
            return;
        }

        $partner = User::query()->whereKey($partnerId)->first();
        if (!$partner) {
            return;
        }

        $gross = (int) ($order->final_price ?? 0);
        if ($gross <= 0) {
            return;
        }

        $taxPercent = (float) ($partner->partner_tax_percent ?? 0);
        if ($taxPercent < 0) $taxPercent = 0;
        if ($taxPercent > 100) $taxPercent = 100;

        $taxAmount = (int) round(($gross * $taxPercent) / 100);
        if ($taxAmount > $gross) $taxAmount = $gross;

        $net = $gross - $taxAmount;

        DB::transaction(function () use ($order, $partner, $partnerId, $gross, $taxPercent, $taxAmount, $net) {

            // lock partner row biar gak race condition
            $lockedPartner = User::query()->whereKey($partnerId)->lockForUpdate()->first();

            // guard ulang dalam transaksi
            $freshOrder = Order::query()->whereKey($order->id)->lockForUpdate()->first();
            if (!$freshOrder || $freshOrder->partner_payout_status === 'credited') {
                return;
            }

            $lockedPartner->partner_balance_available = (int) $lockedPartner->partner_balance_available + (int) $net;
            $lockedPartner->save();

            $freshOrder->partner_id = $partnerId;
            $freshOrder->partner_payout_status = 'credited';
            $freshOrder->partner_payout_gross = $gross;
            $freshOrder->partner_tax_percent_snapshot = $taxPercent;
            $freshOrder->partner_tax_amount = $taxAmount;
            $freshOrder->partner_payout_net = $net;
            $freshOrder->partner_credited_at = now();

            // saveQuietly biar gak loop event
            if (method_exists($freshOrder, 'saveQuietly')) {
                $freshOrder->saveQuietly();
            } else {
                $freshOrder->save();
            }
        });
    }

    public function resolvePartnerIdFromOrder(Order $order): ?int
    {
        try {
            if ($order->type === 'tour') {
                return (int) TourPackage::query()->whereKey($order->product_id)->value('created_by_partner_id') ?: null;
            }

            if ($order->type === 'rent_car') {
                return (int) RentCarPackage::query()->whereKey($order->product_id)->value('created_by_partner_id') ?: null;
            }

            if ($order->type === 'ship') {
                return (int) ShipPackage::query()->whereKey($order->product_id)->value('created_by_partner_id') ?: null;
            }

            if ($order->type === 'restoran') {
                return (int) RestoranPackage::query()->whereKey($order->product_id)->value('created_by_partner_id') ?: null;
            }

            if ($order->type === 'hotel') {
                return (int) HotelPackage::query()->whereKey($order->product_id)->value('created_by_partner_id') ?: null;
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('resolvePartnerIdFromOrder failed', [
                'order_id' => $order->id,
                'type' => $order->type,
                'err' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
