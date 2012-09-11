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


/**
 * Controller: Manage locations (data centres)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LocationController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Location';
        $this->frontend['name']            = 'Location';
        $this->frontend['pageTitle']       = 'Locations';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'name', 'shortname' ),

            'viewPanelRows'  => array( 'name', 'shortname', 'address', 'nocphone', 'nocfax', 'nocemail', 'officephone', 'officefax', 'officeemail', 'notes' ),
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

            'shortname' => array(
                'label' => 'Short Name',
                'sortable' => true
            ),

            'address' => array(
                'label' => 'Address'
            ),

            'nocphone' => array(
                'label' => 'NOC Phone'
            ),

            'nocfax' => array(
                'label' => 'NOC Fax'
            ),

            'nocemail' => array(
                'label' => 'NOC Email'
            ),

            'officephone' => array(
                'label' => 'Office Phone'
            ),

            'officefax' => array(
                'label' => 'Office Fax'
            ),

            'officeemail' => array(
                'label' => 'Office e-mail'
            ),

            'notes' => array(
                'label' => 'Notes'
            )
        );


        parent::feInit();
    }

}

