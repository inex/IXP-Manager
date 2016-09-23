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
 * Controller: API V1 Index controller
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Apiv1_CustomerController extends IXP_Controller_API_V1Action
{

    public function detailsAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );

        $user = $this->assertMinUserPriv( \Entities\User::AUTH_SUPERUSER );

        $customers = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive(
            true,
            $this->getParam( 'trafficing',   false ) ? true : false,
            $this->getParam( 'externalonly', false ) ? true : false,
            is_int( $this->getParam( 'ixp', false ) ) ? $this->getParam( 'ixp', false ) : false
        );

        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
        echo json_encode( $customers );
    }

    public function euroixExportAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );

        $customers = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive(
            false,
            $this->getParam( 'trafficing',   true ) ? true : false,
            $this->getParam( 'externalonly', true ) ? true : false,
            is_int( $this->getParam( 'ixp', false ) ) ? $this->getParam( 'ixp', false ) : false
        );

        $this->getResponse()->setHeader( 'Content-Type', 'text/plain' );

        foreach( $customers as $c )
        {
            echo sprintf( "%s;%s;%s;%s\n", str_replace( ';', '-', $c->getName() ),
                $c->getAutsys(), $c->getCorpwww(),
                $c->isIPvXEnabled( 6 ) ? 'Yes' : 'No'
            );
        }
    }
}
