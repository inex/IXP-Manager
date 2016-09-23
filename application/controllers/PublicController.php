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
 * Controller: Public controller for publically accessable information
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PublicController extends IXP_Controller_Action
{

    /**
     * Function to export details of members in a flexable way
     *
     * Called as: https://www.example.com/ixp/public/member-details
     *
     * You can tack on additional options:
     *
     * * format/json (default HTML)
     * * template/xxx (default member-details.phtml)
     *
     * If a template is specified, it'll use public/member-details/xxx after stripping
     * all but [a-zA-Z0-9-_] from the given template
     *
     */
    public function memberDetailsAction()
    {
        $this->view->customers = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( false );
        
        if( strtolower( $this->getParam( 'format', '0' ) ) == 'json' )
            $this->getResponse()->setHeader( 'Content-Type', 'application/json' );
        
        if( $this->getParam( 'template', false ) )
            $this->_helper->viewRenderer( 'member-details/' . preg_replace( '/[^0-9a-zA-Z-_]/', '', $this->getParam( 'template' ) ) );
        
    }
}

