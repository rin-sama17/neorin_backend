<?php

namespace App\Http\Services\CustomProduct;

use App\Models\Product\Fabric;

class FabricConsumptionCalculator
{
    public function calculateForSection(float $requiredWidth, float $requiredLength, Fabric $fabric, bool $allowSplit = true): array
    {
        $fabricWidth = (float) ($fabric->width ?? 150);
        $consumedLength = $requiredLength;

        $split = false;
        $pieces = 1;

        if ($requiredWidth > $fabricWidth && $allowSplit) {
            $split = true;
            $pieces = (int) ceil($requiredWidth / $fabricWidth);
            $consumedLength = $requiredLength * $pieces;
        }

        $consumedMeters = round($consumedLength / 100, 2);

        $pricePerMeter = (int) $fabric->price;
        $totalFabricCost = (int) round($pricePerMeter * $consumedMeters);

        return [
            'fabric_id'        => $fabric->id,
            'fabric_title'     => $fabric->title,
            'fabric_width'     => $fabricWidth,
            'required_width'   => $requiredWidth,
            'required_length'  => $requiredLength,
            'split'            => $split,
            'pieces'           => $pieces,
            'consumed_meters'  => $consumedMeters,
            'price_per_meter'  => $pricePerMeter,
            'total_cost'       => $totalFabricCost,
        ];
    }

    public function calculateRuffle(float $perimeter, float $ruffleMultiplier, Fabric $fabric): array
    {
        $fabricWidth = (float) ($fabric->width ?? 150);

        $requiredWidth = $perimeter * $ruffleMultiplier;

        $pieces = (int) ceil($requiredWidth / $fabricWidth);
        $consumedMeters = round(($perimeter * $pieces) / 100, 2);

        $pricePerMeter = (int) $fabric->price;
        $totalFabricCost = (int) round($pricePerMeter * $consumedMeters);

        return [
            'fabric_id'        => $fabric->id,
            'fabric_title'     => $fabric->title,
            'fabric_width'     => $fabricWidth,
            'required_width'   => $requiredWidth,
            'perimeter'        => $perimeter,
            'ruffle_multiplier' => $ruffleMultiplier,
            'pieces'           => $pieces,
            'consumed_meters'  => $consumedMeters,
            'price_per_meter'  => $pricePerMeter,
            'total_cost'       => $totalFabricCost,
        ];
    }

    public function calculateFiber(float $areaCm, int $densityGramsPerM2, float $pricePerGram): int
    {
        $areaM2 = $areaCm / 10000;
        $weightGrams = $areaM2 * $densityGramsPerM2;

        return (int) round($weightGrams * $pricePerGram);
    }
}
