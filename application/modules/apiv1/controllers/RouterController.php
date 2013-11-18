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
 * Controller: API V1 router controller
 * 
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Apiv1_RouterController extends IXP_Controller_API_V1Action
{
    use IXP_Controller_Trait_Router;
    
    public function preDispatch()
    {
        $this->assertUserPriv( \Entities\User::AUTH_SUPERUSER );
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
        
        // typically a module uses its own views/ folder - but we're sharing these
        // templates with the main / default module for the CLI actions so we'll
        // point it there instead
        $this->getView()->setScriptPath( APPLICATION_PATH . '/views' );
    }

    /**
     * @see https://github.com/inex/IXP-Manager/wiki/RIR-Objects
     * @throws Zend_Controller_Action_Exception
     */
    public function collectorConfAction()
    {
        $vlan   = $this->view->vlan = $this->apiGetParamVlan();
                
        // get, sanitise and verify the target name
        $target = preg_replace( '/[^\da-z_\-]/i', '', $this->apiGetParam( 'target', true ) );
        if( !$this->view->templateExists( "router-cli/collector/{$target}/index.cfg" ) )
            throw new Zend_Controller_Action_Exception( 'The specified target template does not exist', 401 );
        
        $this->apiLoadConfig();
        
        $this->view->proto = $proto = $this->apiGetParamProtocol( false );
        
        if( !$proto || $proto == 4 )
            $this->view->v4ints = $this->sanitiseVlanInterfaces( $vlan, 4 );
        
        if( !$proto || $proto == 6 )
            $this->view->v6ints = $this->sanitiseVlanInterfaces( $vlan, 6 );
        
        echo $this->view->render( "router-cli/collector/{$target}/index.cfg" );
    }

}
