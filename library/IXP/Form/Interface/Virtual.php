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
 * Form: adding / editing virtual interfaces
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Interface_Virtual extends IXP_Form
{

    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'virtual-interface/forms/virtual-interface.phtml' ] ] ] );

        $this->addElement( IXP_Form_Customer::getPopulatedSelect( 'custid' ) );
        $this->getElement( 'custid' )->setAttrib( 'class', 'chzn-select span8' );

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Virtual Interface Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );


        $descr = $this->createElement( 'text', 'description' );
        $descr->setLabel( 'Description' )
            ->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->addFilter( 'StringTrim' );
        $this->addElement( $descr );


        $channel = $this->createElement( 'text', 'channelgroup' );
        $channel->addValidator( 'int' )
            ->setLabel( 'Channel Group Number' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $channel );

        $mtu = $this->createElement( 'text', 'mtu' );
        $mtu->addValidator( 'int' )
            ->setLabel( 'MTU' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $mtu );


        $trunk = $this->createElement( 'checkbox', 'trunk' );
        $trunk->setLabel( 'Is 802.1q Trunk?' )
            ->setCheckedValue( '1' );
        $this->addElement( $trunk );


        $this->addDisplayGroup(
            [ 'custid', 'name', 'description', 'channelgroup', 'mtu', 'trunk' ],
            'virtualInterfaceDisplayGroup'
        );

        $this->getDisplayGroup( 'virtualInterfaceDisplayGroup' )->setLegend( 'Customer Connection Details' );


        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }

}
