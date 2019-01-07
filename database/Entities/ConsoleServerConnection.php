<?php

namespace Entities;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


use Entities\{
    ConsoleServer   as ConsoleServerEntity,
    Customer        as CustomerEntity,
    Switcher        as SwitcherEntity
};

/**
 * Entities\ConsoleServerConnection
 */
class ConsoleServerConnection
{


    public static $SPEED = [
        300     => 300,
        600     => 600,
        1200    => 1200,
        2400    => 2400,
        4800    => 4800,
        9600    => 9600,
        14400   => 14400,
        19200   => 19200,
        28800   => 28800,
        38400   => 38400,
        57600   => 57600,
        115200  => 115200,
        230400  => 230400
    ];

    const PARITY_EVEN       = 1;
    const PARITY_ODD        = 2;
    const PARITY_NONE       = 3;

    public static $PARITY = [
        self::PARITY_EVEN   => "even",
        self::PARITY_ODD    => "odd",
        self::PARITY_NONE   => "none"
    ];

    const FLOW_CONTROL_NONE         = 1;
    const FLOW_CONTROL_RTS_CTS      = 2;
    const FLOW_CONTROL_XON_XOFF     = 3;

    public static $FLOW_CONTROL = [
        self::FLOW_CONTROL_NONE         => "none",
        self::FLOW_CONTROL_RTS_CTS      => "rts/cts",
        self::FLOW_CONTROL_XON_XOFF     => "xon/xoff"
    ];

    public static $STOP_BITS = [
        1 => 1,
        2 => 2,
    ];

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var string $port
     */
    protected $port;

    /**
     * @var integer $speed
     */
    protected $speed;

    /**
     * @var integer $parity
     */
    protected $parity;

    /**
     * @var integer $stopbits
     */
    protected $stopbits;

    /**
     * @var integer $flowcontrol
     */
    protected $flowcontrol;

    /**
     * @var boolean $autobaud
     */
    protected $autobaud;

    /**
     * @var string $notes
     */
    protected $notes;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var \Entities\Customer
     */
    protected $Customer;

    /**
     * @var ConsoleServer
     */
    protected $consoleServer;

    /**
     * @var integer $switchid
     */
    protected $switchid;


    /**
     * Set description
     *
     * @param string $description
     * @return ConsoleServerConnection
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set port
     *
     * @param string $port
     * @return ConsoleServerConnection
     */
    public function setPort($port)
    {
        $this->port = $port;
    
        return $this;
    }

    /**
     * Get port
     *
     * @return string 
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set speed
     *
     * @param integer $speed
     * @return ConsoleServerConnection
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
    
        return $this;
    }

    /**
     * Get speed
     *
     * @return integer 
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Set parity
     *
     * @param integer $parity
     * @return ConsoleServerConnection
     */
    public function setParity($parity)
    {
        $this->parity = $parity;
    
        return $this;
    }

    /**
     * Get parity
     *
     * @return integer 
     */
    public function getParity()
    {
        return $this->parity;
    }

    /**
     * Set stopbits
     *
     * @param integer $stopbits
     * @return ConsoleServerConnection
     */
    public function setStopbits($stopbits)
    {
        $this->stopbits = $stopbits;
    
        return $this;
    }

    /**
     * Get stopbits
     *
     * @return integer 
     */
    public function getStopbits()
    {
        return $this->stopbits;
    }

    /**
     * Set flowcontrol
     *
     * @param integer $flowcontrol
     * @return ConsoleServerConnection
     */
    public function setFlowcontrol($flowcontrol)
    {
        $this->flowcontrol = $flowcontrol;
    
        return $this;
    }

    /**
     * Get flowcontrol
     *
     * @return integer 
     */
    public function getFlowcontrol()
    {
        return $this->flowcontrol;
    }

    /**
     * Set autobaud
     *
     * @param boolean $autobaud
     * @return ConsoleServerConnection
     */
    public function setAutobaud($autobaud)
    {
        $this->autobaud = $autobaud;
    
        return $this;
    }

    /**
     * Get autobaud
     *
     * @return boolean 
     */
    public function getAutobaud()
    {
        return $this->autobaud;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return ConsoleServerConnection
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    
        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

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
     * Set Customer
     *
     * @param CustomerEntity $customer
     * @return ConsoleServerConnection
     */
    public function setCustomer( CustomerEntity $customer = null)
    {
        $this->Customer = $customer;
    
        return $this;
    }

    /**
     * Get Customer
     *
     * @return \Entities\Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * Set console server
     *
     * @param ConsoleServerEntity $consoleServer
     * @return ConsoleServerConnection
     */
    public function setConsoleServer( ConsoleServerEntity $consoleServer = null)
    {
        $this->consoleServer = $consoleServer;

        return $this;
    }

    /**
     * Get Console Server
     *
     * @return ConsoleServer
     */
    public function getConsoleServer()
    {
        return $this->consoleServer;
    }

    /**
     * Set Switcher
     *
     * @param SwitcherEntity $switcher
     * @return ConsoleServerConnection
     */
    public function setSwitcher( SwitcherEntity $switcher = null)
    {
        $this->setSwitchId( $switcher != null ? $switcher->getId() : null );
        return $this;
    }

    /**
     * Get Switcher
     *
     * @return void
     */
    public function getSwitcher()
    {
        // yann -> D2EM get Switcher for $this->>getSwitchId()
        die();
        //return $this->Switcher;
    }

    /**
     * Get switch id
     *
     * @return integer
     */
    public function getSwitchId()
    {
        return $this->switchid;
    }

    /**
     * Set switch id
     *
     * @param int $switchid
     * @return ConsoleServerConnection
     */
    public function setSwitchId( $switchid )
    {
        $this->switchid = $switchid;
        return $this;
    }


}
