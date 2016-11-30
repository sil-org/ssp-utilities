<?php

namespace Sil\SspUtils;

include __DIR__ . '/../vendor/autoload.php';



class Utils
{
    const EXCLUDE_KEY = 'excludeByDefault'; // Entry in an IDP's metadata

    const FOR_SPS_KEY = 'forSps';  // Entry in an IDP's metadata for SP exclusive whitelist

    const SSP_PATH_ENV = 'SSP_PATH'; // Environment variable for the path to the simplesamlphp code

    /**
     * If the parameter is Null, tries to get the SSP_PATH environment variable.
     *
     * @param string $sspPath
     * @return string - either the input value, or if that is null,
     * the SSP_PATH environment variable
     * @throws InvalidSspPathException if the resulting path value is null or falsey
     **/
    public static function getSspPath($sspPath)
    {

        if ($sspPath === Null) {
            $sspPath = getenv(self::SSP_PATH_ENV);
        }

        if (!$sspPath) {
            throw new InvalidSspPathException(
                'Invalid path for simplesamlphp.' . PHP_EOL .
                'Cannot be null or evaluate to false.',
                1476967000
            );
        }

        return $sspPath;
    }

    /**
     * Determins whether an IdP should be usable by a certain SP
     *
     * @param string $idpEntityId
     * @param array $idpMdEntry - The metadata entry for the IdP from saml20-idp-remote.php
     * @param string $spEntityId
     * @param array $idps4Sp -  list of IdP entity id's that this SP wants to use
     * @return bool
     */
    public static function isIdpValidForSp(
        $idpEntityId,
        $idpMdEntry,
        $spEntityId,
        $idps4Sp
    ) {
        $forSpsList = Null;

        if (isset($idpMdEntry[self::FOR_SPS_KEY])) {
            $forSpsList = $idpMdEntry[self::FOR_SPS_KEY];
        }

        // If there is an exclusive white list for this IDP, but this SP
        // is not included, then not valid
        if ($forSpsList !== Null && ! in_array($spEntityId, $forSpsList)) {
            return False;
        }

        $excludeByDefault = False;
        if (isset($idpMdEntry[self::EXCLUDE_KEY]) &&
            $idpMdEntry[self::EXCLUDE_KEY] === True) {
            $excludeByDefault = True;
        }

        // If the SP does not expect to know about certain IDP's and
        // this idp does not want to be seen without an explicit request, skip it.
        if ( ! $idps4Sp && $excludeByDefault === True) {
            return False;
        }

        // If the SP only wants to know about certain IDP's and this one
        // is not one of those, skip it.
        if ($idps4Sp) {
            if ( ! in_array($idpEntityId, $idps4Sp)) {
                return False;
            }
        }

        // Everything is OK, so add this idp as one of the auth sources
        return True;
    }

}