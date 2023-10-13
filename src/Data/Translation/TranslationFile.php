<?php

namespace App\Data\Translation;

class TranslationFile
{
    private ?int $fileId = null;
    private ?string $languageId = null;
    private ?string $language = null;
    private bool $isMainLanguage = false;
    private ?string $filename;
    private ?string $content;

    public function __construct(?string $filename, ?string $content)
    {
        $this->filename = $filename;
        $this->content = $content;
    }

    public function getFilename() : ?string
    {
        return $this->filename;
    }

    public function getContent() : ?string
    {
        return $this->content;
    }

    public function getFileId() : ?int
    {
        return $this->fileId;
    }

    public function setFileId(?int $fileId) : void
    {
        $this->fileId = $fileId;
    }

    public function getLanguageId() : ?string
    {
        return $this->languageId;
    }

    public function setLanguageId(?string $languageId) : void
    {
        $this->languageId = $languageId;
    }

    public function getLanguage() : ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language) : void
    {
        $this->language = $language;
    }

    public function isMainLanguage() : bool
    {
        return $this->isMainLanguage;
    }

    public function setIsMainLanguage(bool $isMainLanguage) : void
    {
        $this->isMainLanguage = $isMainLanguage;
    }

    public function toArray() : array
    {
        return [
            'file_id' => $this->fileId,
            'language_id' => $this->languageId,
            'language' => $this->language,
            'is_main_language' => $this->isMainLanguage,
            'filename' => $this->filename,
            'content' => $this->content,
        ];
    }

    static public function fromArray(array $array) : self
    {
        $instance = new self($array['filename'], $array['content']);
        $instance->setFileId($array['file_id']);
        $instance->setLanguageId($array['language_id']);
        $instance->setLanguage($array['language']);
        $instance->setIsMainLanguage($array['is_main_language']);

        return $instance;
    }

    public function __toString() : string
    {
        return $this->filename;
    }
}