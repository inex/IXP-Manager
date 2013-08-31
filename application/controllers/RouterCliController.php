<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Controller: Router CLI Actions (such as collectors and servers)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterCliController extends IXP_Controller_CliAction
{
    public function genCollectorConfAction()
    {
        $this->view->vlan = $vlan = $this->cliResolveVlanId();
                
        // what is the destination router type?
        if( !isset( $this->_options['router']['collector']['conf']['target'] ) )
            die( "ERROR: No target router type configured in application.ini\n");

        $target = $this->_options['router']['collector']['conf']['target'];

        $this->view->v4ints = $this->sanitiseVlanInterfaces( $vlan, 4 );
        $this->view->v6ints = $this->sanitiseVlanInterfaces( $vlan, 6 );
        
        if( isset( $this->_options['router']['collector']['conf']['dstfile'] ) )
        {
            if( !$this->writeConfig( $this->_options['smokeping']['conf']['dstfile'], $this->view->render( 'smokeping-cli/conf/index.cfg' ) ) )
                fwrite( STDERR, "Error: could not save configuration data\n" );
        }
        else
            echo $this->view->render( "router-cli/collector/{$target}/index.cfg" );
    }
    
    /**
     * Utility function to get and return active VLAN interfaces on the requested protocol
     * suitable for route collector configuration.
     *
     * Sample return:
     *
     *     [
     *         [cid] => 999
     *         [cname] => Customer Name
     *         [cshortname] => shortname
     *         [autsys] => 65000
     *         [peeringmacro] => QWE       // or AS65500 if not defined
     *         [vliid] => 159
     *         [address] => 192.0.2.123
     *         [bgpmd5secret] => qwertyui  // or false
     *         [maxprefixes] => 20
     *     ]
     *
     * @param \Entities\Vlan $vlan
     * @param int $proto
     * @return array As defined above
     */
    private function sanitiseVlanInterfaces( $vlan, $proto )
    {
        $ints = $this->getD2R( '\\Entities\\VlanInterface' )->getForProto( $vlan, $proto, false );
        $newints = [];
        
        foreach( $ints as $int )
        {
            if( !$int['enabled'] )
                continue;
            
            unset( $int['enabled'] );
            
            if( $int['maxbgpprefix'] && $int['maxbgpprefix'] > $int['gmaxprefixes'] )
                $int['maxprefixes'] = $int['maxbgpprefix'];
            else
                $int['maxprefixes'] = $int['gmaxprefixes'];
            
            if( !$int['maxprefixes'] )
                $int['maxprefixes'] = 20;
            
            unset( $int['gmaxprefixes'] );
            unset( $int['maxbgpprefix'] );
            
            if( $proto == 6 && $int['peeringmacrov6'] )
                $int['peeringmacro'] = $int['peeringmacrov6'];
            
            if( !$int['peeringmacro'] )
                $int['peeringmacro'] = 'AS' . $int['autsys'];
            
            unset( $int['peeringmacrov6'] );
            
            if( !$int['bgpmd5secret'] )
                $int['bgpmd5secret'] = false;
            
            $newints[] = $int;
        }
        
        return $newints;
    }
}

