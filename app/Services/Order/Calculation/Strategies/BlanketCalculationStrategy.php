<?php

namespace App\Services\Order\Calculation\Strategies;

use App\Models\Shop\CartItem;
use App\Models\Product\CalculationProfile;
use App\Models\Product\CustomProductItem;
use App\Services\Order\Calculation\Contracts\CalculationStrategy;

class BlanketCalculationStrategy extends BaseCalculationStrategy implements CalculationStrategy
{
    protected function calculateFabricSections(CartItem $item, CustomProductItem $productItem): void
    {
        $this->fabricSections = [];
        $this->fabricCost = 0;

        $fabrics = $item->fabrics;
        if ($fabrics->isEmpty()) {
            return;
        }

        $fabric = $fabrics->first();

        $section = $this->fabricCalc->calculateForSection($this->width, $this->height, $fabric);
        $this->fabricSections['main'] = $section;
        $this->fabricCost += $section['total_cost'];
    }

    public function getBreakdownLabel(): string
    {
        return 'blanket';
    }
}
