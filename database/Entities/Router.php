<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Entities;

use Carbon\Carbon;
use DateTime;

/**
 * Router
 */
class Router
{

    /**
     * CONST PROTOCOL
     */
    const PROTOCOL_IPV4                 = 4;
    const PROTOCOL_IPV6                 = 6;

    /**
     * @var array Email ids to classes
     */
    public static $PROTOCOLS = [
        self::PROTOCOL_IPV4     =>      'IPv4',
        self::PROTOCOL_IPV6     =>      'IPv6'
    ];

    /**
     * CONST TYPES
     */
    const TYPE_ROUTE_SERVER                 = 1;
    const TYPE_ROUTE_COLLECTOR              = 2;
    const TYPE_AS112                        = 3;
    const TYPE_OTHER                        = 99;

    /**
     * @var array Email ids to classes
     */
    public static $TYPES = [
        self::TYPE_ROUTE_SERVER             => 'Route Server',
        self::TYPE_ROUTE_COLLECTOR          => 'Route Collector',
        self::TYPE_AS112                    => 'AS112',
        self::TYPE_OTHER                    => 'Other'
    ];

    /**
     * @var array Email ids to classes
     */
    public static $TYPES_SHORT = [
        self::TYPE_ROUTE_SERVER             => 'RS',
        self::TYPE_ROUTE_COLLECTOR          => 'RC',
        self::TYPE_AS112                    => 'AS112',
        self::TYPE_OTHER                    => 'Other'
    ];

    /**
     * CONST SOFTWARES
     */
    const SOFTWARE_BIRD                     = 1;
    const SOFTWARE_BIRD2                    = 6;
    const SOFTWARE_CISCO                    = 5;
    const SOFTWARE_FRROUTING                = 3;
    const SOFTWARE_GOBGP                    = 8;
    const SOFTWARE_JUNOS                    = 7;
    const SOFTWARE_QUAGGA                   = 2;
    const SOFTWARE_OPENBGPD                 = 4;
    const SOFTWARE_OTHER                    = 99;

    /**
     * @var array Email ids to classes
     */
    public static $SOFTWARES = [
        self::SOFTWARE_BIRD                 => 'Bird v1',
        self::SOFTWARE_BIRD2                => 'Bird v2',
        self::SOFTWARE_CISCO                => 'Cisco',
        self::SOFTWARE_FRROUTING            => 'FRRouting',
        self::SOFTWARE_GOBGP                => 'GoBGP',
        self::SOFTWARE_JUNOS                => 'JunOS',
        self::SOFTWARE_QUAGGA               => 'Quagga',
        self::SOFTWARE_OPENBGPD             => 'OpenBGPd',
        self::SOFTWARE_OTHER                => 'Other'
    ];

    /**
     * CONST SOFTWARES
     */
    const API_TYPE_NONE                     = 0;
    const API_TYPE_BIRDSEYE                 = 1;
    const API_TYPE_OTHER                    = 99;



    /**
     * @var array Email ids to classes
     */
    public static $API_TYPES = [
        self::API_TYPE_NONE                 => 'None',
        self::API_TYPE_BIRDSEYE             => 'Birdseye',
        self::API_TYPE_OTHER                => 'Other'
    ];

    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
    private $handle;

    /**
     * @var integer
     */
    private $protocol;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $shortname;

    /**
     * @var string
     */
    private $router_id;

    /**
     * @var string
     */
    private $peering_ip;

    /**
     * @var integer
     */
    private $asn;

    /**
     * @var string
     */
    private $software;

    /**
     * @var string
     */
    private $software_version = '';

    /**
     * @var string
     */
    private $operating_system = '';

    /**
     * @var string
     */
    private $operating_system_version = '';

    /**
     * @var string
     */
    private $mgmt_host;

    /**
     * @var string
     */
    private $api;

    /**
     * @var integer
     */
    private $api_type = self::API_TYPE_NONE;

    /**
     * @var integer
     */
    private $lg_access = User::AUTH_CUSTUSER;

    /**
     * @var boolean
     */
    private $quarantine = false;

    /**
     * @var boolean
     */
    private $bgp_lc = false;

    /**
     * @var boolean
     */
    private $rpki = false;

    /**
     * @var boolean
     */
    private $rfc1997_passthru = false;


    /**
     * @var boolean
     */
    private $skip_md5 = false;

    /**
     * @var string
     */
    private $template;
    /**
     * @var \DateTime
     */
    private $last_updated;

