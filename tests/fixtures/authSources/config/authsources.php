<?php

    $config = [
        'auth-choices' => [
            'multiauth:MultiAuth',

            'sources' => [
                'idp-bare', 'idp-exclude', 'idp-forSps', 'idp-forSpsExclude'
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
        'idp-forSps' =>  [
            'saml:SP',
            'entityID' => 'ssp-hub',
            'idp' => 'idp-forSps',
            'discoURL'  => NULL,
        ],      
        'idp-forSpsExclude' =>  [
            'saml:SP',
            'entityID' => 'ssp-hub',
            'idp' => 'idp-forSpsExclude',
            'discoURL'  => NULL,
        ],                
    ];