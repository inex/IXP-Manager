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


/**age cabinets (racks)
 * Controller: Man
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CabinetController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Cabinet';
        $this->frontend['name']            = 'Cabinet';
        $this->frontend['pageTitle']       = 'Cabinets';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'name', 'cololocation', 'height', 'type', 'location' ),

            'viewPanelRows'  => array( 'name', 'cololocation', 'height', 'type', 'location' ),
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

            'cololocation' => array(
                'label' => 'Colo Location',
                'sortable' => true
            ),

            'height' => array(
                'label' => 'Height',
                'sortable' => true
            ),

            'type' => array(
                'label' => 'Type',
                'sortable' => true
            ),

            'location' => array(
                'type' => 'hasOne',
                'model' => 'Location',
                'controller' => 'location',
                'field' => 'name',
                'label' => 'Location',
                'sortable' => true
            )
        );

        parent::feInit();
    }

}

