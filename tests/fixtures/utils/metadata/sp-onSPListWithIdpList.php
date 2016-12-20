<?php 

return [
    'sp-onSPListWithIdpList' => [
        'AssertionConsumerService' => 'http://sp-onSPList2/module.php/saml/sp/saml2-acs.php/ssp-hub',
        'SingleLogoutService' => 'http://sp-onSPList2/module.php/saml/sp/saml2-logout.php/ssp-hub',
        'IDPList' => ['idp-bare', 'idp-SPListExclude'],
    ],
];