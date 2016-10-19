<?php

return [
    'idp-forSps' => [
        'SingleSignOnService'  => 'http://idp-forSps/saml2/idp/SSOService.php',
        'excludeByDefault' => False,
        'forSps' => ['sp-onForSps', 'sp-onForSps2'],
    ],
];