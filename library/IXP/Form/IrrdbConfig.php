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
 * Form: adding / editing IRRDB sources
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_IrrdbConfig extends IXP_Form
{
    public function init()
    {
        $host = $this->createElement( 'text', 'host' );
        $host->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Host' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $host );

        $protocol = $this->createElement( 'text', 'protocol' );
        $protocol->addValidator( 'stringLength', false, array( 1, 10, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Protocol' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $protocol );

        $source = $this->createElement( 'text', 'source' );
        $source->addValidator( 'stringLength', false, array( 1, 50, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Source' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $source );

        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
        ->setRequired( false )
        ->addFilter( new OSS_Filter_StripSlashes() )
        ->setAttrib( 'cols', 60 )
        ->setAttrib( 'class', 'span3' )
        ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );
        
        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }
    
    /**
     * Create a SELECT / dropdown element of all IRRDB names indexed by their id.
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'irrdb' )
    {
        $e = new Zend_Form_Element_Select( $name );
    
        $maxId = self::populateSelectFromDatabase( $e, '\\Entities\\IRRDBConfig', 'id', 'source', 'source', 'ASC' );
    
        $e->setRegisterInArrayValidator( true )
            ->setLabel( _( 'IRRDB Source' ) )
            ->setAttrib( 'class', 'span8 chzn-select' )
            ->setErrorMessages( array( _( 'Please select an IRRDB source' ) ) );
    
        return $e;
    }
    
}
