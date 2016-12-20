<?php

return [
    'idp-SPList' => [
        'SingleSignOnService'  => 'http://idp-SPList/saml2/idp/SSOService.php',
        'excludeByDefault' => False,
        'SPList' => ['sp-onSPList', 'sp-onSPListWithIdpList'],
        'logoURL' => 'http://idp-SPList-logo.png',
    ],
];