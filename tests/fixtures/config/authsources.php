<?php

    $config = [
        'auth-choices' => [
            'multiauth:MultiAuth',

            'sources' => [
                'idp-bare', 
            ],
        ], 

        'idp-bare' =>  [
            'saml:SP',
            'entityID' => 'ssp-hub',
            'idp' => 'idp-bare',
            'discoURL'  => NULL,
        ],            
    ];