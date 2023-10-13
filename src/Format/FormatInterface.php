<?php

namespace App\Format;

interface FormatInterface
{
    public function supports(string $format) : bool;

    /**
     * Denormalize the JSON/YAML string to an array
     */
    public function unpack(string $content) : array;

    /**
     * Normalize the array to a JSON/YAML string
     */
    public function pack(array $data) : string;

    /**
     * Unescape the string from characters that are not supported by the provider
     * (ex: {NL} are replaced by \n)
     */
    public function unescape(string $data) : string;

    /**
     * Escape the string to characters that are not supported by the provider
     * (ex: \n are replaced by {NL} to prevent to be evaluated as a new line)
     */
    public function escape(string $data) : string;
}