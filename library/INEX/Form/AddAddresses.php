<?php

/*
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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


/**
 * Form: adding / editing IP addresses
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class INEX_Form_AddAddresses extends INEX_Form
{
    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'ipv4-address/forms/add-addresses.phtml' ] ] ] );
        
        $this->addElement( INEX_Form_Vlan::getPopulatedSelect( 'vlanid' ) );
                

        $type = $this->createElement( 'select', 'type' );
        $type->setMultiOptions( [ 'IPv4' => 'IPv4', 'IPv6' => 'IPv6' ] )
            ->setRegisterInArrayValidator( true )
            ->setValue( 'IPv6' )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->setLabel( 'Address Family' );
        $this->addElement( $type );
    }
}

