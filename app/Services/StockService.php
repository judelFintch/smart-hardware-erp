<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function recordMovement(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $movement = StockMovement::create($data);

            $productId = $movement->product_id;

            if ($movement->to_location_id) {
                $this->applyInbound($productId, $movement->to_location_id, (float) $movement->quantity, (float) $movement->unit_cost_local, (float) $movement->unit_sale_price_local);
            }

            if ($movement->from_location_id) {
                $this->applyOutbound($productId, $movement->from_location_id, (float) $movement->quantity, (float) $movement->unit_sale_price_local);
            }

            return $movement;
        });
    }

    private function applyInbound(int $productId, int $locationId, float $quantity, float $unitCost, float $unitSalePrice): void
    {
        $balance = StockBalance::firstOrNew([
            'product_id' => $productId,
            'location_id' => $locationId,
        ]);

        $oldQty = (float) $balance->quantity;
        $newQty = $oldQty + $quantity;
        $oldAvgCost = (float) $balance->avg_cost_local;

        $newAvgCost = $newQty > 0
            ? (($oldQty * $oldAvgCost) + ($quantity * $unitCost)) / $newQty
            : 0;

        $balance->quantity = $newQty;
        $balance->avg_cost_local = $newAvgCost;

        $product = Product::find($productId);
        $margin = (float) ($product?->sale_margin_percent ?? 0);
        $manualSalePrice = (float) ($product?->sale_price_local ?? 0);
        $calculatedSalePrice = $newAvgCost > 0 ? $newAvgCost * (1 + ($margin / 100)) : 0;
        $resolvedSalePrice = $manualSalePrice > 0 ? $manualSalePrice : $calculatedSalePrice;

        $balance->sale_price_local = $resolvedSalePrice > 0 ? $resolvedSalePrice : $balance->sale_price_local;

        $balance->save();

        if ($product) {
            $product->update([
                'avg_cost_local' => $newAvgCost,
                'sale_price_local' => $resolvedSalePrice > 0 ? $resolvedSalePrice : $product->sale_price_local,
            ]);
        }
    }

    private function applyOutbound(int $productId, int $locationId, float $quantity, float $unitSalePrice): void
    {
        $balance = StockBalance::firstOrNew([
            'product_id' => $productId,
            'location_id' => $locationId,
        ]);

        $balance->quantity = max(0, (float) $balance->quantity - $quantity);

        if ($unitSalePrice > 0) {
            $balance->sale_price_local = $unitSalePrice;
        }

        $balance->save();
    }
}
