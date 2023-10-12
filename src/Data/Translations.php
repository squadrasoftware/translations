<?php

namespace App\Data;

class Translations
{
    /**
     * @var TranslationFile[]
     */
    private array $files = [];

    public function addFile(TranslationFile $file): self
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * @return TranslationFile[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    public function toArray(): array
    {
        $files = [];
        foreach ($this->files as $file) {
            $files[] = $file->toArray();
        }

        return $files;
    }

    static public function fromArray(array $files): self
    {
        $translations = new self();

        foreach ($files as $file) {
            $translations->addFile(TranslationFile::fromArray($file));
        }

        return $translations;
    }
}