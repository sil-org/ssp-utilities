<?php

    $config = [
        'auth-choices' => [
            'multiauth:MultiAuth',

            'sources' => [
                'idp-bare', 'idp-exclude', 'idp-SPList', 'idp-SPListExclude'
            ],
        ], 

        'idp-bare' =>  [
            'saml:SP',
            'entityID' => 'ssp-hub',
            'idp' => 'idp-bare',
            'discoURL'  => NULL,
        ],     
        'idp-exclude' =>  [
            'saml:SP',
            'entityID' => 'ssp-hub',
            'idp' => 'idp-exclude',
            'discoURL'  => NULL,
        ], 
        'idp-SPList' =>  [
            'saml:SP',
            'entityID' => 'ssp-hub',
            'idp' => 'idp-SPList',
            'discoURL'  => NULL,
        ],      
        'idp-SPListExclude' =>  [
            'saml:SP',
            'entityID' => 'ssp-hub',
            'idp' => 'idp-SPListExclude',
            'discoURL'  => NULL,
        ],                
    ];