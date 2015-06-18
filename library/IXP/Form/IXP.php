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
 * Form: editing IXP details
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     Nerijus Barauskas <nerijus@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_IXP extends IXP_Form
{
    public function init()
    {

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name  );

        $shortname = $this->createElement( 'text', 'shortname' );
        $shortname->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->addValidator( 'alnum' )
            ->setRequired( true )
            ->setLabel( 'Shortname' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $shortname  );

        $address1 = $this->createElement( 'text', 'address1' );
        $address1->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Address' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $address1 );

        $address2 = $this->createElement( 'text', 'address2' );
        $address2->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $address2 );

        $address3 = $this->createElement( 'text', 'address3' );
        $address3->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $address3 );

        $address4 = $this->createElement( 'text', 'address4' );
        $address4->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $address4 );

        $country = $this->createElement( 'select', 'country' );
        $country->setMultiOptions( [ '' => '' ] + OSS_Countries::getCountriesArray() )
            ->setRegisterInArrayValidator( true )
            ->setValue( 'IE' )
            ->setLabel( 'Country' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select' )
            ->setAttrib( 'chzn-fix-width', '1' );
        $this->addElement( $country );

        $mrtgPath = $this->createElement( 'text', 'mrtg_path' );
        $mrtgPath->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'MRTG Path' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $mrtgPath  );
        
        $p2pPath = $this->createElement( 'text', 'mrtg_p2p_path' );
        $p2pPath->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'MRTG P2P Path' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $p2pPath  );
        
        $this->addElement( self::createAggregateGraphNameElement() );
        
        $smokeping = $this->createElement( 'text', 'smokeping' );
        $smokeping->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Smokeping URL' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $smokeping  );
        
        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }

    /**
     * Create a SELECT / dropdown element of all IXP names indexed by their id.
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'ixpid' )
    {
        $sw = new Zend_Form_Element_Select( $name );

        $maxId = self::populateSelectFromDatabase( $sw, '\\Entities\\IXP', 'id', 'name', 'name', 'ASC' );
    
        $sw->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'IXP' ) )
            ->setAttrib( 'class', 'chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( [ 'Please select an IXP' ] );
    
        return $sw;
    }
    
    /**
     * Create a 'Aggregate Graph Name element used by the IXP and infrastructure forms
     *
     * @param string $name The element name (defaults to `aggregate_graph_name`)
     * @return Zend_Form_Element_Text
     */
    public static function createAggregateGraphNameElement( $name = 'aggregate_graph_name' )
    {
        $agn = new Zend_Form_Element_Text( $name );
        
        return $agn->setRequired( false )
            ->setLabel( 'Aggregate Graph Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StringToLower' )
            ->addFilter( new OSS_Filter_StripSlashes() );
    }

}

