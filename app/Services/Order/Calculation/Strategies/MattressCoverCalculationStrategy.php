<?php

namespace App\Services\Order\Calculation\Strategies;

use App\Models\Shop\CartItem;
use App\Models\Product\CalculationProfile;
use App\Models\Product\CustomProductItem;
use App\Services\Order\Calculation\Contracts\CalculationStrategy;

class MattressCoverCalculationStrategy extends BaseCalculationStrategy implements CalculationStrategy
{
    protected int $zipperCost = 0;

    protected int $handleCost = 0;

    protected function calculateFabricSections(CartItem $item, CustomProductItem $productItem): void
    {
        $this->fabricSections = [];
        $this->fabricCost = 0;

        $fabrics = $item->fabrics;
        if ($fabrics->isEmpty()) {
            return;
        }

        $fabric = $fabrics->first();

        $requiredWidth  = $this->width + (2 * $this->depth);
        $requiredLength = $this->height + (2 * $this->depth);

        $section = $this->fabricCalc->calculateForSection($requiredWidth, $requiredLength, $fabric);
        $this->fabricSections['main'] = $section;
        $this->fabricCost += $section['total_cost'];

        $zipperRate = (int) $this->profile->getExtraConfig('zipper_rate', 0);
        $this->zipperCost = (int) round($zipperRate * $this->perimeter / 100);

        $handleRate  = (int) $this->profile->getExtraConfig('handle_rate', 0);
        $handleCount = (int) $this->profile->getExtraConfig('handle_count', 0);
        $this->handleCost = $handleRate * $handleCount;
    }

    protected function calculateLabor(CustomProductItem $productItem): void
    {
        parent::calculateLabor($productItem);
        $this->laborCost += $this->zipperCost + $this->handleCost;
    }

    public function buildBreakdown(array $pricing): array
    {
        $breakdown = parent::buildBreakdown($pricing);
        $breakdown['zipper']  = $this->zipperCost;
        $breakdown['handles'] = $this->handleCost;
        return $breakdown;
    }

    public function getBreakdownLabel(): string
    {
        return 'mattress_cover';
    }
}
