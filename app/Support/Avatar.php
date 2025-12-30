<?php

namespace App\Support;

class Avatar
{
    public static function url(?string $url, string $name): string
    {
        return $url ?? self::generateInitials($name);
    }

    public static function generateInitials(string $name): string
    {
        $initials = collect(explode(' ', $name))
            ->map(fn (string $word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->join('');

        return "https://ui-avatars.com/api/?name={$initials}&background=random";
    }
}
