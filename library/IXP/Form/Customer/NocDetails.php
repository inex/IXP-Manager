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
 * Form: editing NOC details
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Customer_NocDetails extends IXP_Form
{

    public function init()
    {
        $nocphone = $this->createElement( 'text', 'nocphone' );
        $nocphone->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Phone' )
            ->setAttrib( 'placeholder', '+353 1 123 4567' )
            ->setAttrib( 'class', 'span4' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocphone );

        $noc24hphone = $this->createElement( 'text', 'noc24hphone' );
        $noc24hphone->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', '+353 86 876 5432' )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( '24h Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $noc24hphone );

        $nocfax = $this->createElement( 'text', 'nocfax' );
        $nocfax->addValidator( 'stringLength', false, array( 0, 40, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Fax' )
            ->setAttrib( 'placeholder', '+353 1 765 4321' )
            ->setAttrib( 'class', 'span4' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocfax );

        $nocemail = $this->createElement( 'text', 'nocemail' );
        $nocemail->addValidator('emailAddress' )
            ->addValidator( 'stringLength', false, array( 0, 40, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'placeholder', 'noc@example.com' )
            ->addFilter( 'StripTags' )
            ->setLabel( 'E-Mail' );
        $this->addElement( $nocemail );

        $nochours = $this->createElement( 'select', 'nochours' );
        $nochours->setMultiOptions( [ '0' => '' ] + \Entities\Customer::$NOC_HOURS )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Hours' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select span12' )
            ->addFilter( 'StripTags' )
            ->setAttrib( 'chzn-fix-width', '1' );
        $this->addElement( $nochours );


        $nocwww = $this->createElement( 'text', 'nocwww' );
        $nocwww->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Website' )
            ->setAttrib( 'placeholder', 'http://www.noc.example.com/' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocwww );

        $this->addDisplayGroup(
            array( 'nocphone', 'noc24hphone', 'nocfax', 'nocemail', 'nochours', 'nocwww' ),
        	'nocDisplayGroup'
        );
        $this->getDisplayGroup( 'nocDisplayGroup' )->setLegend( 'NOC Details' );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Update' ) ) );
    }

}
