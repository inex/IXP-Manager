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
 * @package IXP_Form
 * @subpackage Customer
 */
class IXP_Form_Provision_InterfaceEmail extends Zend_Form
{

    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options );

        $this->setAttrib( 'accept-charset', 'UTF-8' );
        $this->setAttrib( 'class', 'form' );

        $this->setElementDecorators(
            array(
                'ViewHelper',
                'Errors',
                array( 'HtmlTag', array( 'tag' => 'dd' ) ),
                array( 'Label', array( 'tag' => 'dt' ) ),
            )
        );


        $to = $this->createElement( 'text', 'to',
            array(
                'size' => 100
            )
        );
        $to->addValidator( 'stringLength', false, array( 1, 4096, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'To' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new IXP_Filter_StripSlashes() );

        $this->addElement( $to );

        $cc = $this->createElement( 'text', 'cc',
            array(
                'size' => 100
            )
        );

        $cc->addValidator( 'stringLength', false, array( 1, 4096, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'CC' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new IXP_Filter_StripSlashes() );

        $this->addElement( $cc );

        $bcc = $this->createElement( 'text', 'bcc',
            array(
                'size' => 100
            )
        );

        $bcc->addValidator( 'stringLength', false, array( 1, 4096, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'BCC' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new IXP_Filter_StripSlashes() );

        $this->addElement( $bcc );

        $subject = $this->createElement( 'text', 'subject',
            array(
                'size' => 100
            )
        );

        $subject->addValidator( 'stringLength', false, array( 1, 4096, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Subject' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new IXP_Filter_StripSlashes() );

        $this->addElement( $subject );


        $message = $this->createElement( 'textarea', 'message',
            array(
                'cols' => 100,
                'rows' => 12,
                'style' => 'font-family:monospace;'
            )
        );

        $message->addValidator( 'stringLength', false, array( 1, 40960, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Message' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new IXP_Filter_StripSlashes() );

        $this->addElement( $message );




        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='$cancelLocation'" ) );
        $this->addElement( 'submit', 'commit', array( 'label' => 'Send' ) );
    }

}
