<?php

namespace App\Services\Order\Calculation\Contracts;

use App\Models\Shop\CartItem;
use App\Models\Product\CalculationProfile;
use App\Models\Product\CustomProductItem;

interface CalculationStrategy
{
    public function calculate(CartItem $item, CustomProductItem $productItem, CalculationProfile $profile): array;

    public function getBreakdownLabel(): string;
}
