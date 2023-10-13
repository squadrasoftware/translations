<?php

namespace App\Data\Catalog;

class Key
{
    private string $key;
    private array $local = [];
    private array $remote = [];
    private bool $updated = false;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getKey() : string
    {
        return $this->key;
    }

    public function addLocalValue(string $language, string $value) : void
    {
        $this->local[$language] = $value;
    }

    public function addRemoteValue(string $language, string $value) : void
    {
        $this->remote[$language] = $value;
    }

    public function hasNoChanges() : bool
    {
        asort($this->local);
        asort($this->remote);

        return $this->local === $this->remote;
    }

    public function getLanguages() : array
    {
        return array_unique(
            array_merge(
                array_keys($this->local),
                array_keys($this->remote)
            )
        );
    }

    public function getLocalValue(string $filename) : ?string
    {
        return $this->local[$filename] ?? null;
    }

    public function getRemoteValue(string $filename) : ?string
    {
        return $this->remote[$filename] ?? null;
    }

    public function keepRemote() : void
    {
        $this->local = $this->remote;
        $this->updated = true;
    }

    public function keepLocal() : void
    {
        $this->remote = $this->local;
        $this->updated = true;
    }

    public function keepRemoteForLanguage(string $language) : void
    {
        $this->local[$language] = $this->remote[$language] ?? '';
        $this->updated = true;
    }

    public function keepLocalForLanguage(string $language) : void
    {
        $this->remote[$language] = $this->local[$language] ?? '';
        $this->updated = true;
    }

    public function hasUpdates() : bool
    {
        return $this->updated;
    }

    public function __toString() : string
    {
        return $this->getKey();
    }
}