<?php

namespace App\Entity;

class RentOrSale
{
    const type = [
        1 => 'Аренда',
        2 => 'Продажа'
    ];

    public static function getTypes(): ?array
    {
        return self::type;
    }

    public static function getType($type): ?string
    {
        return isset(self::type[$type]) ? self::type[$type] : null;
    }
}
