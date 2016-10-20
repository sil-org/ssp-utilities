<?php
namespace Sil\SspUtilsTests;

include __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Sil\SspUtils\AuthSourcesUtils;
use Sil\SspUtils\Metadata;

class AuthSourcesUtilsTest extends TestCase
{
    public static $authSourcesConfig = [
            'auth-choices' => [
                'multiauth:MultiAuth',

                'sources' => [
                    'admin',
                    'idp-bare', 
                    'idp-exclude', 
                    'idp-forSps', 
                    'idp-forSpsExclude', 
                ],
            ], 

            'admin' => [
                'core:AdminPassword',
            ],
            'idp-bare' =>  [
                'saml:SP',
                'entityID' => 'ssp-hub',
                'idp' => 'idp-bare',
                'discoURL'  => NULL,
            ], 
            'idp-exclude' =>  [
                'saml:SP',
                'entityID' => 'ssp-hub',
                'idp' => 'idp-exclude',
                'discoURL'  => NULL,
            ], 
            'idp-forSps' =>  [
                'saml:SP',
                'entityID' => 'ssp-hub',
                'idp' => 'idp-forSps',
                'discoURL'  => NULL,
            ],      
            'idp-forSpsExclude' =>  [
                'saml:SP',
                'entityID' => 'ssp-hub',
                'idp' => 'idp-forSpsExclude',
                'discoURL'  => NULL,
            ],             
        ];
        
    public static function getTestSources()
    {
        $oldSources = self::$authSourcesConfig;
        unset($oldSources['auth-choices']);
        unset($oldSources['admin']);
        
        $newSources = [];
        foreach ($oldSources as $label => $nextSource) {
            $addEntry = [
                'source' => $label,
                'details' => $nextSource, // This is just for padding in the tests
            ];
            $newSources[] = $addEntry;
        }
        
        return $newSources;
    }
        
    public function testGetIdpsFromAuthSources()
    {
        $expected = [
            'idp-bare' => 'idp-bare',
            'idp-exclude' => 'idp-exclude',
            'idp-forSps' => 'idp-forSps',
            'idp-forSpsExclude' => 'idp-forSpsExclude',
        ];

        $results = AuthSourcesUtils::getIdpsFromAuthSources(self::$authSourcesConfig);
        $this->assertEquals($expected, $results);
    }
    
    /*
     * The SP that does not have an IDPList entry and is not included in any
     * of the IDPs' forSps entry.  It should only see the IDP that does not
     * have excludeByDefault => True and does not have a forSps entry.  
     */
    public function testGetSourcesSpBare()
    {    
        $metadataPath = __DIR__ . '/fixtures/metadata/set4_authSources';
        $spEntries = Metadata::getSpMetadataEntries($metadataPath);
        
        $startSources = self::getTestSources();
        $spEntityId = 'sp-bare';
        $spMetadata = $spEntries[$spEntityId];
        
        $expected = [
            [
                'source' => 'idp-bare',
                'details' => self::$authSourcesConfig['idp-bare'],
            ],
        ];
        
        $results = AuthSourcesUtils::getSources(
            self::$authSourcesConfig, 
            $startSources, 
            $spEntityId,
            $spMetadata, 
            $metadataPath
        );
        
        // echo PHP_EOL . "AAAAA" . PHP_EOL . var_export($results, true);
        
        $this->assertEquals($expected, $results);
    }
    
