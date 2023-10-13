<?php

namespace App\Service;

class FilterService
{
    private const REPLACEMENTS = [
        '\n' => '{LF}',
        '\r' => '{CR}',
        '\t' => '{TAB}',
        '\\' => '{BS}',
    ];

    static public function importFilter(string $content) : string
    {
        return strtr($content, array_flip(self::REPLACEMENTS));
    }

    static public function exportFilter(string $content) : string
    {
        return strtr($content, self::REPLACEMENTS);
    }
}