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
 *  @package INEX_Form
 */
class INEX_Form extends Twitter_Form
{
    /**
     * Where to go it the add / edit is cancelled.
     * @var string Where to go it the add / edit is cancelled.
     */
    public $cancelLocation = '';
    
    
    /**
     * A list of elements we should not update on an edit
     * if the submitted data is an empty string.
     * @var array
     */
    public $onEditSkipIfBlank = null;
    
    /**
     * If true, we are editing an existing object. Otherwise we are adding a new object.
     * @var bool If true, we are editing an existing object. Otherwise we are adding a new object.
     */
    public $isEdit = false;

    public function __construct( $options = null, $isEdit = false, $cancelLocation = '' )
    {
        $this->isEdit = $isEdit;
        
        $this->cancelLocation = $cancelLocation;
        
        $this->setAttrib( 'accept-charset', 'UTF-8' );
        $this->setMethod( 'post' );
        $this->setAttrib( "horizontal", true );
        
        $this->onEditSkipIfBlank = array();
        
        $this->addElementPrefixPath( 'OSS_Filter',   'OSS/Filter/',   'filter' );
        $this->addElementPrefixPath( 'OSS_Validate', 'OSS/Validate/', 'validate' );
        
        parent::__construct( $options );
    }
    
    /**
     * A utility function for creating a standard cancel button for forms.
     *
     * @param string $name The element name
     * @param string $cancelLocation The cancel location URL
     * @return Zend_Form_Element_Submit The cancel element
     */
    public function createCancelElement( $name = 'cancel', $cancelLocation = null )
    {
        if( $cancelLocation === null )
            $cancelLocation = $this->cancelLocation;
        
        $cancel = new OSS_Form_Element_Buttonlink( $name );
        
        return $cancel->setAttrib( 'href', $cancelLocation )
            ->setAttrib( 'label', _( 'Cancel' ) );
    }
}

