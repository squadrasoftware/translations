<?php

namespace App\Service;

class FlatArrayService
{
    static public function flatten(array $original) : array
    {
        $array = [];

        foreach ($original as $key => $value) {
            if (null == $value or is_scalar($value)) {
                $array[$key] = $value;
            } else {
                foreach (FlatArrayService::flatten($value) as $childKey => $childValue) {
                    $array[sprintf('%s.%s', $key, $childKey)] = $childValue;
                }
            }
        }

        return $array;
    }

    static public function unflatten(array $array) : array
    {
        $original = [];

        foreach ($array as $key => $value) {
            $ref = &$original;
            foreach (explode('.', $key) as $node) {
                if (!array_key_exists($node, $ref)) {
                    $ref[$node] = [];
                }
                $ref = &$ref[$node];
            }
            $ref = $value;
        }

        return $original;
    }
}