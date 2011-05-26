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

class PatchPanelPortController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'PatchPanelPort';
        $this->frontend['name']            = 'PatchPanelPort';
        $this->frontend['pageTitle']       = 'Patch Panel Ports';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'patch_panel_id', 'port', 'side', 'type', 'colo_ref', 'cable_type', 'duplex' ),

            'viewPanelRows' => array( 'id', 'patch_panel_id', 'port', 'side', 'type', 'colo_ref', 'cable_type', 'duplex' ),

            'viewPanelTitle' => 'colo_ref',

            'sortDefaults' => array(
                'column' => 'colo_ref',
                'order'  => 'asc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),

            'patch_panel_id' => array(
                'type' => 'hasOne',
                'model' => 'PatchPanel',
                'controller' => 'patch-panel',
                'field' => 'name',
                'label' => 'Patch Panel',
                'sortable' => true
            ),

            'colo_ref' => array(
                'label' => 'Co-lo Ref',
                'sortable' => true
            ),

            'side' => array(
                'label' => 'Side',
                'sortable' => true,
                'type' => 'xlate',
                'xlator' => PatchPanelPort::$SIDES
            ),

            'cable_type' => array(
                'label' => 'Cable Type',
                'sortable' => true,
                'type' => 'xlate',
                'xlator' => PatchPanelPort::$CABLES_TYPES
            ),

            'type' => array(
                'label' => 'Interface',
                'sortable' => true,
                'type' => 'xlate',
                'xlator' => PatchPanelPort::$INTERFACE_TYPES
            ),

            'duplex' => array(
                'label' => 'Duplexed?',
                'sortable' => true
            )

        );

        parent::feInit();
    }

}

?>