<?php
namespace Sil\SspUtilsTests;

include __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Sil\SspUtils\Utils;
use Sil\SspUtils\DiscoUtils;
use Sil\SspUtils\Metadata;

class DiscoUtilsTest extends TestCase
{
    /*
     * The SP that does not have an IDPList entry so only get the Idp
     * that also doesn't have a list for SPS.
     */
    public function testGetReducedIdpList_SpBare()
    {
        $metadataPath = __DIR__ . '/fixtures/utils/metadata';
        $idpEntries = Metadata::getIdpMetadataEntries($metadataPath);

        $spEntityId = 'sp-bare';

        $expected = ['idp-bare' => $idpEntries['idp-bare']];

        $results = DiscoUtils::getReducedIdpList(
            $idpEntries,
            $metadataPath,
            $spEntityId
        );
        
        // echo PHP_EOL . "AAAAA" . PHP_EOL . var_export($results, true);
        
        $this->assertEquals($expected, $results);
    }

    /*
     * The SP that does have an IDPList entry so will get the Idps
     * that do not have Exclude by Default
     */
    public function testGetReducedIdpList_SpOnSPList()
    {
        $metadataPath = __DIR__ . '/fixtures/utils/metadata';
        $idpEntries = Metadata::getIdpMetadataEntries($metadataPath);

        $spEntityId = 'sp-onSPList';

        $expected = [
            'idp-bare' => $idpEntries['idp-bare'],
            'idp-SPList' => $idpEntries['idp-SPList']
        ];

        $results = DiscoUtils::getReducedIdpList(
            $idpEntries,
            $metadataPath,
            $spEntityId
        );

        // echo PHP_EOL . "AAAAA" . PHP_EOL . var_export($results, true);

        $this->assertEquals($expected, $results);
    }


    /*
     * The SP that does have an IDPList entry so will get the Idps
     * that do not have Exclude by Default
     */
    public function testGetIdpsForSp_SpOnSPList()
    {
        $metadataPath = __DIR__ . '/fixtures/utils/metadata';
        $idpEntries = Metadata::getIdpMetadataEntries($metadataPath);

        $spEntityId = 'sp-onSPList';

        $expected = [
            'idp-bare' => $idpEntries['idp-bare'],
            'idp-SPList' => $idpEntries['idp-SPList']
        ];

        $results = DiscoUtils::getIdpsForSp(
            $spEntityId,
            $metadataPath

        );

        // echo PHP_EOL . "AAAAA" . PHP_EOL . var_export($results, true);

        $this->assertEquals($expected, $results);
    }

    /*
     * The SP that does have an IDPList entry so will get the Idps
     * that do not have Exclude by Default
     */
    public function testListAllSpIdpLinksPlainText()
    {
        $metadataPath = __DIR__ . '/fixtures/utils/metadata';
        $allResults = DiscoUtils::listAllSpIdpLinks($metadataPath);
        $expected = [
            'sp-bare' => [
                    'idp-bare',
                ],
                'sp-onSPList' => [
                    'idp-SPList',
                    'idp-bare',
                ],
                'sp-onSPListWithIdpList' => [
                    'idp-SPListExclude',
                    'idp-bare',
                ],
                'sp-withIdpList' => [
                    'idp-exclude',
                ],
        ];

        $this->assertEquals($expected, $allResults["sps"]);

        $expected = [
            'idp-bare' => [
                'sp-bare',
                'sp-onSPList',
                'sp-onSPListWithIdpList',
            ],
            'idp-SPList' => [
                'sp-onSPList',
            ],
            'idp-SPListExclude' => [
                'sp-onSPListWithIdpList',
            ],
            'idp-exclude' => [
                'sp-withIdpList',
            ],
        ];

        $this->assertEquals($expected, $allResults["idps"]);

        $expected = PHP_EOL . "These IdP's are available";
        $results = substr($allResults["text"], 0, strlen($expected));
        $this->assertEquals($expected, $results);
    }

    /*
     * The SP that does have an IDPList entry so will get the Idps
     * that do not have Exclude by Default
     */
    public function testListAllSpIdpLinksPlainHtml()
    {
        $metadataPath = __DIR__ . '/fixtures/utils/metadata';
        $allResults = DiscoUtils::listAllSpIdpLinks($metadataPath, "html");
        $expected = [
            'sp-bare' => [
                'idp-bare',
            ],
            'sp-onSPList' => [
                'idp-SPList',
                'idp-bare',
            ],
            'sp-onSPListWithIdpList' => [
                'idp-SPListExclude',
                'idp-bare',
            ],
            'sp-withIdpList' => [
                'idp-exclude',
            ],
        ];

        $this->assertEquals($expected, $allResults["sps"]);

        $expected = [
            'idp-bare' => [
                'sp-bare',
                'sp-onSPList',
                'sp-onSPListWithIdpList',
            ],
            'idp-SPList' => [
                'sp-onSPList',
            ],
            'idp-SPListExclude' => [
                'sp-onSPListWithIdpList',
            ],
            'idp-exclude' => [
                'sp-withIdpList',
            ],
        ];

        $this->assertEquals($expected, $allResults["idps"]);

        $expected = PHP_EOL . '<style type="text/css">';
        $results = substr($allResults["text"], 0, strlen($expected));
        $this->assertEquals($expected, $results);
    }

}