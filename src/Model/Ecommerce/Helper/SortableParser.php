<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Helper;

class SortableParser
{
    static $sort = ['popular', 'cost'];
    static $order = ['ASC', 'DESC'];
    static $default = ['slug' => 'popular', 'order' => 'ASC'];

    public static function getSort(string $request = ''): array
    {
        $sortable = explode('_', $request, 2);

        if (2 !== count($sortable)) {
            return self::$default;
        }

        $sort = mb_strtolower($sortable[0]);
        $order = mb_strtoupper($sortable[1]);

        if (!in_array($sort, self::$sort)) {
            return self::$default;
        }

        if (!in_array($order, self::$order)) {
            return self::$default;
        }

        return ['slug' => $sort, 'order' => $order];
    }
}
