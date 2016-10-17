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


/**
 * Form: adding / editing IP addresses
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_AddAddresses extends IXP_Form
{
    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'ipv4-address/forms/add-addresses.phtml' ] ] ] );
        
        $this->addElement( IXP_Form_Vlan::getPopulatedSelect( 'vlanid', true ) );
                

        $type = $this->createElement( 'select', 'type' );
        $type->setMultiOptions( [ 'IPv4' => 'IPv4', 'IPv6' => 'IPv6' ] )
            ->setRegisterInArrayValidator( true )
            ->setValue( 'IPv6' )
            ->setAttrib( 'class', 'span2 chzn-select' )
            ->setLabel( 'Address Family' );
        $this->addElement( $type );
    }
}

