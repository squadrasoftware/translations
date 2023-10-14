<?php

namespace App\Data\Configuration;

class Configuration
{
    private array $projects = [];
    private ?Project $currentProject = null;

    public static function create(array $configuration) : self
    {
        $instance = new self;

        foreach ($configuration['projects'] as $projectName => $projectConfiguration) {
            $instance->projects[$projectName] = Project::create($projectConfiguration);
        }

        return $instance;
    }

    public function getProjects() : array
    {
        return $this->projects;
    }

    public function getCurrentProject() : ?Project
    {
        return $this->currentProject;
    }

    public function setCurrentProject(?string $name) : ?Project
    {
        if ($name) {
            $project = $this->getProject($name);

            $this->currentProject = $project;
        } else {
            $this->currentProject = null;
        }

        return $project;
    }

    public function getProject(string $name) : ?Project
    {
        return $this->projects[$name] ?? null;
    }
}