    /**
     * @var \Entities\Vlan
     */
    private $vlan;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get handle
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Get handle
     *
     * @return string
     */
    public function handle()
    {
        return $this->getHandle();
    }

    /**
     * Get protocol
     *
     * @return int
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Get protocol
     *
     * @return int
     */
    public function protocol()
    {
        return $this->getProtocol();
    }


    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function type()
    {
        return $this->getType();
    }


    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function name()
    {
        return $this->getName();
    }


    /**
     * Get shortname
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortname;
    }

    /**
     * Get shortname
     *
     * @return string
     */
    public function shortname()
    {
        return $this->getShortName();
    }


    /**
     * Get router_id
     *
     * @return string
     */
    public function getRouterId()
    {
        return $this->router_id;
    }

    /**
     * Get router_id
     *
     * @return string
     */
    public function routerId()
    {
        return $this->getRouterId();
    }


    /**
     * Get peering_ip
     *
     * @return string
     */
    public function getPeeringIp()
    {
        return $this->peering_ip;
    }

    /**
     * Get peering_ip
     *
     * @return string
     */
    public function peeringIp()
    {
        return $this->getPeeringIp();
    }


    /**
     * Get asn
     *
     * @return int
     */
    public function getAsn()
    {
        return $this->asn;
    }

    /**
     * Get asn
     *
     * @return int
     */
    public function asn()
    {
        return $this->getAsn();
    }


    /**
     * Get software
     *
     * @return string
     */
    public function getSoftware()
    {
        return $this->software;
    }

    /**
     * Get software
     *
     * @return string
     */
    public function software()
    {
        return $this->getSoftware();
    }


    /**
     * Get software version
     *
     * @return string
     */
    public function getSoftwareVersion()
    {
        return $this->software_version;
    }

    /**
     * Get software version
     *
     * @return string
     */
    public function softwareVersion()
    {
        return $this->getSoftwareVersion();
    }

    /**
     * Get OS
     *
     * @return string
     */
    public function getOperatingSystem()
    {
        return $this->operating_system;
    }

    /**
     * Get OS
     *
     * @return string
     */
    public function operatingSystem()
    {
        return $this->getOperatingSystem();
    }

    /**
     * Get OS version
     *
     * @return string
     */
    public function getOperatingSystemVersion()
    {
        return $this->operating_system_version;
    }

    /**
     * Get OS version
     *
     * @return string
     */
    public function operatingSystemVersion()
    {
        return $this->getOperatingSystemVersion();
    }





    /**
     * Get mgmt_host
     *
     * @return string
     */
    public function getMgmtHost()
    {
        return $this->mgmt_host;
    }

    /**
     * Get mgmt_host
     *
     * @return string
     */
    public function mgmtIp()
    {
        return $this->getMgmtHost();
    }


    /**
     * Get api
     *
     * @return string
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Get api
     *
     * @return string
     */
    public function api()
    {
        return $this->getApi();
    }


    /**
     * Get api_type
     *
     * @return int
     */
    public function getApiType()
    {
        return $this->api_type;
    }

    /**
     * Get api_type
     *
     * @return int
     */
    public function apiType()
    {
        return $this->getApiType();
    }

    /**
     * Does the router have an API?
     *
     * In other words, is 'api' and 'api_type' set?
     * @return bool
     */
    public function hasApi(): bool {
        return $this->getApi() && $this->getApiType();
    }

    /**
     * Get lg_access
     *
     * @return int
     */
    public function getLgAccess()
    {
        return $this->lg_access;
    }

    /**
     * Get lg_access
     *
     * @return int
     */
    public function lgAccess()
    {
        return $this->getLgAccess();
    }

    /**
     * Get quarantine
     *
     * @return bool
     */
    public function getQuarantine()
    {
        return $this->quarantine;
    }

    /**
     * Get quarantine
     *
     * @return bool
     */
    public function quarantine()
    {
        return $this->getQuarantine();
    }

    /**
     * Get bgp_lc
     *
     * @return bool
     */
    public function getBgpLc()
    {
        return $this->bgp_lc;
    }

    /**
     * Get bgp_lc
     *
     * @return bool
     */
    public function bgpLargeCommunities()
    {
        return $this->getBgpLc();
    }

    /**
     * Get rpki enabled state
     *
     * @return bool
     */
    public function getRPKI(): bool
    {
        return $this->rpki;
    }

    /**
     * Alias get rpki enabled state
     *
     * @return bool
     */
    public function rpki(): bool
    {
        return $this->getRPKI();
    }

