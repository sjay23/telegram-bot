<?php

namespace App\Entity;

class Room
{
    const rooms = [
        0 => 'Любое',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
    ];

    public static function getRooms(): ?array
    {
        return self::rooms;
    }

    public static function getType($rooms): ?string
    {
        $result = [];
        foreach ($rooms as $room){
            if(isset(self::rooms[$room])) {
                $result[] = self::rooms[$room];
            }

        }
        return implode(',', $result);
    }
}
