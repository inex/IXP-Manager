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
 *  @package IXP_Form
 */
class IXP_Form extends Twitter_Form
{
    use OSS_Form_Trait;
    use OSS_Form_Trait_CancelLocation;
    use OSS_Form_Trait_IsEdit;
    use OSS_Form_Trait_GenericElements;
    use OSS_Form_Trait_InsertElementFns;
    // use OSS_Form_Trait_Doctrine1Mapping;
    use OSS_Form_Trait_Doctrine2;
    
    /**
     * A list of elements we should not update on an edit
     * if the submitted data is an empty string.
     * @var array
     */
    public $onEditSkipIfBlank = null;
    
    public function __construct( $options = null )
    {
        $this->setAttrib( 'accept-charset', 'UTF-8' );
        $this->setMethod( 'post' );
        $this->setAttrib( "horizontal", true );
        
        $this->onEditSkipIfBlank = array();
        
        $this->addElementPrefixPath( 'OSS_Filter',   'OSS/Filter/',   'filter' );
        $this->addElementPrefixPath( 'OSS_Validate', 'OSS/Validate/', 'validate' );
        
        if( method_exists( $this, 'initialiseTraits' ) )
            $this->initialiseTraits( $options );
        
        parent::__construct( $options );
    }
    
}

