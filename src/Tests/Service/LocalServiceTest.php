<?php

namespace App\Tests\Service;

use App\Service\LocalService;
use App\Tests\BaseTestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class LocalServiceTest extends BaseTestCase
{
    public function testGetLocalTranslations()
    {
        // Given
        $config = $this->createConfig();
        $service = new LocalService($config);

        // When
        $translations = $service->getLocalTranslations(new BufferedOutput());

        // Then
        $this->assertCount(2, $translations->getFiles());
        $this->assertStringContainsString('This is a translation sample', $translations->getFile('messages.en.yaml')->getContent());
        $this->assertStringContainsString('Ceci est un exemple de traduction', $translations->getFile('messages.fr.yaml')->getContent());
    }
}