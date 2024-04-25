<?php

namespace App\Data\Catalog;

use App\Data\Translation\TranslationFile;

class Catalog
{
    /**
     * @var File[]
     */
    private array $files = [];

    /**
     * @var Key[]
     */
    private array $keys = [];

    public function addLocalKeys(string $language, array $keys) : void
    {
        foreach ($keys as $key => $value) {
            if (!is_scalar($value)) {
                echo "Could not process \"{$key}\" key as it contain a non-scalar value:\n";
                dump($value);
                continue;
            }

            $this->getKey($key)->addLocalValue($language, $value);
        }
    }

    public function addRemoteKeys(string $language, array $keys) : void
    {
        foreach ($keys as $key => $value) {
            $this->getKey($key)->addRemoteValue($language, $value);
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

    public function addFile(TranslationFile $file) : self
    {
        $this->files[] = new File($file->getFileId(), $file->getLanguageId(), $file->getLanguage(), $file->isMainLanguage(), $file->getFilename());

        return $this;
    }

    public function getFile(string $language) : File
    {
        /** @var File $file */
        foreach ($this->files as $file) {
            if ($file->getLanguage() === $language) {
                return $file;
            }
        }

        throw new \RuntimeException(sprintf('Language %s not found', $language));
    }
}