<?php

namespace App\Format;

interface FormatInterface
{
    public function supports(string $format) : bool;

    public function unpack(string $content) : array;

    public function pack(array $data) : string;
}