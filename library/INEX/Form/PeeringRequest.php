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
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 *
 * @package INEX_Form
 * @subpackage Customer
 */
class INEX_Form_PeeringRequest extends INEX_Form
{

    public function __construct( $options = null, $isEdit = false, $cancelLocation = null )
    {
        parent::__construct( $options );

        $this->setMethod( 'post' )
            ->setAttrib( 'id', 'peering-request-form' )
            ->setAttrib( 'name', 'peering-request-form' );
        

        $this->setDecorators(
            array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => 'peering-manager/peering-request-form.tpl'
                    )
                )
            )
        );
        

        $custid = $this->createElement( 'hidden', 'custid' )
            ->setAttrib( 'id', 'peering-request-form-custid' );
        $this->addElement( $custid );
        
        $to = $this->createElement( 'text', 'to' );
        $to->addValidator( 'stringLength', false, array( 1, 4096 ) )
            ->setRequired( true )
            ->setLabel( 'To' )
            ->setAttrib( 'class', 'span5' )
            ->setAttrib( 'readonly', 'readonly' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $to );

        $cc = $this->createElement( 'text', 'cc' );
        $cc->addValidator( 'stringLength', false, array( 1, 4096 ) )
            ->setRequired( false )
            ->setLabel( 'CC' )
            ->setAttrib( 'class', 'span5' )
            ->setAttrib( 'readonly', 'readonly' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $cc );

        $bcc = $this->createElement( 'text', 'bcc' );
        $bcc->addValidator( 'stringLength', false, array( 1, 4096 ) )
            ->setRequired( false )
            ->setLabel( 'BCC' )
            ->setAttrib( 'class', 'span5' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $bcc );

        $subject = $this->createElement( 'text', 'subject' );
        $subject->addValidator( 'stringLength', false, array( 1, 4096 ) )
            ->setRequired( true )
            ->setLabel( 'Subject' )
            ->setAttrib( 'class', 'span5' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $subject );


        $message = $this->createElement( 'textarea', 'message',
            array(
                'cols' => 50,
                'rows' => 8
            )
        );

        $message->addValidator( 'stringLength', false, array( 1, 40960 ) )
            ->setRequired( true )
            ->setLabel( 'Message' )
            ->setAttrib( 'class', 'span5 mono' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $message );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Send' ) );
    }

}
