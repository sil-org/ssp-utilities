<?php

return [
    'idp-forSpsExclude' => [
        'SingleSignOnService'  => 'http://idp-forSpsExclude/saml2/idp/SSOService.php',
        'excludeByDefault' => True,
        'forSps' => ['sp-onForSps', 'sp-onForSps2'],
    ],
];