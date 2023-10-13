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

    public function addLocalValue(string $filename, string $value) : void
    {
        $this->local[$filename] = $value;
    }

    public function addRemoteValue(string $filename, string $value) : void
    {
        $this->remote[$filename] = $value;
    }

    public function hasNoChanges() : bool
    {
        asort($this->local);
        asort($this->remote);

        return $this->local === $this->remote;
    }

    public function getFilenames() : array
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

    public function keepRemoteForFilename(string $filename) : void
    {
        $this->local[$filename] = $this->remote[$filename] ?? '';
        $this->updated = true;
    }

    public function keepLocalForFilename(string $filename) : void
    {
        $this->remote[$filename] = $this->local[$filename] ?? '';
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