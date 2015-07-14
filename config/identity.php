<?php

return [

    /*
    | IXP Identity Information
    */

    'legalname'   =>      "Some City Internet Exchange Ltd",

    'location'    => [
            'city'       => "Dublin",
            'country'    => "IE",
        ],

    'ixfid'       => "1111",		/* this refers to the ID in https://db.ix-f.net/api/ixp */
    'orgname'     => "SCIX",
    'name'        => "SCIX Operations",
    'email'       => "operations@example.org",
    'testemail'   => "barryo@example.org",

    'support_email'       => "operations@example.org",
    'support_phone'       => "+353 1 531 1234",
    'support_hours'       => "24/7",

    'billing_email'       => "accounts@example.org",
    'billing_phone'       => "+353 1 433 1234",
    'billing_hours'       => "8/5",


    'autobot'     => [
            'name'           => "SCIX Ops Autobot",
            'email'          => "ops-auto@example.org"
        ],

    'mailer'      => [
        'name'               => "SCIX Autobot",
        'email'              => "do-not-reply@example.org"
    ],

    'sitename'      => "SCIX IXP Manager",
    'corporate_url' => "https://www.example.org/",
    'url'           => "https://www.example.org/ixp/",
    'logo'          => "https://www.example.org/public/images/inex-logo-150x73.jpg",
    'biglogo'       => "https://www.example.org/ixp/images/inex-logo-600x165.gif",

    'biglogoconf' => [
            'offset'             => 'offset4'
        ],

    'misc'        => [
            'irc_password'       => "foobar"
        ],

    'vlans'       => [
            'default' => 2
        ],

    // appended to switch names in some places. If you use FQDNs for your switches in IXP Manager then leave blank.
    'switch_domain' => ".example.org"

];
