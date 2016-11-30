<?php
namespace Sil\SspUtilsTests;

include __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Sil\SspUtils\AuthSourcesUtils;
use Sil\SspUtils\Metadata;
use Sil\SspUtils\Utils;

class AuthSourcesUtilsTest extends TestCase
{
    public static function getAuthState($spEntityId) {
        return '_5ee8efaf7292af:http://ssp-hub/saml2/idp/SSOService.php?spentityid=' .
          $spEntityId . '&cookieTime=1476968808&RelayState=' . $spEntityId . 
          '%253A8080%252Fmodule.php%252Fcore%252Fauthenticate.php%253Fas%253Dssp-hub';
    }
  
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
        
    public static function getTestSources($authSourcesConfig=Null)
    {
        if ($authSourcesConfig === Null) {
            $authSourcesConfig = self::$authSourcesConfig;
        }
        $oldSources = $authSourcesConfig;
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
        $metadataPath = __DIR__ . '/fixtures/authSources/metadata';
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
        $metadataPath = __DIR__ . '/fixtures/authSources/metadata';
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
        $metadataPath = __DIR__ . '/fixtures/authSources/metadata';
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
        $metadataPath = __DIR__ . '/fixtures/authSources/metadata';
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
        $path = __DIR__ . '/fixtures/authSources/config';

        $expected = [
            'auth-choices' => [
                'multiauth:MultiAuth',

                'sources' => [
                    'idp-bare', 'idp-exclude', 'idp-forSps', 'idp-forSpsExclude',
                ],
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

        $results = AuthSourcesUtils::getAuthSourcesConfig($path);
        $this->assertEquals($expected, $results);
    }
        
    /**
     * Ensure exception is thrown for empty config
     */
    public function testGetAuthSourcesConfigBad()
    {
        $path = __DIR__ . '/fixtures/authSources/config';
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
        $metadataPath = __DIR__ . '/fixtures/authSources/metadata';
        $sources = self::getTestSources();
        
        $spEntries = Metadata::getSpMetadataEntries($metadataPath);
        
        AuthSourcesUtils::addIdpLogoUrls(
            $sources,
            self::$authSourcesConfig, 
            $metadataPath
        );
        
        $expected = [
            ['idp-bare'],
            ['idp-exclude', "http://www.bd.com"],
            ['idp-forSps', 'http://idp-forSps-logo.png'],
            ['idp-forSpsExclude', false],
        ];
        
        $results = [];
        foreach ($sources as $nextSource) {
            $newEntry = [$nextSource['source']];
            if (isset($nextSource[Utils::IDP_LOGO_KEY])) {
                $newEntry[] = $nextSource[Utils::IDP_LOGO_KEY];
            }
            $results[] = $newEntry;
        }

        $this->assertEquals($expected, $results);
    }
    
    public function testGetSourcesForSp()
    {
        $sspPath = __DIR__ . '/fixtures/authSources';
        $spEntries = Metadata::getSpMetadataEntries($sspPath);
        
        $startSources = self::getTestSources();
        
        $spEntityId = 'sp-bare';
        $spMetadata = $spEntries[$spEntityId];
        
        $expected = [
            [
                'source' => 'idp-bare',
                'details' => self::$authSourcesConfig['idp-bare'],
            ],
        ];
        
        $results = AuthSourcesUtils::getSourcesForSp(
            $startSources, 
            self::getAuthState($spEntityId), 
            $sspPath
        );

        $this->assertEquals($expected, $results);
    }
    
    public function testGetSourcesWithLogoUrls()
    {
        $sspPath = __DIR__ . '/fixtures/authSources';
        $spEntries = Metadata::getSpMetadataEntries($sspPath);
        
        $authSourcesConfig = AuthSourcesUtils::getAuthSourcesConfig(
            $sspPath . '/config');
        $startSources = self::getTestSources($authSourcesConfig);
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
                'logoURL' => 'http://idp-forSps-logo.png'
            ],
        ];
        
        $authState = self::getAuthState($spEntityId);
        $results = AuthSourcesUtils::getSourcesWithLogoUrls(
            $startSources, 
            $authState, 
            $sspPath
        );      

        $this->assertEquals($expected, $results);
    }
    
    /*
     * The SP that does not have an IDPList entry and is not included in any
     * of the IDPs' forSps entry.  It should only see the IDP that does not
     * have excludeByDefault => True and does not have a forSps entry.  
     */
    public function testGetIdpsForSpNoAuthStateSPBare()
    {
        $sspPath = __DIR__ . '/fixtures/authSources';
        
        $spEntityId = 'sp-bare';
        $allSpMetadata = Metadata::getSpMetadataEntries($sspPath . '/metadata');
        $spMetadata = $allSpMetadata[$spEntityId];
        
        $authSourcesConfig = AuthSourcesUtils::getAuthSourcesConfig(
            $sspPath . '/config');

        $expected = ['idp-bare'];
        $results = AuthSourcesUtils::getIdpsForSpNoAuthState(
            $authSourcesConfig,
            $spEntityId,
            $spMetadata,
            $sspPath
        );

        $this->assertEquals($expected, $results);
    } 
    
    /*
     * The SP that does not have an IDPList entry but is included in 
     * the IDPs' forSps entry.  It should see all the IDPs except the two that
     * have excludeByDefault => True.  
     */ 
    public function testGetIdpsForSpNoAuthStateSpOnForSps()
    {
        $sspPath = __DIR__ . '/fixtures/authSources';
        
        $spEntityId = 'sp-onForSps';
        $allSpMetadata = Metadata::getSpMetadataEntries($sspPath . '/metadata');
        $spMetadata = $allSpMetadata[$spEntityId];
        
        $authSourcesConfig = AuthSourcesUtils::getAuthSourcesConfig(
            $sspPath . '/config');

        $expected = ['idp-bare', 'idp-forSps'];
        $results = AuthSourcesUtils::getIdpsForSpNoAuthState(
            $authSourcesConfig,
            $spEntityId,
            $spMetadata,
            $sspPath
        );

        $this->assertEquals($expected, $results);
    } 
    
    /*
     * The SP that has an IDPList entry but is not included in the IDPs' 
     * forSps entry.  It should see the IDPs that are in its IDPList,
     * except for the ones that have a forSps entry.
     *   
     */ 
    public function testGetIdpsForSpNoAuthStateSpWithIdpList()
    {
        $sspPath = __DIR__ . '/fixtures/authSources';
        
        $spEntityId = 'sp-withIdpList';
        $allSpMetadata = Metadata::getSpMetadataEntries($sspPath . '/metadata');
        $spMetadata = $allSpMetadata[$spEntityId];
                
        $authSourcesConfig = AuthSourcesUtils::getAuthSourcesConfig(
            $sspPath . '/config');

        $expected = ['idp-exclude'];
        $results = AuthSourcesUtils::getIdpsForSpNoAuthState(
            $authSourcesConfig,
            $spEntityId,
            $spMetadata,
            $sspPath
        );

        $this->assertEquals($expected, $results);
    } 
}