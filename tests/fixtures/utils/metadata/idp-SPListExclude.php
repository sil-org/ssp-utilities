<?php

return [
    'idp-SPListExclude' => [
        'SingleSignOnService'  => 'http://idp-SPListExclude/saml2/idp/SSOService.php',
        'excludeByDefault' => True,
        'SPList' => ['sp-onSPList', 'sp-onSPListWithIdpList'],
        'logoURL' => ['no arrays allowed'],
    ],
];