<?php
namespace Sil\SspUtilsTests;

include __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Sil\SspUtils\Metadata;

class MetadataTest extends TestCase
{
    /**
     * Load metadata files from fixtures/metadata/set1
     */
    public function testGetMetadataFilesSet1()
    {
        $path = __DIR__ . '/fixtures/metadata/set1';
        $originalFiles = Metadata::getMetadataFiles($path, 'sp');

        /*
         * Since files have full path which changes based on where tests are run,
         * remove __DIR__ from file path to compare just files found without full path
         */
        $files = [];
        foreach ($originalFiles as $file) {
            $files[] = str_replace(__DIR__, '', $file);
        }

        $expected = [
            '/fixtures/metadata/set1/sp-one.php',
            '/fixtures/metadata/set1/sp-two.php',
            '/fixtures/metadata/set1/subDir/sp-three.php',
        ];

        $this->assertEquals($expected, $files);

        $originalFiles = Metadata::getMetadataFiles($path, 'idp');

        /*
         * Since files have full path which changes based on where tests are run,
         * remove __DIR__ from file path to compare just files found without full path
         */
        $files = [];
        foreach ($originalFiles as $file) {
            $files[] = str_replace(__DIR__, '', $file);
        }

        $expected = [
            '/fixtures/metadata/set1/idp-one.php',
            '/fixtures/metadata/set1/idp-two.php',
            '/fixtures/metadata/set1/subDir/idp-three.php',
        ];

        $this->assertEquals($expected, $files);
    }

    public function testGetSpMetadataEntriesSet1()
    {
        $path = __DIR__ . '/fixtures/metadata/set1';
        $entries = Metadata::getSpMetadataEntries($path);

        $expectedEntities = ['entity1', 'entity2', 'sub-entity'];

        $this->assertEquals($expectedEntities, array_keys($entries));
    }

    public function testGetIdpMetadataEntriesSet1()
    {
        $path = __DIR__ . '/fixtures/metadata/set1';
        $entries = Metadata::getIdpMetadataEntries($path);

        $expectedEntities = ['idp1', 'idp2', 'sub-idp'];

        $this->assertEquals($expectedEntities, array_keys($entries));
    }

    public function testGetSpMetadataSet2DuplicateException()
    {
        $path = __DIR__ . '/fixtures/metadata/set2';
        $this->expectException('Sil\SspUtils\InvalidMetadataFileException');
        $this->expectExceptionCode(1476733724);
        Metadata::getSpMetadataEntries($path);
    }

    public function testGetSpMetadataSet3NotArrayReturned()
    {
        $path = __DIR__ . '/fixtures/metadata/set3';
        $this->expectException('Sil\SspUtils\InvalidMetadataFileException');
        $this->expectExceptionCode(1476719480);
        Metadata::getSpMetadataEntries($path);
    }
}