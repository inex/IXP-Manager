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

    'ixfid'       => "ixf#ixp-001",
    'orgname'     => "SCIX",
    'name'        => "SCIX Operations",
    'email'       => "operations@scix.com",
    'testemail'   => "barryo@scix.com",

    'support_email'       => "operations@scix.com",
    'support_phone'       => "+353 1 531 1234",
    'support_hours'       => "24/7",

    'billing_email'       => "accounts@scix.com",
    'billing_phone'       => "+353 1 433 1234",
    'billing_hours'       => "8/5",


    'autobot'     => [
            'name'           => "SCIX Ops Autobot",
            'email'          => "ops-auto@scix.com"
        ],

    'mailer'      => [
        'name'               => "SCIX Autobot",
        'email'              => "do-not-reply@scix.com"
    ],

    'sitename'      => "SCIX IXP Manager",
    'corporate_url' => "https://www.scix.com/",
    'url'           => "https://www.scix.com/ixp/",
    'logo'          => "http://blah/public/images/inex-logo-150x73.jpg",
    'biglogo'       => "https://www.scix.come/ixp/images/inex-logo-600x165.gif",

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
    'switch_domain' => ".scix.com"

];
