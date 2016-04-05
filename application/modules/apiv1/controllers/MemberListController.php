<?php

/*
 * Copyright (C) 2009-2014 Internet Neutral Exchange Association Limited.
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
 * Controller: API V1 Memberlist controller
 *
 * @author     Nick Hilliard <nick@foobar.org>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2014, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Apiv1_MemberListController extends IXP_Controller_API_V1Action
{
    public function preDispatch()
    {
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
    }


    public function listAction()
    {
        if( !config( 'ixp_api.json_export_schema.public', false ) ) {
            $this->assertMinUserPriv( \Entities\User::AUTH_CUSTUSER );
        }
        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
        $exporter = new \IXP\Utils\Export\JsonSchema;
        print $exporter->get( $this->getParam( 'version', false ) );
    }
}
