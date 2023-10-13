<?php

namespace App\Format;

use Symfony\Component\Yaml\Yaml;

class YamlFormat implements FormatInterface
{
    private const REPLACEMENTS = [
        '\n' => '{LF}',
        '\r' => '{CR}',
        '\t' => '{TAB}',
        '\\' => '{BS}',
    ];

    public function supports(string $format) : bool
    {
        return 'yaml' === $format;
    }

    public function unpack(string $content) : array
    {
        return Yaml::parse($content);
    }

    public function pack(array $data) : string
    {
        return Yaml::dump($data, 100, 2);
    }

    public function unescape(string $data) : string
    {
        return strtr($data, array_flip(self::REPLACEMENTS));
    }

    public function escape(string $data) : string
    {
        return strtr($data, self::REPLACEMENTS);
    }
}