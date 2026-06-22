<?php

namespace App\Mappers;

use App\Dtos\Data\LowStockAlertData;
use App\Models\LowStockAlert;
use Illuminate\Support\Collection;

class LowStockAlertMapper
{
    public static function toData(LowStockAlert $alert): LowStockAlertData
    {
        return new LowStockAlertData(
            id: $alert->id,
            product: $alert->product ? ProductMapper::toBasicData($alert->product) : throw new \RuntimeException('LowStockAlert requires loaded product'),
            resolvedAt: $alert->resolved_at?->toDateTimeImmutable(),
            createdAt: $alert->created_at->toDateTimeImmutable(),
            updatedAt: $alert->updated_at?->toDateTimeImmutable(),
        );
    }

    /**
     * @param  Collection<int, LowStockAlert>  $alerts
     * @return Collection<int, LowStockAlertData>
     */
    public static function toDataCollection(Collection $alerts): Collection
    {
        return $alerts->map(fn (LowStockAlert $alert) => self::toData($alert));
    }
}
