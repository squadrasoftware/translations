<?php

namespace App\Data\Catalog;

class File
{
    private string $fileId;
    private string $languageId;
    private string $language;
    private bool $isMainLanguage;
    private string $filename;

    public function __construct(string $fileId,
        string $languageId,
        string $language,
        bool $isMainLanguage,
        string $filename)
    {
        $this->fileId = $fileId;
        $this->languageId = $languageId;
        $this->language = $language;
        $this->isMainLanguage = $isMainLanguage;
        $this->filename = $filename;
    }

    public function getFileId() : string
    {
        return $this->fileId;
    }

    public function getLanguageId() : string
    {
        return $this->languageId;
    }

    public function getLanguage() : string
    {
        return $this->language;
    }

    public function isMainLanguage() : bool
    {
        return $this->isMainLanguage;
    }

    public function getFilename() : string
    {
        return $this->filename;
    }
}