    /*
     * The SP that does not have an IDPList entry but is included in 
     * the IDPs' forSps entry.  It should see all the IDPs except the two that
     * have excludeByDefault => True.  
     */    
    public function testGetSourcesSpOnForSps()
    {    
        $metadataPath = __DIR__ . '/fixtures/metadata/set4_authSources';
        $spEntries = Metadata::getSpMetadataEntries($metadataPath);
        
        $startSources = self::getTestSources();
        $spEntityId = 'sp-onForSps';
        $spMetadata = $spEntries[$spEntityId];
        
        $expected = [
            [
                'source' => 'idp-bare',
                'details' => self::$authSourcesConfig['idp-bare'],
            ],
            [
                'source' => 'idp-forSps',
                'details' => self::$authSourcesConfig['idp-forSps'],
            ],
        ];
        
        $results = AuthSourcesUtils::getSources(
            self::$authSourcesConfig, 
            $startSources, 
            $spEntityId,
            $spMetadata, 
            $metadataPath
        );
        
        $this->assertEquals($expected, $results);
    }
    
    
    /*
     * The SP that has an IDPList entry and is included in the IDPs' 
     * forSps entry.  It should see all the IDPs that are in its IDPList.
     *   
     */      
    public function testGetSourcesSpOnForSps2()
    {    
        $metadataPath = __DIR__ . '/fixtures/metadata/set4_authSources';
        $spEntries = Metadata::getSpMetadataEntries($metadataPath);
        
        $startSources = self::getTestSources();
        $spEntityId = 'sp-onForSps2';
        $spMetadata = $spEntries[$spEntityId];
        
        $expected = [
            [
                'source' => 'idp-bare',
                'details' => self::$authSourcesConfig['idp-bare'],
            ],
            [
                'source' => 'idp-forSpsExclude',
                'details' => self::$authSourcesConfig['idp-forSpsExclude'],
            ],
        ];
        
        $results = AuthSourcesUtils::getSources(
            self::$authSourcesConfig, 
            $startSources, 
            $spEntityId,
            $spMetadata, 
            $metadataPath
        );
        
        $this->assertEquals($expected, $results);
    }
    
    /*
     * The SP that has an IDPList entry but is not included in the IDPs' 
     * forSps entry.  It should see the IDPs that are in its IDPList,
     * except for the ones that have a forSps entry.
     *   
     */      
    public function testGetSourcesSpWithIdpList()
    {    
        $metadataPath = __DIR__ . '/fixtures/metadata/set4_authSources';
        $spEntries = Metadata::getSpMetadataEntries($metadataPath);
        
        $startSources = self::getTestSources();
        $spEntityId = 'sp-withIdpList';
        $spMetadata = $spEntries[$spEntityId];
        
        $expected = [
            [
                'source' => 'idp-exclude',
                'details' => self::$authSourcesConfig['idp-exclude'],
            ],
        ];
        
        $results = AuthSourcesUtils::getSources(
            self::$authSourcesConfig, 
            $startSources, 
            $spEntityId,
            $spMetadata, 
            $metadataPath
        );
        
        $this->assertEquals($expected, $results);
    }    
    

    /**
     * Good config
     */
    public function testGetAuthSourcesConfig()
    {
        $path = __DIR__ . '/fixtures/config';

        $expected = [
            'auth-choices' => [
                'multiauth:MultiAuth',

                'sources' => [
                    'idp-bare', 
                ],
            ], 

            'idp-bare' =>  [
                'saml:SP',
                'entityID' => 'ssp-hub',
                'idp' => 'idp-bare',
                'discoURL'  => NULL,
            ],            
        ];

        $results = AuthSourcesUtils::getAuthSourcesConfig($path);
        $this->assertEquals($expected, $results);
    }
        
    /**
     * Ensure exception is thrown for empty config
     */
    public function testGetAuthSourcesConfigBad()
    {
        $path = __DIR__ . '/fixtures/config';
        $fileName = 'authsourcesBad.php';

        $expected = ['auth-choices'];      

        $this->expectException('Sil\SspUtils\InvalidAuthSourcesException');
        $this->expectExceptionCode(1476966993);
        
        $results = AuthSourcesUtils::getAuthSourcesConfig($path, $fileName);
        $this->assertEquals($expected, $results);
    }    
    
    
    /*
     * 
     * 
     */
    public function testAddIdpLogoUrls()
    {    
        $metadataPath = __DIR__ . '/fixtures/metadata/set4_authSources';
        $sources = self::getTestSources();
        
        $spEntries = Metadata::getSpMetadataEntries($metadataPath);
        
        AuthSourcesUtils::addIdpLogoUrls(
            $sources,
            self::$authSourcesConfig, 
            $metadataPath
        );
        
        $expected = [
            ['idp-bare'],
            ['idp-exclude'],
            ['idp-forSps', 'http://idp-forSps-logo.png'],
            ['idp-forSpsExclude'],
        ];
        
        $results = [];
        foreach ($sources as $nextSource) {
            $newEntry = [$nextSource['source']];
            if (isset($nextSource[AuthSourcesUtils::IDP_LOGO_KEY])) {
                $newEntry[] = $nextSource[AuthSourcesUtils::IDP_LOGO_KEY];
            }
            $results[] = $newEntry;
        }

        $this->assertEquals($expected, $results);
    }
    
}