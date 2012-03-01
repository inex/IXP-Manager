<?php

/*
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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
 * @package INEX_Form
 */
class INEX_Form_Customer_NocDetails extends INEX_Form
{

    public function __construct( $options = null, $isEdit = false )
    {
        parent::__construct( $options );

        $nocphone = $this->createElement( 'text', 'nocphone' );
        $nocphone->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'NOC Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocphone );

        $noc24hphone = $this->createElement( 'text', 'noc24hphone' );
        $noc24hphone->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'NOC 24h Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $noc24hphone );

        $nocfax = $this->createElement( 'text', 'nocfax' );
        $nocfax->addValidator( 'stringLength', false, array( 0, 40 ) )
            ->setRequired( false )
            ->setLabel( 'NOC Fax' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocfax );

        $nocemail = $this->createElement( 'text', 'nocemail' );
        $nocemail->addValidator('emailAddress' )
            ->addValidator( 'stringLength', false, array( 0, 40 ) )
            ->setRequired( false )
            ->setLabel( 'NOC E-Mail' );
        $this->addElement( $nocemail );

        $nochours = $this->createElement( 'text', 'nochours' );
        $nochours->addValidator( 'stringLength', false, array( 0, 40 ) )
            ->setRequired( false )
            ->setLabel( 'NOC Hours' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nochours );

        $nocwww = $this->createElement( 'text', 'nocwww' );
        $nocwww->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'NOC WWW' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocwww );

        $this->addDisplayGroup(
            array( 'nocphone', 'noc24hphone', 'nocfax', 'nocemail', 'nochours', 'nocwww' ),
        	'nocDisplayGroup'
        );
        $this->getDisplayGroup( 'nocDisplayGroup' )->setLegend( 'NOC Details' );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Update' ) );
    }

}