    /**
     * Get rfc1997_passthru enabled state
     *
     * @return bool
     */
    public function getRFC1997Passthru(): bool
    {
        return $this->rfc1997_passthru;
    }

    /**
     * Alias get rfc1997_passthru enabled state
     *
     * @return bool
     */
    public function rfc1997Passthru(): bool
    {
        return $this->getRFC1997Passthru();
    }


    /**
     * Get skip MD5
     *
     * @return bool
     */
    public function getSkipMd5()
    {
        return $this->skip_md5;
    }

    /**
     * Get skip MD5
     *
     * @return bool
     */
    public function skipMD5()
    {
        return $this->getSkipMD5();
    }


    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get last updated
     *
     * @return DateTime|null
     */
    public function getLastUpdated() {
        return $this->last_updated;
    }

    /**
     * Get last updated as a Carbon object
     *
     * @return Carbon|null
     */
    public function getLastUpdatedCarbon() {
        return $this->last_updated ? Carbon::instance( $this->getLastUpdated() ) : null;
    }

    /**
     * Get vlan
     *
     * @return \Entities\Vlan
     */
    public function getVlan()
    {
        return $this->vlan;
    }

    /**
     * Get vlan ID
     *
     * @return int
     */
    public function vlanId()
    {
        return $this->getVlan()->getId();
    }


    /**
     * Is the type TYPE_ROUTE_SERVER?
     *
     * @return bool
     */
    public function isTypeRouterServer(): bool {
        return $this->getType() === self::TYPE_ROUTE_SERVER;
    }

    /**
     * Is the type TYPE_ROUTE_COLLECTOR?
     *
     * @return bool
     */
    public function isTypeRouterCollector(): bool {
        return $this->getType() === self::TYPE_ROUTE_COLLECTOR;
    }

    /**
     * Is the type TYPE_ASN112?
     *
     * @return bool
     */
    public function isTypeAS112(): bool {
        return $this->getType() === self::TYPE_AS112;
    }

    /**
     * Is the type TYPE_OTHER?
     *
     * @return bool
     */
    public function isTypeOther(): bool {
        return $this->getType() === self::TYPE_OTHER;
    }

    /**
     * Is the type API_TYPE_NONE?
     *
     * @return bool
     */
    public function isApiNone(): bool {
        return $this->getApiType() === self::API_TYPE_NONE;
    }

    /**
     * Is the type API_TYPE_BIRDSEYE?
     *
     * @return bool
     */
    public function isApiBirdseye(): bool {
        return $this->getApiType() === self::API_TYPE_BIRDSEYE;
    }

