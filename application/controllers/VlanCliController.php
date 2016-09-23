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
 * Controller: VLAN CLI Actions (such as reverse DNS entries)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanCliController extends IXP_Controller_CliAction
{
    /**
     * Action to generate a route collector configuration
     */
    public function genArpaEntriesAction()
    {
        $this->view->vlan = $vlan = $this->cliResolveVlanId();
        $this->view->proto = $proto = $this->cliResolveProtocol( true );
        $target = $this->cliResolveTarget( false );
        
        $addresses = $this->getD2R( '\\Entities\\Vlan' )->getArpaDetails( $vlan, $proto, false );
        
        foreach( $addresses as $i => $a )
        {
            if( $proto == 4 )
                $addresses[ $i ]['arpa'] = OSS_Net_IPv4::ipv4ToARPA( $a['address'] );
            else
                $addresses[ $i ]['arpa'] = OSS_Net_IPv6::ipv6ToARPA( $a['address'] );
        }

        $this->view->addresses = $addresses;
        
        echo $this->view->render( "vlan-cli/arpa/{$target}/index.cfg" );
    }
}

