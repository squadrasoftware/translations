<?php

namespace App\Data\Configuration;

class Project
{
    private string $id;
    private string $path;
    private string $pattern;
    private string $format;

    static public function create(array $projectConfiguration): self
    {
        $instance = new self;

        $instance->id = $projectConfiguration['project_id'];
        $instance->path = $projectConfiguration['path'];
        $instance->pattern = $projectConfiguration['pattern'];
        $instance->format = $projectConfiguration['format'];

        return $instance;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPattern() : string
    {
        return $this->pattern;
    }

    public function getFormat() : string
    {
        return $this->format;
    }
}