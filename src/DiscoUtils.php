<?php

namespace Sil\SspUtils;

include __DIR__ . '/../vendor/autoload.php';

use Sil\SspUtils\Metadata;
use Sil\SspUtils\Utils;


/**
 * Class DiscoUtils
 *
 * Used for the IdP Discovery page
 *
 * @package Sil\SspUtils
 */
class DiscoUtils
{

        
    /**
     *
     * Takes the original IDPList and reduces it down to the ones
     * the current SP is meant to see.
     *    The relevant entries in saml20-idp-remote.php would be ...
     *      - 'excludeByDefault' (boolean), which when set to True would keep this idp
     *        from being shown to SP's that don't explicitly include it in the
     *        'IDPList' entry of their metadata.
     *      - 'forSps' (array), which when set would only allow this idp to be shown
     *        to SPs whose entity_id is included in this array.
     *
     * @param array $startIdps - with entityid => metadata mappings
     * @param string $metadataPath - the path to the sqml20-idp-remote.php files.
     * @param string $spEntityId - the current SP's entity id
     *
     * @return array of a subset of the original $startSources.
     */
    public static function getReducedIdpList(
        $startIdps, 
        $metadataPath,
        $spEntityId
    ) {

        $spEntries = Metadata::getSpMetadataEntries($metadataPath);  
        $spMetadata = $spEntries[$spEntityId];
        
        $reducedIdps = [];
        
        $idps4Sp = [];  // The list of IDP's this SP wants to know about
        if (array_key_exists(Utils::IDP_LIST_KEY, $spMetadata)) {
            $idps4Sp = $spMetadata[Utils::IDP_LIST_KEY];
        }   

        foreach ($startIdps as $idpEntityId => $idpMdEntry) {   
            if (Utils::isIdpValidForSp($idpEntityId,
                                       $idpMdEntry,
                                       $spEntityId,
                                       $idps4Sp)) {
                $reducedIdps[$idpEntityId] = $idpMdEntry;
            }
        }
        
        return $reducedIdps;        
    }


    /**
     * Takes the original idp entries and reduces them down to the ones
     * the current SP is meant to see.
     *
     * @param string $spEntityId
     * @param string $metadataPath, the path to the simplesamlphp/metadata folder
     *
     * @returns array of strings of entity id's of IDP's that this SP
     *     is allowed to use for authentication.
     */
    public static function getIdpsForSp(
        $spEntityId,
        $metadataPath
    ) {
        $idpEntries = Metadata::getIdpMetadataEntries($metadataPath);

        return self::getReducedIdpList(
            $idpEntries,
            $metadataPath,
            $spEntityId);
    }
}