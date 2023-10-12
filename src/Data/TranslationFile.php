<?php

namespace App\Data;

class TranslationFile
{
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

    public function toArray(): array
    {
        return [
            'filename' => $this->filename,
            'content' => $this->content,
        ];
    }

    static public function fromArray(array $array): self
    {
        return new self($array['filename'], $array['content']);
    }
}