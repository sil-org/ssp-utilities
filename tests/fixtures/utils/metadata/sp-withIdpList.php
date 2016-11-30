<?php 

return [
    'sp-withIdpList' => [
        'AssertionConsumerService' => 'http://sp-withIdpList/module.php/saml/sp/saml2-acs.php/ssp-hub',
        'SingleLogoutService' => 'http://sp-withIdpList/module.php/saml/sp/saml2-logout.php/ssp-hub',
        'IDPList' => ['idp-exclude', 'idp-forSps'],
    ],
];