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
class IXP_Form_SubForm_PatchPanelAutoGen extends IXP_Form_SubForm
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
                array( 'ViewScript', array( 'viewScript' => 'patch-panel/form/autogen.tpl' ) )
            )
        );

        ////////////////////////////////////////////////
        // Create and configure elements
        ////////////////////////////////////////////////

        $autogen = $this->createElement( 'checkbox', 'cb_autogen' );
        $autogen->setLabel( 'Autogenerate ports for this patch panel?' )
                ->setAttrib( 'id', 'auto_gen_ports_cb' );

        $this->addElement( $autogen );

        $num_ports = $this->createElement( 'text', 'num_ports' );
        $num_ports->setAttrib('size', 4 )
            ->setAttrib( 'maxlength', 3 )
            ->setLabel( 'Number of Ports' )
            ->setErrorMessages( array( 'You must provide a numeric number of ports between 1 and 48' ) );

        $this->addElement( $num_ports );

        $edit = $this->createElement( 'checkbox', 'edit' );
        $edit->setLabel( 'Edit before committing to database?' );

        $this->addElement( $edit );

        $this->setElementDecorators(
            array(
                'ViewHelper'
            )
        );

    }

}

?>