<?php

namespace App\Support;

use App\Models\Order;
use App\Models\User;
use App\Models\TourPackage;
use App\Models\RentCarPackage;
use App\Models\ShipPackage;

class OrderPartnerResolver
{
    public static function resolvePartnerUserId(Order $order): ?int
    {
        if (!$order->type || !$order->product_id) return null;

        return match ($order->type) {
            'tour' => TourPackage::where('id', $order->product_id)->value('created_by_partner_id'),
            'rent_car' => RentCarPackage::where('id', $order->product_id)->value('created_by_partner_id'),
            'ship' => ShipPackage::where('id', $order->product_id)->value('created_by_partner_id'),
            default => null,
        };
    }

    public static function resolvePartnerUser(Order $order): ?User
    {
        $pid = self::resolvePartnerUserId($order);
        if (!$pid) return null;
        return User::find($pid);
    }

    // normalize WA: "08xxx" -> "628xxx"
    public static function normalizeWhatsapp(?string $raw): ?string
    {
        $raw = (string) $raw;
        $wa = preg_replace('/\D+/', '', $raw);

        if (!$wa) return null;

        if (str_starts_with($wa, '0')) {
            $wa = '62' . substr($wa, 1);
        }

        // kalau user simpan "62..." biarin
        // kalau simpan "+62..." udah ke-strip jadi "62..."
        return $wa ?: null;
    }

    public static function buildWaLink(string $wa, string $message): string
    {
        return "https://wa.me/{$wa}?text=" . urlencode($message);
    }
}
