<?php

namespace App\Entity;

class Type
{
    const type = [
            1 => 'Новострой',
            2 => 'Квартиры',
            3 => 'Дома/участки',
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
