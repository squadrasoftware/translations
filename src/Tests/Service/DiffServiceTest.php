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
        $local->addFile($this->getTranslationFile('foo', 'hello: world'));
        $local->addFile($this->getTranslationFile('bar', 'hello: world'));

        $remote = new Translations();
        $remote->addFile($this->getTranslationFile('bar', 'hello: world'));
        $remote->addFile($this->getTranslationFile('qux', 'hello: world'));

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
        $remote = $this->createRemote($local);

        // When
        $service = new DiffService(
            $this->createConfig(),
            $this->createFormatResolver()
        );
        $diff = $service->getCatalog($local, $remote);

        // Then
        $this->assertNotNull($diff->getCatalog());
    }

    private function getTranslationFile(string $filename, string $content) : TranslationFile
    {
        $file = new TranslationFile($filename, $content);
        $file->setFileId('42');
        $file->setLanguageId('en');
        $file->setLanguage('English');

        return $file;
    }

    private function createRemote(Translations $translations) : Translations
    {
        $remote = new Translations();

        foreach ($translations->getFiles() as $file) {
            $remote->addFile($this->getTranslationFile($file->getFilename(), $file->getContent()));
        }

        return $remote;
    }
}