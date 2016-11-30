<?php

return [
    'idp-forSps' => [
        'SingleSignOnService'  => 'http://idp-forSps/saml2/idp/SSOService.php',
        'excludeByDefault' => False,
        'forSps' => ['sp-onForSps', 'sp-onForSpsWithIdpList'],
        'logoURL' => 'http://idp-forSps-logo.png',
    ],
];