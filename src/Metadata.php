<?php
namespace Sil\SspUtils;

class Metadata
{
    /**
     * @param string $path Path to directory containing metadata files
     * @param string $prefix Prefix to decide which type to load, either 'sp' or 'idp'
     * @return array Array of full paths to files for type of metadata defined by prefix
     */
    public static function getMetadataFiles($path, $prefix)
    {
        $files = [];
        $allFiles = scandir($path);
        foreach ($allFiles as $file) {
            /*
             * If $file is a directory (and not . or ..), recursively crawl it for more metadata files
             */
            if ( ! in_array($file, ['.','..']) && is_dir($path . '/' . $file)) {
                $files = array_merge(
                    $files,
                    self::getMetadataFiles($path . '/' . $file, $prefix)
                );
            } elseif (preg_match('/^'.$prefix.'-.*\.php$/', $file)) {
                /*
                 * If $file matches expected pattern for metadata files, add to array
                 */
                $files[] = $path . '/' . $file;
            }
        }

        return $files;
    }

    /**
     * @param string $path Path to directory containing metadata files
     * @param string $prefix Prefix to decide which type to load, either 'sp' or 'idp'
     * @return array Associative array of key = entityId, value = array of metadata configuration
     * @throws \Exception
     */
    public static function getMetadataEntries($path, $prefix)
    {
        $entries = [];

        try {
            $files = self::getMetadataFiles($path, $prefix);
            foreach ($files as $file) {
                $fileEntries = include $file;
                if (is_array($fileEntries)) {
                    foreach ($fileEntries as $id => $value) {
                        /*
                         * Check for duplicate and throw exception
                         */
                        if ( array_key_exists($id, $entries)){
                            throw new InvalidMetadataFileException(
                                'Metadata file includes duplicate entity. File: ' . $file .'. For entity: ' . $id,
                                1476733724
                            );
                        }

                        $entries[$id] = $value;
                    }
                } else {
                    throw new InvalidMetadataFileException(
                        'Metadata file does not return an array as expected. File: ' . $file,
                        1476719480
                    );
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $entries;
    }

    /**
     * @param string $path Path to directory containing metadata files
     * @return array Associative array of key = entityId, value = array of metadata configuration
     * @throws \Exception
     */
    public static function getSpMetadataEntries($path)
    {
        return self::getMetadataEntries($path, 'sp');
    }

    /**
     * @param string $path Path to directory containing metadata files
     * @return array Associative array of key = entityId, value = array of metadata configuration
     * @throws \Exception
     */
    public static function getIdpMetadataEntries($path)
    {
        return self::getMetadataEntries($path, 'idp');
    }


}