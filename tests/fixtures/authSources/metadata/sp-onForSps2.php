<?php 

return [
    'sp-onForSps2' => [
        'AssertionConsumerService' => 'http://sp-onForSps2/module.php/saml/sp/saml2-acs.php/ssp-hub',
        'SingleLogoutService' => 'http://sp-onForSps2/module.php/saml/sp/saml2-logout.php/ssp-hub',
        'IDPList' => ['idp-bare', 'idp-forSpsExclude'],
    ],
];