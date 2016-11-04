<?php

return [
    // route servers/collectors

    'rc1-lan1-ipv4' => [
        'vlan_id'    => 1,
        'protocol'   => 4,
        'type'       => 'RC',   // RC|RS|AS112?
        'name'       => 'INEX LAN1 - Route Collector - IPv4',
        'shortname'  => 'RC1 - LAN1 - IPv4',
        'router_id'  => '192.0.2.8',
        'peering_ip' => '192.0.2.8',
        'asn'        => 65500,
        'type'       => 'bird',
        'mgmt_ip'    => '203.0.113.8',
        'api'        => 'http://rc1-lan1-ipv4.mgmt.example.com/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/collector/bird/standard',
    ],

    'rc1-lan1-ipv6' => [
        'vlan_id'    => 1,
        'protocol'   => 6,
        'type'       => 'RC',   // RC|RS|AS112?
        'name'       => 'INEX LAN1 - Route Collector - IPv6',
        'shortname'  => 'RC1 - LAN1 - IPv6',
        'router_id'  => '192.0.2.8',
        'peering_ip' => '2001:db8::8',
        'asn'        => 65500,
        'type'       => 'bird',
        'mgmt_ip'    => '2001:db8:0:0:2::8',
        'api'        => 'http://rc1-lan1-ipv6.mgmt.example.com/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/collector/bird/standard',
    ],

    'rc1-lan2-ipv4' => [
        'vlan_id'    => 2,
        'protocol'   => 4,
        'type'       => 'RC',   // RC|RS|AS112?
        'name'       => 'INEX LAN2 - Route Collector - IPv4',
        'shortname'  => 'RC1 - LAN2 - IPv4',
        'router_id'  => '192.0.2.9',
        'peering_ip' => '192.0.2.9',
        'asn'        => 65500,
        'type'       => 'bird',
        'mgmt_ip'    => '203.0.113.9',
        'api'        => 'http://rc1-lan2-ipv4.mgmt.example.com/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/collector/bird/standard',
    ],

    'rc1-lan2-ipv6' => [
        'vlan_id'    => 2,
        'protocol'   => 6,
        'type'       => 'RC',   // RC|RS|AS112?
        'name'       => 'INEX LAN2 - Route Collector - IPv6',
        'shortname'  => 'RC1 - LAN2 - IPv6',
        'router_id'  => '192.0.2.9',
        'peering_ip' => '2001:db8::9',
        'asn'        => 65500,
        'type'       => 'bird',
        'mgmt_ip'    => '2001:db8:0:0:2::9',
        'api'        => 'http://rc1-lan2-ipv6.mgmt.example.com/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/collector/bird/standard',
    ],

    'rs1-lan1-ipv4' => [
        'vlan_id'    => 1,
        'protocol'   => 4,
        'type'       => 'RS',   // RC|RS|AS112?
        'name'       => 'INEX LAN1 - Route Server - IPv4',
        'shortname'  => 'RS1 - LAN1 - IPv4',
        'router_id'  => '192.0.2.18',
        'peering_ip' => '192.0.2.18',
        'asn'        => 65501,
        'type'       => 'bird',
        'mgmt_ip'    => '203.0.113.18',
        'api'        => 'http://rs1-lan1-ipv4.mgmt.example.com/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/server/bird/standard',
    ],

    'rs1-lan1-ipv6' => [
        'vlan_id'    => 1,
        'protocol'   => 6,
        'type'       => 'RS',   // RC|RS|AS112?
        'name'       => 'INEX LAN1 - Route Server - IPv6',
        'shortname'  => 'RS1 - LAN1 - IPv6',
        'router_id'  => '192.0.2.18',
        'peering_ip' => '2001:db8::18',
        'asn'        => 65501,
        'type'       => 'bird',
        'mgmt_ip'    => '2001:db8:0:0:2::18',
        'api'        => 'http://rs1-lan1-ipv6.mgmt.example.com/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/server/bird/standard',
    ],

    'rs1-lan2-ipv4' => [
        'vlan_id'    => 2,
        'protocol'   => 4,
        'type'       => 'RS',   // RC|RS|AS112?
        'name'       => 'INEX LAN2 - Route Server - IPv4',
        'shortname'  => 'RS1 - LAN2 - IPv4',
        'router_id'  => '192.0.2.19',
        'peering_ip' => '192.0.2.19',
        'asn'        => 65501,
        'type'       => 'bird',
        'mgmt_ip'    => '203.0.113.19',
        'api'        => 'http://rs1-lan2-ipv4.mgmt.example.com/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/server/bird/standard',
    ],

    'rs1-lan2-ipv6' => [
        'vlan_id'    => 2,
        'protocol'   => 6,
        'type'       => 'RS',   // RC|RS|AS112?
        'name'       => 'INEX LAN2 - Route Server - IPv6',
        'shortname'  => 'RS1 - LAN2 - IPv6',
        'router_id'  => '192.0.2.19',
        'peering_ip' => '2001:db8::19',
        'asn'        => 65501,
        'type'       => 'bird',
        'mgmt_ip'    => '2001:db8:0:0:2::19',
        'api'        => 'http://rs1-lan2-ipv6.mgmt.example.com/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/server/bird/standard',
    ],

    'unknown-vlan' => [
        'vlan_id'    => 99999,
        'protocol'   => 6,
        'type'       => 'RC',   // RC|RS|AS112?
        'name'       => 'INEX LAN2 - Route Collector - IPv6',
        'shortname'  => 'RC1 - LAN2 - IPv6',
        'router_id'  => '192.0.2.9',
        'peering_ip' => '2001:db8::9',
        'asn'        => 65500,
        'type'       => 'bird',
        'mgmt_ip'    => '2001:db8:0:0:2::9',
        'api'        => 'http://rc1-lan2-ipv6.mgmt.example.com/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/collector/bird/standard',
    ],

    'unknown-template' => [
        'vlan_id'    => 1,
        'protocol'   => 6,
        'type'       => 'RC',   // RC|RS|AS112?
        'name'       => 'INEX LAN2 - Route Collector - IPv6',
        'shortname'  => 'RC1 - LAN2 - IPv6',
        'router_id'  => '192.0.2.9',
        'peering_ip' => '2001:db8::9',
        'asn'        => 65500,
        'type'       => 'bird',
        'mgmt_ip'    => '2001:db8:0:0:2::9',
        'api'        => 'http://rc1-lan2-ipv6.mgmt.example.com/api',
        'api_type'   => 'birdseye',
        'lg_access'  => Entities\User::AUTH_PUBLIC,
        'quarantine' => false,
        'template'   => 'api/v4/router/does-not-exist',
    ],


];
