<?php

namespace App\Mappers;

use App\Dtos\Data\StockMovementData;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Collection;

class StockMovementMapper
{
    public static function toData(StockMovement $movement): StockMovementData
    {
        return new StockMovementData(
            id: $movement->id,
            type: $movement->type,
            quantity: (int) $movement->quantity,
            reference: $movement->reference,
            notes: $movement->notes,
            product: $movement->relationLoaded('product') && $movement->product ? ProductMapper::toBasicData($movement->product) : null,
            user: $movement->relationLoaded('user') && $movement->user ? UserMapper::toData($movement->user) : null,
            createdAt: $movement->created_at->toDateTimeImmutable(),
            updatedAt: $movement->updated_at?->toDateTimeImmutable(),
        );
    }

    /**
     * @param  Collection<int, StockMovement>  $movements
     * @return Collection<int, StockMovementData>
     */
    public static function toDataCollection(Collection $movements): Collection
    {
        return $movements->map(fn (StockMovement $movement) => self::toData($movement));
    }
}
