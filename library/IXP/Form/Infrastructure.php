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
 * Form: editing IXP details
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     Nerijus Barauskas <nerijus@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Infrastructure extends IXP_Form
{
    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'infrastructure/forms/edit.phtml' ] ] ] );

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name  );

        $shortname = $this->createElement( 'text', 'shortname' );
        $shortname->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Short Name' )
            ->addFilter( 'StringToLower' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $shortname  );

        $isPrimary = $this->createElement( 'checkbox', 'isPrimary' );
        $isPrimary->setLabel( 'This is the <em>primary</em> infrastructure' )
            ->setValue( '0' )
            ->addValidator( 'InArray', false, [ [ 0, 1 ] ] )
            ->addFilter( 'Int' );
        $this->addElement( $isPrimary );

        $this->addElement( IXP_Form_IXP::createAggregateGraphNameElement() );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }

    /**
     * Sets IXP form element to drop down or hidden depends on
     * multi IXP is enabled or not.
     *
     * @param bool $multiIXP Flag if multi ixp mode enabled
     * @return IXP_Form_Infrastructure
     */
    public function setMultiIXP( $multiIXP )
    {
        if( !$multiIXP )
        {
            $ixp = $this->createElement( 'hidden', 'ixp' );
            $ixp->setValue( '1' );
        }
        else
            $ixp = IXP_Form_IXP::getPopulatedSelect( 'ixp' );

        $this->addElement( $ixp  );
        return $this;
    }

    /**
     * Create a SELECT / dropdown element of all infrastructures indexed by their id.
     *
     * Drop down list will be appended like this:
     *  ixp1 - inf1
     *  ixp1 - inf3
     *  ixp2 - inf2
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'ifrastructureid' )
    {
        $sw = new Zend_Form_Element_Select( $name );

        $qb = Zend_Registry::get( 'd2em' )['default']->createQueryBuilder()
            ->select( 'e.id AS id, e.name AS name, ix.shortname AS ixp' )
            ->from( '\\Entities\\Infrastructure', 'e' )
            ->join( 'e.IXP', 'ix' )
            ->add( 'orderBy', "ixp ASC, name ASC" );

        $maxId = self::populateSelectFromDatabaseQuery( $qb->getQuery(), $sw, '\\Entities\\Infrastructure', 'id', [ 'ixp', 'name' ], 'ixp', 'ASC' );

        $sw->setRegisterInArrayValidator( true )
            ->setRequired( false )
            ->setAttrib( 'data-maxId', $maxId )
            ->setLabel( _( 'infrastructure' ) )
            ->setAttrib( 'class', 'chzn-select-deselect' )
            //->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( [ 'Please select an infrastructure' ] );

        return $sw;
    }
}
