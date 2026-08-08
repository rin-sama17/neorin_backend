<?php

namespace App\Services\Order\Calculation;

use App\Models\Shop\CartItem;
use App\Models\Product\CustomProductItem;
use App\Services\Order\Calculation\Contracts\CalculationStrategy;
use App\Services\Order\Calculation\Strategies\QuiltCalculationStrategy;
use App\Services\Order\Calculation\Strategies\FittedSheetCalculationStrategy;
use App\Services\Order\Calculation\Strategies\PillowCalculationStrategy;
use App\Services\Order\Calculation\Strategies\MattressCoverCalculationStrategy;
use App\Services\Order\Calculation\Strategies\BlanketCalculationStrategy;
use InvalidArgumentException;

class StrategyResolver
{
    private const STRATEGIES = [
        'quilt'          => QuiltCalculationStrategy::class,
        'fitted_sheet'   => FittedSheetCalculationStrategy::class,
        'pillow'         => PillowCalculationStrategy::class,
        'mattress_cover' => MattressCoverCalculationStrategy::class,
        'blanket'        => BlanketCalculationStrategy::class,
    ];

    public function resolve(string $strategyType): CalculationStrategy
    {
        $class = self::STRATEGIES[$strategyType] ?? null;

        if (!$class) {
            throw new InvalidArgumentException(
                "Calculation strategy [{$strategyType}] is not registered."
            );
        }

        return new $class();
    }

    public function calculateForItem(CartItem $item, CustomProductItem $productItem): array
    {
        $item->loadMissing([
            'fabrics',
            'categoryValues.attribute',
            'categoryValues.categoryValue',
        ]);

        $profile = $productItem->calculationProfile;

        if (!$profile || !$profile->is_active) {
            return ['total' => 0];
        }

        $strategy = $this->resolve($profile->strategy_type);

        return $strategy->calculate($item, $productItem, $profile);
    }

    public function calculate(CartItem $item): array
    {
        $itemConfig = $item->configuration ?? [];
        $itemId     = $itemConfig['custom_product_item_id'] ?? null;

        if (!$itemId) {
            return ['total' => 0];
        }

        $productItem = CustomProductItem::with('calculationProfile')->find($itemId);

        if (!$productItem) {
            return ['total' => 0];
        }

        return $this->calculateForItem($item, $productItem);
    }

    public function getRegisteredStrategies(): array
    {
        return array_keys(self::STRATEGIES);
    }
}
