# ssp-utilities
SimpleSAMLphp related utility classes

## Overview

This project includes utilities that are used by [ssp-base](https://github.com/sil-org/ssp-base)

It provides business logic that determines which simplesamlphp Identity Providers can be used for authentication by a certain SP.

It also provides a utility that will gather together remote metadata from a folder and its sub-folders to be used by a saml20-*-remote.php file.

It includes unit tests that can be run from the /data folder via $ vendor/phpunit/phpunit/phpunit tests. These should provide insight into what the utilities do in practice.

## Editing IdP Business Logic

There are several ways to limit which **IdP's** can be used for authentication by a certain **SP**.  (These are provided by `Utils.php::isIdpValidForSp` which is called by the `DiscoUtils.php::getIdpsForSp` method.)

1. If an **IDP's** entry in the `saml20-idp-remote.php` file includes a `'SPList'` entry (as an array), then only the **SP's** which have an **entity id** listed in that array will be permissible.

2. If an **IDP's** entry in the `saml20-idp-remote.php` file includes an `'excludeByDefault'` entry set to `True`, then only the **SP's** which include the **IdP's entity id** in their `'IDPList'` entry will be permissible.

3. If an **SP's** entry in the `saml20-sp-remote.php` file includes an `'IDPList'` entry (as an array), then only the **IdP's** which have an **entity id** listed in that array will be permissible.

## Metadata Utilities

The metadata.php file includes utilities that pull in metadata from all the files named idp-*.php and sp-*.php respectively, including those in sub-folders.