    /**
     * Is the type API_TYPE_OTHER?
     *
     * @return bool
     */
    public function isApiOther(): bool {
        return $this->getApiType() === self::API_TYPE_OTHER;
    }



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Router
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set handle
     *
     * @param string $handle
     *
     * @return Router
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
        return $this;
    }

    /**
     * Set protocol
     *
     * @param int $protocol
     *
     * @return Router
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return Router
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set shortname
     *
     * @param string $shortName
     *
     * @return Router
     */
    public function setShortName($shortName)
    {
        $this->shortname = $shortName;
        return $this;
    }

    /**
     * Set router_id
     *
     * @param string $router_id
     *
     * @return Router
     */
    public function setRouterId($router_id)
    {
        $this->router_id = $router_id;
        return $this;
    }

    /**
     * Set peering_ip
     *
     * @param string $peering_ip
     *
     * @return Router
     */
    public function setPeeringIp($peering_ip)
    {
        $this->peering_ip = $peering_ip;
        return $this;
    }

    /**
     * Set asn
     *
     * @param int $asn
     *
     * @return Router
     */
    public function setAsn($asn)
    {
        $this->asn = $asn;
        return $this;
    }

    /**
     * Set software
     *
     * @param string $software
     *
     * @return Router
     */
    public function setSoftware($software)
    {
        $this->software = $software;
        return $this;
    }


    /**
     * Set software version
     *
     * @param string $software_version
     * @return Router
     */
    public function setSoftwareVersion($software_version)
    {
        $this->software_version = $software_version ?? '';
        return $this;
    }

    /**
     * Set os
     *
     * @param string $operating_system
     * @return Router
     */
    public function setOperatingSystem($operating_system)
    {
        $this->operating_system = $operating_system ?? '';
        return $this;
    }

    /**
     * Set os version
     *
     * @param string $operating_system_version
     * @return Router
     */
    public function setOperatingSystemVersion($operating_system_version)
    {
        $this->operating_system_version = $operating_system_version ?? '';
        return $this;
    }




    /**
     * Set mgmt_host
     *
     * @param string $mgmt_host
     *
     * @return Router
     */
    public function setMgmtHost($mgmt_host)
    {
        $this->mgmt_host = $mgmt_host;
        return $this;
    }

    /**
     * Set api
     *
     * @param string $api
     *
     * @return Router
     */
    public function setApi($api)
    {
        $this->api = $api;
        return $this;
    }

    /**
     * Set api_type
     *
     * @param int $api_type
     *
     * @return Router
     */
    public function setApiType($api_type)
    {
        $this->api_type = $api_type;
        return $this;
    }

    /**
     * Set lg_access
     *
     * @param int $lg_access
     *
     * @return Router
     */
    public function setLgAccess($lg_access)
    {
        $this->lg_access = $lg_access;
        return $this;
    }

    /**
     * Set quarantine
     *
     * @param bool $quarantine
     *
     * @return Router
     */
    public function setQuarantine($quarantine)
    {
        $this->quarantine = $quarantine;
        return $this;
    }

    /**
     * Set bgp_lc
     *
     * @param bool $bgp_lc
     *
     * @return Router
     */
    public function setBgpLc($bgp_lc)
    {
        $this->bgp_lc = $bgp_lc;
        return $this;
    }

    /**
     * Set rpki enabled state
     *
     * @param bool $rpki
     *
     * @return Router
     */
    public function setRPKI( bool $rpki ): Router
    {
        $this->rpki = $rpki;
        return $this;
    }

    /**
     * Set rfc1997 passthru enabled state
     *
     * @param bool $rfc1997_passthru
     *
     * @return Router
     */
    public function setRFC1997Passthru( bool $rfc1997_passthru ): Router
    {
        $this->rfc1997_passthru = $rfc1997_passthru;
        return $this;
    }

    /**
     * Set skip_md5
     *
     * @param bool $skip_md5
     *
     * @return Router
     */
    public function setSkipMd5($skip_md5)
    {
        $this->skip_md5 = $skip_md5;
        return $this;
    }

    /**
     * Set template
     *
     * @param string $template
     *
     * @return Router
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Set last updated
     *
     * @param DateTime $date
     * @return Router
     */
    public function setLastUpdated( DateTime $date ): Router {
        $this->last_updated = $date;
        return $this;
    }

    /**
     * Set vlan
     *
     * @param \Entities\Vlan $vlan
     *
     * @return Router
     */
    public function setVlan(Vlan $vlan)
    {
        $this->vlan = $vlan;
        return $this;
    }


    /**
     * Turn the database integer representation of the protocol into text as
     * defined in the self::$PROTOCOLS array (or 'Unknown')
     * @return string
     */
    public function resolveProtocol(): string {
        return self::$PROTOCOLS[ $this->getProtocol() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES array (or 'Unknown')
     * @return string
     */
    public function resolveType(): string {
        return self::$TYPES[ $this->getType() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES_SHORT array (or 'Unknown')
     * @return string
     */
    public function resolveTypeShortName(): string {
        return self::$TYPES_SHORT[ $this->getType() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the software into text as
     * defined in the self::$SOFTWARES array (or 'Unknown')
     * @return string
     */
    public function resolveSoftware(): string {
        return self::$SOFTWARES[ $this->getSoftware() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the api type into text as
     * defined in the self::$SOFTWARES array (or 'Unknown')
     * @return string
     */
    public function resolveApiType(): string {
        return self::$API_TYPES[ $this->getApiType() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the lg access into text as
     * defined in the User::$PRIVILEGES_ALL array (or 'Unknown')
     * @return string
     */
    public function resolveLgAccess(): string {
        return User::$PRIVILEGES_ALL[ $this->getLgAccess() ] ?? 'Unknown';
    }

    /**
     * This function controls access to a router for a looking glass
     *
     * @param int $privs User's privileges (see \Entities\User)
     * @return bool
     */
    function authorise( int $privs ): bool {
        return $privs >= $this->getLgAccess();
    }

    /**
     * This function check is the last updated time is greater than the given number of seconds
     *
     * @return bool
     */
    public function lastUpdatedGreaterThanSeconds( int $threshold ) {
        if( !$this->getLastUpdated() ) {
            // if null, then, as far as we know, it has never been updated....
            return true;
        }

        return $this->getLastUpdatedCarbon()->diffInSeconds( null ) > $threshold;
    }
}
