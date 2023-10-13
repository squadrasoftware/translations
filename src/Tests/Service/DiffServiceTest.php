<?php

namespace App\Tests\Service;

use App\Data\Translation\TranslationFile;
use App\Data\Translation\Translations;
use App\Service\DiffService;
use PHPUnit\Framework\TestCase;

class DiffServiceTest extends TestCase
{
    public function testComputeFiles()
    {
        // Given
        $service = new DiffService();

        $local = new Translations();
        $local->addFile(new TranslationFile('foo', ''));
        $local->addFile(new TranslationFile('bar', ''));

        $remote = new Translations();
        $remote->addFile(new TranslationFile('bar', ''));
        $remote->addFile(new TranslationFile('qux', ''));

        // When
        $diff = $service->compute($local, $remote);

        // Then
        $this->assertEquals([$remote->getFile('qux')], $diff->getExtraFiles());
        $this->assertEquals([$local->getFile('foo')], $diff->getMissingFiles());
    }

}