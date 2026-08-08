<?php

namespace App\Services\Order\Calculation\Strategies;

use App\Models\Shop\CartItem;
use App\Models\Product\CalculationProfile;
use App\Models\Product\CustomProductItem;
use App\Http\Services\CustomProduct\FabricConsumptionCalculator;

abstract class BaseCalculationStrategy
{
    protected FabricConsumptionCalculator $fabricCalc;

    protected array $dimensions = [];

    protected float $width = 0;

    protected float $height = 0;

    protected float $depth = 0;

    protected float $area = 0;

    protected float $perimeter = 0;

    protected int $fabricCost = 0;

    protected int $laborCost = 0;

    protected int $accessoriesCost = 0;

    protected int $fiberCost = 0;

    protected int $productionCost = 0;

    protected array $fabricSections = [];

    protected CalculationProfile $profile;

    public function __construct()
    {
        $this->fabricCalc = app(FabricConsumptionCalculator::class);
    }

    protected function extractDimensions(CartItem $item): void
    {
        $this->dimensions = $item->configuration['dimensions'] ?? [];

        $this->width  = (float) ($this->dimensions['width'] ?? 0);
        $this->height = (float) ($this->dimensions['height'] ?? 0);
        $this->depth  = (float) ($this->dimensions['depth'] ?? 0);

        if ($this->width > 0 && $this->height > 0) {
            $this->area      = $this->width * $this->height;
            $this->perimeter = 2 * ($this->width + $this->height);
        }
    }

    protected function calculateFabricSections(CartItem $item, CustomProductItem $productItem): void
    {
        $this->fabricSections = [];
        $this->fabricCost = 0;
    }

    protected function calculateLabor(CustomProductItem $productItem): void
    {
        $laborConfig = $this->profile->getLaborConfig();

        $rateType = $laborConfig['type'] ?? 'fixed';
        $rate     = (int) ($laborConfig['rate'] ?? 0);

        $this->laborCost = match ($rateType) {
            'per_area'  => (int) round($rate * $this->area / 10000),
            'per_meter' => (int) round($rate * $this->perimeter / 100),
            default     => $rate,
        };
    }

    protected function calculateFiber(CartItem $item, CustomProductItem $productItem): void
    {
        $this->fiberCost = 0;
    }

    protected function calculateAccessories(CartItem $item, CustomProductItem $productItem): void
    {
        $this->accessoriesCost = (int) $item->categoryValues
            ->filter(fn($cv) => $cv->category_value_id !== null)
            ->sum(fn($cv) => (int) ($cv->categoryValue?->price ?? 0));
    }

    protected function calculateProduction(CustomProductItem $productItem): void
    {
        $this->productionCost = (int) $this->profile->getExtraConfig('production_rate', 0);
    }

    protected function getSubtotal(): int
    {
        return $this->fabricCost
            + $this->laborCost
            + $this->accessoriesCost
            + $this->fiberCost
            + $this->productionCost;
    }

    protected function applyProfitAndTax(int $subtotal): array
    {
        $profitPercent = (float) $this->profile->profit_percent;
        $vatPercent    = (float) $this->profile->vat_percent;

        $profit = (int) round($subtotal * $profitPercent / 100);
        $vat    = (int) round(($subtotal + $profit) * $vatPercent / 100);

        return [
            'subtotal'    => $subtotal,
            'profit'      => $profit,
            'profit_pct'  => $profitPercent,
            'vat'         => $vat,
            'vat_pct'     => $vatPercent,
            'total'       => $subtotal + $profit + $vat,
        ];
    }

    public function calculate(CartItem $item, CustomProductItem $productItem, CalculationProfile $profile): array
    {
        $this->profile = $profile;

        $this->extractDimensions($item);
        $this->calculateFabricSections($item, $productItem);
        $this->calculateLabor($productItem);
        $this->calculateFiber($item, $productItem);
        $this->calculateAccessories($item, $productItem);
        $this->calculateProduction($productItem);

        $subtotal = $this->getSubtotal();
        $pricing  = $this->applyProfitAndTax($subtotal);

        return $this->buildBreakdown($pricing);
    }

    protected function buildBreakdown(array $pricing): array
    {
        return [
            'dimensions'      => [
                'width'     => $this->width,
                'height'    => $this->height,
                'depth'     => $this->depth,
                'area'      => $this->area,
                'perimeter' => $this->perimeter,
            ],
            'fabric_sections' => $this->fabricSections,
            'fabric'          => $this->fabricCost,
            'labor'           => $this->laborCost,
            'fiber'           => $this->fiberCost,
            'accessories'     => $this->accessoriesCost,
            'production'      => $this->productionCost,
            'subtotal'        => $pricing['subtotal'],
            'profit'          => $pricing['profit'],
            'profit_pct'      => $pricing['profit_pct'],
            'vat'             => $pricing['vat'],
            'vat_pct'         => $pricing['vat_pct'],
            'total'           => $pricing['total'],
        ];
    }
}
