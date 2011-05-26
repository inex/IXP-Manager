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

/**
 *
 * @package INEX_Form
 */
class INEX_Form_PatchPanel extends INEX_Form
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false, $cancelLocation = null )
    {
        parent::__construct( $options, $isEdit );

        $this->setSubFormDecorators(
            array(
                'FormElements'
            )
        );

        // Add these sub forms in _AFTER_ setting the global sub form decorators as they define their own decorators during initialisation.
        $this->addSubForm( new INEX_Form_SubForm_PatchPanel(),                  'PatchPanelForm',    0 );
        $this->addSubForm( new INEX_Form_SubForm_PatchPanelAutoGen(),           'AutoGenForm',       1 );

        if( $isEdit )
            $this->addSubForm( new INEX_Form_SubForm_AddEditCancel( null, $isEdit), 'AddEditCancelForm', 2 );
        else
            $this->addSubForm( new INEX_Form_SubForm_AddCancel(), 'AddCancelForm', 2 );
    }

}

