<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Helper;

use App\Model\Ecommerce\Entity\Attribute\Field;

class FiltersParser
{
    static $default = [];

    public static function getFilters(string $request = ''): array
    {
        if ('' === $request) {
            return self::$default;
        }

        $filters = explode(',', $request);

        if (0 === count($filters)) {
            return self::$default;
        }

        return array_map(
            static function ($filter) {
                $params = explode('_', $filter);
                $filter = ['slug' => $params[0], 'type' => $params[1]];
                if (Field::NUMBER === $params[1]) {
                    $range = explode(';', $params[2], 2);
                    $filter['from'] = $range[0];
                    $filter['to'] = $range[1];
                }
                if (Field::SELECT === $params[1]) {
                    $filter['value'] = $params[2];
                }
                return $filter;
            }, $filters);

    }

    public static function getFiltersGroupByType(string $request = ''): array
    {
        $filters = self::getFilters($request);
        if (0 === count($filters)) {
            return self::$default;
        }

        $groups = [];
        foreach ($filters as $filter) {
            $groups[$filter['type']][$filter['slug']] = $filter;
        }
        return $groups;
    }

    public static function hasGroup(array &$groups, string $nameGroup): bool
    {
        return array_key_exists($nameGroup, $groups);
    }

    public static function hasFilterInGroup(array &$groups, string $nameGroup, string $filter): bool
    {
        if (!self::hasGroup($groups, $nameGroup)) {
            return false;
        }
        return array_key_exists($filter, $groups[$nameGroup]);
    }

    public static function getChooseValue(array &$groups, string $nameGroup, string $filter): ?array
    {
        if (!self::hasFilterInGroup($groups, $nameGroup, $filter)) {
            return null;
        }
        return  $groups[$nameGroup][$filter];
    }
}
