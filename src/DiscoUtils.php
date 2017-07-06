<?php

namespace Sil\SspUtils;

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
     *      - 'SPList' (array), which when set would only allow this idp to be shown
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

    /*
     * Returns a nested array of all the IdP's that are available to each SP
     *
     * @param string the metadata folder's path
     * @return array ["sp1" => ["idp1", ...], ...]]
     */
    public static function getSpIdpLinks($metadataPath) {
        $links = [];
        $spEntries = Metadata::getSpMetadataEntries($metadataPath);

        foreach ($spEntries as $spEntityId => $spEntry) {
            $idpList = DiscoUtils::getIdpsForSp(
                $spEntityId,
                $metadataPath
            );
            $links[$spEntityId] = array_keys($idpList);
        }
        return $links;
    }

    /*
     * Returns a nested array of all the SP's that are allowed to use each IdP
     * and all the IdP's that are available to each SP
     *
     * @param string the metadata folder's path
     * @param string optional (default '') ...
     *   - if empty, then no echoed output
     *   - if html, then html output with a style section
     *   - if other value, then plain text output
     *
     * @returns array  ["sps" => ["sp1" => ["idp1", ...], ...],
     *                 "idps" => ["idp1" => ["sp1", ...], ...],
     *                ]
     */
    public static function listAllSpIdpLinks($metadataPath, $outputStyle='') {
        $spLinks = self::getSpIdpLinks($metadataPath);
        $idpLinks = [];

        // for the IDP-based array, transpose the SP-based array
        foreach ($spLinks as $nextSp => $idps) {
            foreach ($idps as $nextIdp) {
                if ( ! isset($idpLinks[$nextIdp])) {
                    $idpLinks[$nextIdp] = [];
                }
                $idpLinks[$nextIdp][] = $nextSp;
            }
        }

        $allLinks = [];
        $allLinks["sps"] = $spLinks;
        $allLinks["idps"] = $idpLinks;

        // No echoed output requested, just return the results
        if ( ! $outputStyle) {
            return $allLinks;
        }

        // For plain text output, don't include html tags
        $cssStyle = '';
        $openDivLinks = '';
        $openDivEntry = '  ';
        $closeDiv = '';

        $openUl = '';
        $closeUl = '-----------';
        $li = '    ';

        // for html output include style section and html tags
        if ($outputStyle == "html") {
            $cssStyle = '
<style type="text/css">
  .idpSpLinks {
    margin-top: 20px;
    font-size: 20px;
    font-family: Arial;
  }
  
  .idpSpLinksEntry {
    margin-top: 10px;
    margin-left: 14px;
    font-size: 16px;
    font-family: Arial;
  }

  .idpSpLinksEntry>ul {
    margin: 2px;
    font-size: 14px;
    font-family: Arial;
  }  
</style>
';
            $openDivLinks = '<div class="idpSpLinks">';
            $openDivEntry = '<div class="idpSpLinksEntry">';
            $closeDiv = '</div>';

            $openUl = PHP_EOL . '  <ul>';
            $closeUl = '  </ul>';
            $li = '    <li>';
        }

        echo PHP_EOL . $cssStyle . PHP_EOL ;
        echo $openDivLinks .
            "These IdP's are available to the corresponding Sp's" .
            $closeDiv . PHP_EOL;

        foreach ($idpLinks as $idpEntityId => $spList) {
            echo $openDivEntry . "$idpEntityId is available to ..." .
                $openUl . PHP_EOL;
            foreach ($spList as $nextSp) {
                echo $li . $nextSp . PHP_EOL;
            }
            echo  $closeUl . PHP_EOL;
            echo $closeDiv . PHP_EOL;
        }

        echo PHP_EOL . $openDivLinks .
             "These SP's may use the corresponding IdP's" .
             $closeDiv . PHP_EOL;

        foreach ($spLinks as $spEntityId => $idpList) {
            echo $openDivEntry . "$spEntityId may use ... " .
                $openUl . PHP_EOL;
            foreach ($idpList as $nextIdp) {
                echo $li . $nextIdp . PHP_EOL;
            }
            echo  $closeUl . PHP_EOL;
            echo $closeDiv . PHP_EOL;
        }
        return $allLinks;
    }

}