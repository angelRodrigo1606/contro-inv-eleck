<?php

namespace App\Mappers;

use App\Dtos\Data\SupplierData;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;

class SupplierMapper
{
    public static function toData(Supplier $supplier): SupplierData
    {
        return new SupplierData(
            id: $supplier->id,
            name: $supplier->name,
            contactName: $supplier->contact_name,
            phone: $supplier->phone,
            address: $supplier->address,
            email: $supplier->email,
            isActive: (bool) $supplier->is_active,
            createdAt: $supplier->created_at?->toDateTimeImmutable(),
            updatedAt: $supplier->updated_at?->toDateTimeImmutable(),
            deletedAt: $supplier->deleted_at?->toDateTimeImmutable(),
        );
    }

    /**
     * @param  Collection<int, Supplier>  $suppliers
     * @return Collection<int, SupplierData>
     */
    public static function toDataCollection(Collection $suppliers): Collection
    {
        return $suppliers->map(fn (Supplier $supplier) => self::toData($supplier));
    }
}
