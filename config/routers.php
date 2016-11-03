<?php

return [
    // route servers/collectors

    'rc1-lan1-ipv4' => [
        'vlan_id'    => 2,
        'protocol'   => 4,
        'type'       => 'RC',   // RC|RS|AS112?
        'name'       => 'INEX LAN1 - Route Collector - IPv4',
        'shortname'  => 'RC1 - LAN1 - IPv4',
        'router_id'  => '185.6.36.128',
        'peering_ip' => '185.6.36.128',
        'asn'        => 2128,
        'type'       => 'bird',
        'mgmt_ip'    => '10.39.5.214',
        'api'        => 'http://rc1-lan1-ipv4.mgmt.inex.ie/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/collector/bird/standard',
    ],

];
