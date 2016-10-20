<?php

namespace Sil\SspUtils;

include __DIR__ . '/../vendor/autoload.php';

class AuthStateUtils
{
  
    /**
     * Gets the SP entityID out of the AuthState parameter
     *
     * @param string $authState 
     */
    public static function getSpEntityIdForMultiAuth($authState)
    {  
        //
        $idParam = '?spentityid';

        // remove the text up to the start of the parameter key
        $paramStart = strpos($authState, $idParam);
        $entityId = substr($authState, $paramStart);

        // remove the parameter key from the start
        $valueStart = strpos($entityId, '=') + 1;
        $entityId = substr($entityId, $valueStart);

        // remove the text after the end of the parameter value
        $valueEnd = strpos($entityId, '&');
        $entityId = substr($entityId, 0, $valueEnd);
        $entityId = urldecode(urldecode($entityId));   
        
        return $entityId;
        
    }
}