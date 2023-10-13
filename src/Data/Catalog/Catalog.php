<?php

namespace App\Data\Catalog;

class Catalog
{
    /**
     * @var Key[]
     */
    private array $keys = [];

    public function addLocalKeys(string $filename, array $keys) : void
    {
        foreach ($keys as $key => $value) {
            $this->getKey($key)->addLocalValue($filename, $value);
        }
    }

    public function addRemoteKeys(string $filename, array $keys) : void
    {
        foreach ($keys as $key => $value) {
            $this->getKey($key)->addRemoteValue($filename, $value);
        }
    }

    public function getKey(string $key) : Key
    {
        if (!isset($this->keys[$key])) {
            $this->keys[$key] = new Key($key);
        }

        return $this->keys[$key];
    }

    public function getKeys() : array
    {
        return $this->keys;
    }

    public function hasUpdates() : bool
    {
        foreach ($this->keys as $key) {
            if ($key->hasUpdates()) {
                return true;
            }
        }

        return false;
    }
}