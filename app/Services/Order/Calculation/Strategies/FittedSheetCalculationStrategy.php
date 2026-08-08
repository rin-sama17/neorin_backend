<?php

namespace App\Services\Order\Calculation\Strategies;

use App\Models\Shop\CartItem;
use App\Models\Product\CalculationProfile;
use App\Models\Product\CustomProductItem;
use App\Services\Order\Calculation\Contracts\CalculationStrategy;

class FittedSheetCalculationStrategy extends BaseCalculationStrategy implements CalculationStrategy
{
    protected int $elasticCost = 0;

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

        $elasticRate = (int) $this->profile->getExtraConfig('elastic_rate', 0);
        $this->elasticCost = (int) round($elasticRate * $this->perimeter / 100);
    }

    protected function calculateLabor(CustomProductItem $productItem): void
    {
        parent::calculateLabor($productItem);

        $this->laborCost += $this->elasticCost;
    }

    public function buildBreakdown(array $pricing): array
    {
        $breakdown = parent::buildBreakdown($pricing);
        $breakdown['elastic'] = $this->elasticCost;
        return $breakdown;
    }

    public function getBreakdownLabel(): string
    {
        return 'fitted_sheet';
    }
}
