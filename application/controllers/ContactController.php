<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class ContactController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Contact';
        $this->frontend['name']            = 'Contact';
        $this->frontend['pageTitle']       = 'Contacts';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'name', 'custid', 'email', 'phone', 'mobile' ),

            'viewPanelRows'  => array(  'name', 'custid', 'email', 'phone', 'mobile',
                'facilityaccess', 'mayauthorize'
            ),

            'viewPanelTitle' => 'name',

            'sortDefaults' => array(
                'column' => 'name',
                'order'  => 'desc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),


            'name' => array(
                'label' => 'Name',
                'sortable' => 'true',
            ),

            'custid' => array(
                'type' => 'hasOne',
                'model' => 'Cust',
                'controller' => 'customer',
                'field' => 'name',
                'label' => 'Customer',
                'sortable' => true
            ),

            'email' => array(
                'label' => 'E-mail',
                'sortable' => true
            ),

            'phone' => array(
                'label' => 'Phone',
            ),

            'mobile' => array(
                'label' => 'Mobile',
            ),

            'facilityaccess' => array(
                'label' => 'Facility Access',
            ),

            'mayauthorize' => array(
                'label' => 'May Authorise',
            )
        );

        parent::feInit();
    }

}

?>