<?php

namespace Sil\SspUtils;

include __DIR__ . '/../vendor/autoload.php';

class AuthSourcesConfig
{
  
    /**
     * @param string $path Path to directory containing metadata files
     */
    public static function getAuthSourcesConfig($path, $file="authsources.php")
    {  
        $pathFile = $path . '/' . $file;
        
        try {
            $authSourcesContents = include $pathFile;
            
            if ($config === Null or ! $config) {
                throw new InvalidAuthSourcesException(
                    'Invalid authsources config for ' . $pathFile . PHP_EOL . 
                        'Cannot be null or evaluate to false.',
                    1476966993
                );
            }
            
        } catch (\Exception $e) {
            throw $e;
        }
        
        return $config;
        
    }
}