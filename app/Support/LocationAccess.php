<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class LocationAccess
{
    public static function user(?User $user = null): ?User
    {
        return $user ?? auth()->user();
    }

    public static function hasGlobalAccess(?User $user = null): bool
    {
        $user = static::user($user);

        return !$user || $user->isAdministrator();
    }

    public static function assignedLocationId(?User $user = null): ?int
    {
        return static::user($user)?->stock_location_id;
    }

    public static function restrictLocations(Builder $query, ?User $user = null): Builder
    {
        if (static::hasGlobalAccess($user)) {
            return $query;
        }

        $locationId = static::assignedLocationId($user);

        return $locationId
            ? $query->whereKey($locationId)
            : $query->whereRaw('1 = 0');
    }

    public static function filterByLocation(Builder $query, string|array $columns, ?User $user = null): Builder
    {
        if (static::hasGlobalAccess($user)) {
            return $query;
        }

        $locationId = static::assignedLocationId($user);
        if (!$locationId) {
            return $query->whereRaw('1 = 0');
        }

        $columns = (array) $columns;

        return $query->where(function (Builder $builder) use ($columns, $locationId) {
            foreach ($columns as $column) {
                $builder->orWhere($column, $locationId);
            }
        });
    }

    public static function filterSales(Builder $query, ?User $user = null): Builder
    {
        if (static::hasGlobalAccess($user)) {
            return $query;
        }

        $locationId = static::assignedLocationId($user);
        if (!$locationId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('items', fn (Builder $builder) => $builder->where('location_id', $locationId));
    }

    public static function filterPurchases(Builder $query, ?User $user = null): Builder
    {
        return static::filterByLocation($query, 'receive_location_id', $user);
    }

    public static function filterInventoryCounts(Builder $query, ?User $user = null): Builder
    {
        return static::filterByLocation($query, 'location_id', $user);
    }

    public static function ensureLocationAllowed(?int $locationId, ?User $user = null, ?string $message = null): void
    {
        if (static::hasGlobalAccess($user)) {
            return;
        }

        abort_unless(
            $locationId && $locationId === static::assignedLocationId($user),
            403,
            $message ?? 'Acces non autorise pour ce magasin ou depot.'
        );
    }
}
