<?php
namespace Sil\SspUtilsTests;

include __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Sil\SspUtils\AuthStateUtils;

class AuthStateUtilsTest extends TestCase
{
    /**
     * Good config
     */
    public function testGetSpEntityIdForMultiAuth()
    {
        $authState = '_5ee8efaf7292af:http://ssp-hub/saml2/idp/SSOService.php?spentityid=http%253A%252F%252Fssp-hub-sp2&cookieTime=1476968808&RelayState=http%253A%252F%252Fssp-hub-sp2%253A8080%252Fmodule.php%252Fcore%252Fauthenticate.php%253Fas%253Dssp-hub';
        
        $expected = 'http://ssp-hub-sp2';
        $results = AuthStateUtils::getSpEntityIdForMultiAuth($authState);
        $this->assertEquals($expected, $results);
    }
        
}