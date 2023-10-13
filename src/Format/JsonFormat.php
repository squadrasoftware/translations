<?php

namespace App\Format;

class JsonFormat implements FormatInterface
{
    public function supports(string $format) : bool
    {
        return 'json' === $format;
    }

    public function unpack(string $content) : array
    {
        return json_decode($content, true);
    }

    public function pack(array $data) : string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}