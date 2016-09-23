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
 * Form: adding / editing VLANs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_VLAN extends IXP_Form
{
    public function init()
    {

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );

        $number = $this->createElement( 'text', 'number' );
        $number->addValidator( 'int' )
            ->addValidator( 'between', false, array( 1, 4096 ) )
            ->setRequired( true )
            ->setLabel( 'Tag' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $number );

        $infrastructure = IXP_Form_Infrastructure::getPopulatedSelect( 'infrastructure' )
            ->setRequired( true );

        $infrastructure->addValidator( 'between', false, array( 1, $infrastructure->getAttrib( 'data-maxId' ) ) )
            ->setAttrib( 'class', 'chzn-select' );
        $this->addElement( $infrastructure );

        $rcvrfname = $this->createElement( 'text', 'rcvrfname' );
        $rcvrfname->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setLabel( 'RC VRF Name' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $rcvrfname );

        $private = $this->createElement( 'checkbox', 'private' );
        $private->setLabel( 'Private VLAN between a subset of members' )
            ->setCheckedValue( '1' );
        $this->addElement( $private );

        $peering_matrix = $this->createElement( 'checkbox', 'peering_matrix' );
        $peering_matrix->setLabel( 'Include VLAN in the peering matrix (see notes below)' )
            ->setCheckedValue( '1' );
        $this->addElement( $peering_matrix );

        $peering_manager = $this->createElement( 'checkbox', 'peering_manager' );
        $peering_manager->setLabel( 'Include VLAN in the peering manager (see notes below)' )
            ->setCheckedValue( '1' );
        $this->addElement( $peering_manager );

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
     * Create a SELECT / dropdown element of all VLAN names indexed by their id.
     *
     * @param string $name The element name
     * @param bool $publicOnly If true, exclude private VLANs from the dropdown
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'vlanid', $publicOnly = false )
    {
        $vlan = new Zend_Form_Element_Select( $name );


        $qb = Zend_Registry::get( 'd2em' )['default']->createQueryBuilder()
            ->select( 'v.id AS id, v.name AS name' )
            ->from( '\\Entities\\Vlan', 'v' )
            ->orderBy( "v.name", 'ASC' );

        if( $publicOnly )
            $qb->where( "v.private = 0" );

        $maxId = self::populateSelectFromDatabaseQuery( $qb->getQuery(), $vlan, '\\Entities\\Vlan', 'id', 'name', 'name', 'ASC' );

        $vlan->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'VLAN' ) )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a VLAN' ) ) );

        return $vlan;
    }

}
