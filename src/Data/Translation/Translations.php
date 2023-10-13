<?php

namespace App\Data\Translation;

class Translations
{
    /**
     * @var TranslationFile[]
     */
    private array $files = [];

    public function addFile(TranslationFile $file) : self
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * @return TranslationFile[]
     */
    public function getFiles() : array
    {
        usort($this->files, function (TranslationFile $a, TranslationFile $b) {
            return $b->isMainLanguage() <=> $a->isMainLanguage();
        });

        return $this->files;
    }

    public function getFile(string $name) : ?TranslationFile
    {
        foreach ($this->files as $file) {
            if ($file->getFilename() === $name) {
                return $file;
            }
        }

        return null;
    }

    public function toArray() : array
    {
        $files = [];
        foreach ($this->files as $file) {
            $files[] = $file->toArray();
        }

        return $files;
    }

    static public function fromArray(array $files) : self
    {
        $translations = new self();

        foreach ($files as $file) {
            $translations->addFile(TranslationFile::fromArray($file));
        }

        return $translations;
    }
}