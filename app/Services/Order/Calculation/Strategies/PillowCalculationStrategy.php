<?php

namespace App\Services\Order\Calculation\Strategies;

use App\Models\Shop\CartItem;
use App\Models\Product\CalculationProfile;
use App\Models\Product\CustomProductItem;
use App\Services\Order\Calculation\Contracts\CalculationStrategy;

class PillowCalculationStrategy extends BaseCalculationStrategy implements CalculationStrategy
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

    protected function calculateFiber(CartItem $item, CustomProductItem $productItem): void
    {
        $fiberConfig = $this->profile->getFiberConfig();

        if (empty($fiberConfig) || !($fiberConfig['enabled'] ?? false)) {
            $this->fiberCost = 0;
            return;
        }

        $density = (int) ($fiberConfig['density_grams_per_m2'] ?? 200);
        $pricePerGram = (float) ($fiberConfig['price_per_gram'] ?? 0.1);

        $this->fiberCost = $this->fabricCalc->calculateFiber($this->area, $density, $pricePerGram);
    }

    public function getBreakdownLabel(): string
    {
        return 'pillow';
    }
}
