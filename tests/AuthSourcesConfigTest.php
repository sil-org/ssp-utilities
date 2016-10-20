<?php
namespace Sil\SspUtilsTests;

include __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Sil\SspUtils\AuthSourcesConfig;

class AuthSourcesConfigTest extends TestCase
{
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

        $results = AuthSourcesConfig::getAuthSourcesConfig($path);
        $this->assertEquals($expected, $results);
    }
    
    
    /**
     * Ensure exception is thrown
     */
    public function testGetAuthSourcesConfigBad()
    {
        $path = __DIR__ . '/fixtures/config';
        $fileName = 'authsourcesBad.php';

        $expected = ['auth-choices'];      

        $this->expectException('Sil\SspUtils\InvalidAuthSourcesException');
        $this->expectExceptionCode(1476966993);
        
        $results = AuthSourcesConfig::getAuthSourcesConfig($path, $fileName);
        $this->assertEquals($expected, $results);
    }

}