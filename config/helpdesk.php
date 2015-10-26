<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Helpdesk Backend
    |--------------------------------------------------------------------------
    |
    |
    */

    'backend' => 'zendesk',   // default: none

    /*
    |--------------------------------------------------------------------------
    | Helpdesk Backends
    |--------------------------------------------------------------------------
    |
    */

    'backends' => [

        'zendesk' => [
            'subdomain' => env('HELPDESK_ZENDESK_SUBDOMAIN', 'xxx'),
            'token'     => env('HELPDESK_ZENDESK_TOKEN',     'xxx'),
            'email'     => env('HELPDESK_ZENDESK_EMAIL',     'xxx')
        ],

    ],

];
