<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Form: adding / editing customer notes
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Customer_Notes extends IXP_Form
{
    public function init()
    {
        $custid = $this->createElement( 'hidden', 'custid' );
        $this->addElement( $custid );

        $noteid = $this->createElement( 'hidden', 'noteid' );
        $this->addElement( $noteid );

        $title = $this->createElement( 'text', 'title' );
        $title->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Title' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $title );

        $note = $this->createElement( 'textarea', 'note' );
        $note->setRequired( false )
            ->setLabel( 'Note' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $note );
        
        $public = $this->createElement( 'checkbox', 'public' );
        $public->setLabel( 'Make note visible to customer' )
            ->setCheckedValue( 'makePublic' );
        $this->addElement( $public );
        
        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }
    
}
