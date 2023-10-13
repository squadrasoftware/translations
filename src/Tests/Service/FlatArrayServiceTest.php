<?php

namespace App\Tests\Service;

use App\Service\FlatArrayService;
use App\Service\LocalService;
use App\Tests\BaseTestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Yaml\Yaml;

class FlatArrayServiceTest extends BaseTestCase
{
    public function testFlatten()
    {
        $original = [
            'a' => 'b',
            'c' => [
                'd' => 'e',
                'f' => 'g',
            ],
        ];

        $flatten = FlatArrayService::flatten($original);

        $this->assertEquals([
            'a' => 'b',
            'c.d' => 'e',
            'c.f' => 'g',
        ], $flatten);
    }

    public function testUnflatten()
    {
        $array = [
            'a' => 'b',
            'c.d' => 'e',
            'c.f' => 'g',
        ];

        $unflatten = FlatArrayService::unflatten($array);

        $this->assertEquals([
            'a' => 'b',
            'c' => [
                'd' => 'e',
                'f' => 'g',
            ],
        ], $unflatten);
    }

    public function testFlipFlop()
    {
        // Given
        $config = $this->createConfig();
        $local = new LocalService($config);
        $files = $local->getLocalTranslations(new NullOutput());

        // When
        $flip = Yaml::parse($files->getFile('messages.en.yaml')->getContent());
        $flatten = FlatArrayService::flatten($flip);
        $flop = FlatArrayService::unflatten($flatten);

        // Then
        $this->assertEquals($flip, $flop);
    }
}