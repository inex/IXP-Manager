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
 *
 * @package IXP_Form
 * @subpackage SubForm
 *
 */
class IXP_Form_SubForm_AddCancel extends IXP_Form_SubForm
{
    /**
     *
     *
     */
    public function __construct( $options = null )
    {
        parent::__construct( $options );

        $this->setDecorators(
            array(
                array( 'ViewScript', array( 'viewScript' => 'form/add-cancel.tpl' ) )
            )
        );


        $this->setElementDecorators(
            array(
                'ViewHelper'
            )
        );

    }

}

?>