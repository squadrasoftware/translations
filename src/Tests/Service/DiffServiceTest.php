<?php

namespace App\Tests\Service;

use App\Data\Translation\TranslationFile;
use App\Data\Translation\Translations;
use App\Service\DiffService;
use App\Service\LocalService;
use App\Tests\BaseTestCase;
use Symfony\Component\Console\Output\NullOutput;

class DiffServiceTest extends BaseTestCase
{
    public function testComputeFiles()
    {
        // Given
        $service = new DiffService(
            $this->createConfig(),
            $this->createFormatResolver()
        );

        $local = new Translations();
        $local->addFile(new TranslationFile('foo', 'hello: world'));
        $local->addFile(new TranslationFile('bar', 'hello: world'));

        $remote = new Translations();
        $remote->addFile(new TranslationFile('bar', 'hello: world'));
        $remote->addFile(new TranslationFile('qux', 'hello: world'));

        // When
        $diff = $service->getCatalog($local, $remote);

        // Then
        $this->assertEquals([$remote->getFile('qux')], $diff->getExtraFiles());
        $this->assertEquals([$local->getFile('foo')], $diff->getMissingFiles());
    }

    public function testCatalog()
    {
        // Given
        $config = $this->createConfig();
        $local = (new LocalService($config))->getLocalTranslations(new NullOutput());
        $remote = clone $local;

        // When
        $service = new DiffService(
            $this->createConfig(),
            $this->createFormatResolver()
        );
        $diff = $service->getCatalog($local, $remote);

        // Then
        $this->assertNotNull($diff->getCatalog());
    }
}