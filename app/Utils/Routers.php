<?php

namespace IXP\Utils;


/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Exceptions\ConfigurationException;
use IXP\Exceptions\Utils\RouterException;
use View;

/**
 * A class to proxy access to config/routers.php. Eventually this will serve
 * as a template for an interface / contract to a database backed version of
 * that configuration file.
 *
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 */
class Routers
{
    private $routers = [];

    public function __construct() {
        $this->reset();
    }
    
    public function reset() {
        if( !($this->routers = config('routers', false )) ) {
            throw new ConfigurationException('config/routers.php does not exists');
        }
    }
    
    public function getObjects() {
        $objs = [];
        foreach( $this->routers as $h => $r ) {
            $objs[$h] = new Router($h);
        }
        return $objs;
    }
    
    public function isEmpty() {
        return $this->routers == [];
    }
    
    public function filterForVlanId( $i ) {
        foreach( $this->routers as $h => $r ) {
            if( $r['vlan_id'] != $i ) {
                unset( $this->routers[$h] );
            }
        }
        return $this;
    }

    public function filterForProtocol( $p ) {
        foreach( $this->routers as $h => $r ) {
            if( $r['protocol'] != $p ) {
                unset( $this->routers[$h] );
            }
        }
        return $this;
    }

    public function filterForType( $t ) {
        foreach( $this->routers as $h => $r ) {
            if( $r['type'] != $t ) {
                unset( $this->routers[$h] );
            }
        }
        return $this;
    }

    public function filterForApiType( $t ) {
        foreach( $this->routers as $h => $r ) {
            if( $r['api_type'] != $t ) {
                unset( $this->routers[$h] );
            }
        }
        return $this;
    }
}
