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
 * Controller: Nagios CLI Actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class NagiosCliController extends IXP_Controller_CliAction
{

    /**
     * Generates a Nagios configuration for supported switches in the database
     */
    public function genSwitchConfigAction()
    {
        $this->view->ixp = $ixp = $this->cliResolveIXP();
        
        // we want a fresh switch list here
        $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->clearCache( true );
        $switches = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->getAndCache( true );
    
        echo $this->view->render( 'nagios-cli/conf/switch-definitions.phtml' );
    
        $brocade = array(); $vendor_brocade = "";
        $cisco   = array(); $vendor_cisco   = "";
        $mrv     = array(); $vendor_mrv     = "";
    
        $all     = [];
    
        foreach( $switches as $s )
        {
            $this->view->sw = $s;
            echo $this->view->render( 'nagios-cli/conf/switch-hosts.phtml' );
    
            switch( $s->getVendor()->getName() )
            {
                case 'Foundry Networks':
                    $brocade[] = $s;
                    $vendor_brocade .= ( strlen( $vendor_brocade ) ? ', ' : '' ) . $s->getName();
                    break;
    
                case 'Cisco Systems':
                    $cisco[] = $s;
                    $vendor_cisco .= ( strlen( $vendor_cisco ) ? ', ' : '' ) . $s->getName();
                    break;
    
                case 'MRV':
                    $mrv[] = $s;
                    $vendor_mrv .= ( strlen( $vendor_mrv ) ? ', ' : '' ) . $s->getName();
                    break;
            }

            $all[] = $s->getName();
    
            if( isset( $locations[ $s->getCabinet()->getLocation()->getShortname() ] ) )
                $locations[ $s->getCabinet()->getLocation()->getShortname() ] .= ", " . $s->getName();
            else
                $locations[ $s->getCabinet()->getLocation()->getShortname() ] = $s->getName();
        }
    
        $this->view->all = implode( ', ', $all );
    
        $this->view->locations = $locations;
    
        $this->view->brocade = $brocade;   $this->view->vendor_brocade = $vendor_brocade;
        $this->view->cisco   = $cisco;     $this->view->vendor_cisco   = $vendor_cisco;
        $this->view->mrv     = $mrv;       $this->view->vendor_mrv     = $vendor_mrv;
    
        echo $this->view->render( 'nagios-cli/conf/switch-templates.phtml' );
    }
    
}

