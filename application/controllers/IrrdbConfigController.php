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

class IrrdbConfigController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'source';
        $this->frontend['model']           = 'Irrdbconfig';
        $this->frontend['name']            = 'IrrdbConfig';
        $this->frontend['pageTitle']       = 'IRRDB Configuration';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'host', 'protocol', 'source' ),

            'viewPanelRows'  => array( 'id', 'host', 'protocol', 'source', 'notes' ),

            'sortDefaults' => array(
                'column' => 'source',
                'order'  => 'asc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),

            'host' => array(
                'label' => 'Host',
                'sortable' => 'true',
            ),

            'protocol' => array(
                'label' => 'Protocol',
                'sortable' => 'true',
            ),

            'source' => array(
                'label' => 'Source',
                'sortable' => 'true',
            ),

            'notes' => array(
                'label' => 'Notes'
            )

        );

        parent::feInit();
    }

}

?>