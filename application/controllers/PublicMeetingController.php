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
 * Controller: Manage meetings
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PublicMeetingController extends IXP_Controller_Action
{

    /**
     * A simple HTML snippet for display on other websites
     */
    public function simpleAction()
    {
        $this->view->limit = $limit = (int)$this->getParam( 'limit', 0 );
        
        $q = $this->getD2EM()->createQuery(
                'SELECT m, mi FROM \\Entities\\Meeting m LEFT JOIN m.MeetingItems mi
                    ORDER BY m.date DESC, mi.other_content ASC'
        );
        
        if( $limit && $limit > 0 )
            $q->setMaxResults( $limit );
        
        $this->view->entries = $q->execute();
        $this->view->simple  = true;

        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
        if( $this->getParam( 'nostyle', false ) )
            $this->view->display( 'meeting/simple2.phtml' );
        else
            $this->view->display( 'meeting/simple.phtml' );
    }
}

