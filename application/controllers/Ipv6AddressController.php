<?php

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

require_once APPLICATION_PATH . '/controllers/Ipv4AddressController.php';

/**
 * Controller: Manage IPv6 addresses
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Ipv6AddressController extends Ipv4AddressController
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        parent::_feInit();
        
        $this->_feParams->entity        = '\\Entities\\IPv6Address';
        $this->_feParams->pagetitle     = 'IPv6 Addresses';
        $this->_feParams->titleSingular = 'IPv6 Address';
        $this->_feParams->nameSingular  = 'an IPv6 address';
    }
    
    public function addAction()
    {
        $this->forward( 'add', 'ipv4-address' );
    }

    public function ajaxGetNextAction()
    {
        $schema = ["LONAP" => "2001:7f8:17::%asn:%rtr"];
        $cust = $this->getD2R( "\\Entities\\Customer" )->find( $this->getParam('custid', 0 ) );

        if( !isset( $schema[ $this->getParam('schema', "" ) ] ) || !$cust )
            return false;
        

        $asn = dechex( $cust->getAutsys() );
        $ipv6s = $this->getD2R( "\\Entities\\IPv6Address" )->getArrayForCustomer( $cust );

        $rtr = 0;
        foreach( $ipv6s as $ip )
        {
            if( !strrpos( $ip, ':' ) )
                continue;
            
            $end = substr( $ip, strrpos( $ip, ':' ) );
            $end = hexdec( $end );
            if( $end > $rtr )
                $rtr = $end;
        }
        $rtr = dechex( $rtr + 1 );
        $ipv6 = str_replace( '%asn', $asn, $schema[ $this->getParam('schema', "" ) ] );
        $ipv6 = str_replace( '%rtr', $rtr, $ipv6 );
        echo $ipv6;
    }
}

