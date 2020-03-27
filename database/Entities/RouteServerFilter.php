<?php

namespace Entities;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Entities\{
    Customer    as CustomerEntity,
    Router      as RouterEntity,
    Vlan        as VlanEntity
};

/**
 * RouteServerFilter
 */
class RouteServerFilter
{
    const AS_IN             = null;
    const NO_ADVERTISE      = "NO_ADVERTISE";
    const PREPEND_ONCE      = "PREPEND_ONCE";
    const PREPEND_TWICE     = "PREPEND_TWICE";
    const PREPEND_THRICE    = "PREPEND_THRICE";

    public static $ADVERTISE_ACTION_TEXT = [
        self::AS_IN             => 'Advertise As Is',
        self::NO_ADVERTISE      => 'Do Not Advertise To Peer',
        self::PREPEND_ONCE      => 'Prepend My ASN Once To Peer',
        self::PREPEND_TWICE     => 'Prepend My ASN Twice To Peer',
        self::PREPEND_THRICE    => 'Prepend My ASN Thrice To Peer',
    ];

    public static $RECEIVE_ACTION_TEXT = [
        self::AS_IN             => "Receive As Is",
        self::NO_ADVERTISE      => "Do Not Receive To Peer",
        self::PREPEND_ONCE      => "Prepend Peer's ASN Once",
        self::PREPEND_TWICE     => "Prepend Peer's ASN Twice",
        self::PREPEND_THRICE    => "Prepend Peer's ASN Thrice",
    ];


    /**
     * @var string
     */
    private $prefix;

    /**
     * @var int
     */
    private $protocol;

    /**
     * @var string|null
     */
    private $action_advertise;

    /**
     * @var string|null
     */
    private $action_receive;

    /**
     * @var bool
     */
    private $enabled = '1';

    /**
     * @var int
     */
    private $order_by;

    /**
     * @var string
     */
    private $live;

    /**
     * @var int
     */
    private $id;

    /**
     * @var CustomerEntity
     */
    private $customer;

    /**
     * @var CustomerEntity
     */
    private $peer;

    /**
     * @var VlanEntity
     */
    private $vlan;


    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string|null $prefix
     * @return RouteServerFilter
     */
    public function setPrefix( $prefix ): RouteServerFilter
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return int
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param int|null $protocol
     * @return RouteServerFilter
     */
    public function setProtocol( $protocol ): RouteServerFilter
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getActionAdvertise(): ?string
    {
        return $this->action_advertise;
    }

    /**
     * @param string|null $action_advertise
     * @return RouteServerFilter
     */
    public function setActionAdvertise( ?string $action_advertise ): RouteServerFilter
    {
        $this->action_advertise = $action_advertise;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getActionReceive(): ?string
    {
        return $this->action_receive;
    }

    /**
     * @param string|null $action_receive
     * @return RouteServerFilter
     */
    public function setActionReceive( ?string $action_receive ): RouteServerFilter
    {
        $this->action_receive = $action_receive;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return RouteServerFilter
     */
    public function setEnabled( bool $enabled ): RouteServerFilter
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderBy(): int
    {
        return $this->order_by;
    }

    /**
     * @param int $order_by
     * @return RouteServerFilter
     */
    public function setOrderBy( int $order_by ): RouteServerFilter
    {
        $this->order_by = $order_by;
        return $this;
    }

    /**
     * @return string
     */
    public function getLive(): string
    {
        return $this->live;
    }

    /**
     * @param string $live
     * @return RouteServerFilter
     */
    public function setLive( string $live ): RouteServerFilter
    {
        $this->live = $live;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return RouteServerFilter
     */
    public function setId( int $id ): RouteServerFilter
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return RouteServerFilter
     */
    public function setCustomer( Customer $customer ): RouteServerFilter
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getPeer(): Customer
    {
        return $this->peer;
    }

    /**
     * @param Customer $peer
     * @return RouteServerFilter
     */
    public function setPeer( Customer $peer ): RouteServerFilter
    {
        $this->peer = $peer;
        return $this;
    }

    /**
     * @return Vlan
     */
    public function getVlan()
    {
        return $this->vlan;
    }

    /**
     * @param $vlan
     * @return RouteServerFilter
     */
    public function setVlan( $vlan ): RouteServerFilter
    {
        $this->vlan = $vlan;
        return $this;
    }


    /**
     * Turn the database integer representation of the action advertise into text as
     * defined in the self::$ADVERTISE_ACTION_TEXT array (or 'Unknown')
     * @return string
     */
    public function resolveActionAdvertise(): string {
        return self::$ADVERTISE_ACTION_TEXT[ $this->getActionAdvertise() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the action receive into text as
     * defined in the self::$RECEIVE_ACTION_TEXT array (or 'Unknown')
     * @return string
     */
    public function resolveActionReceive(): string {
        return self::$RECEIVE_ACTION_TEXT[ $this->getActionReceive() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the protocol into text as
     * defined in the RouterEntity::$PROTOCOLS array (or 'Unknown')
     * @return string
     */
    public function resolveProtocol(): string {
        return RouterEntity::$PROTOCOLS[ $this->getProtocol() ] ?? 'Both';
    }
}
