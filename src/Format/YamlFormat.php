<?php

namespace App\Format;

use Symfony\Component\Yaml\Yaml;

class YamlFormat implements FormatInterface
{
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
}