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

class CustKitController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Custkit';
        $this->frontend['name']            = 'CustKit';
        $this->frontend['pageTitle']       = 'Customer Kit';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'name', 'custid', 'cabinetid' ),

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

            'cabinetid' => array(
                'type' => 'hasOne',
                'model' => 'Cabinet',
                'controller' => 'cabinet',
                'field' => 'name',
                'label' => 'Cabinet',
                'sortable' => true
            ),

            'descr' => array(
                'label' => 'Description',
                'sortable' => false
            )

        );

        parent::feInit();
    }

}

?>