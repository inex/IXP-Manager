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
 * Controller: API V1 RIR controller
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Apiv1_RirController extends IXP_Controller_API_V1Action
{
    public function preDispatch()
    {
        $this->assertUserPriv( \Entities\User::AUTH_SUPERUSER );
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
    }

    public function updateObjectAction()
    {
        if( !$tmpl = $this->getParam( 'tmpl', false ) )
            throw new Zend_Controller_Action_Exception( 'You must specify a RIR template to update', 412 );

        if( !$this->view->templateExists( 'rir/tmpl/' . $tmpl . '.tpl' ) )
            throw new Zend_Controller_Action_Exception( 'The specified RIR template does not exist', 412 );
        
        $email = $this->getParam( 'email', 'auto-dbm@ripe.net' );

        $customers = $this->getD2R( '\\Entities\\Customer' )->getCurrentActive( false, true, true );

        $asns = [];
        foreach( $customers as $c )
            $asns[ $c->getAutsys() ] = [ 
                'asmacro' => $c->resolveAsMacro( 4, 'AS' ),
                'name'    => $c->getName()
            ];
        
        ksort( $asns, SORT_NUMERIC );
        $this->view->asns = $asns;
        
        echo $this->view->render( 'rir/tmpl/' . $tmpl . '.tpl' );
    }
    
}
