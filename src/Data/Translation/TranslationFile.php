<?php

namespace App\Data\Translation;

class TranslationFile
{
    private ?string $fileId = null;
    private ?string $languageId = null;
    private ?string $language = null;
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

    public function getFileId() : ?string
    {
        return $this->fileId;
    }

    public function setFileId(?string $fileId) : void
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

    public function toArray() : array
    {
        return [
            'filename' => $this->filename,
            'content' => $this->content,
            'file_id' => $this->fileId,
            'language_id' => $this->languageId,
            'language' => $this->language,
        ];
    }

    static public function fromArray(array $array) : self
    {
        $instance = new self($array['filename'], $array['content']);
        $instance->setFileId($array['file_id']);
        $instance->setLanguageId($array['language_id']);
        $instance->setLanguage($array['language']);

        return $instance;
    }

    public function __toString() : string
    {
        return $this->filename;
    }
}