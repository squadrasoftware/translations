<?php

namespace App\Tests;

use App\Data\Configuration\Configuration;
use App\Format\FormatResolver;
use App\Format\JsonFormat;
use App\Format\YamlFormat;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected function createConfig() : Configuration
    {
        $config = Configuration::create(
            [
                'api_token' => 'foo',
                'projects' => [
                    'test' => [
                        'project_id' => '42',
                        'path' => 'src/Tests/Fixtures',
                        'pattern' => 'messages.*.yaml',
                        'format' => 'yaml',
                    ],
                ],
            ]
        );

        $config->setCurrentProject('test');

        return $config;
    }

    protected function createFormatResolver() : FormatResolver
    {
        return (new FormatResolver())
            ->addFormat(new JsonFormat())
            ->addFormat(new YamlFormat());
    }
}