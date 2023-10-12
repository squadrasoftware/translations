<?php

namespace App\Service;

use App\Data\Configuration;

class LocalService
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


}