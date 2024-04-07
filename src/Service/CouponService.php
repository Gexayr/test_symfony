<?php
// src/Service/CouponService.php

namespace App\Service;

class CouponService
{
    private $coupons;

    public function __construct()
    {
        // Имитация данных о купонах с использованием ассоциативного массива
        $this->coupons = [
            'P10' => ['code' => 'P10', 'type' => 'percentage', 'value' => 0.10], // Скидка 10%
            'P100' => ['code' => 'P100', 'type' => 'fixed', 'value' => 100], // Скидка 100 евро
            'D15' => ['code' => 'D15', 'type' => 'percentage', 'value' => 0.15], // Скидка 15%
        ];
    }

    public function getCouponByCode(string $couponCode): ?array
    {
        return $this->coupons[$couponCode] ?? null;
    }
}
