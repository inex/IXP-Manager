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
 * Form: send email to a customer
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 *
 */
class IXP_Form_Customer_SendEmail extends IXP_Form
{

    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'customer/forms/send-email.phtml' ] ] ] );

        $to = $this->createElement( 'text', 'to', [ 'size' => 100 ] );
        $to->addValidator( 'stringLength', false, array( 1, 4096, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'To' )
            ->setAttrib( 'class', 'span9' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $to );

        $cc = $this->createElement( 'text', 'cc', [ 'size' => 100 ] );
        $cc->addValidator( 'stringLength', false, array( 1, 4096, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'CC' )
            ->setAttrib( 'class', 'span9' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $cc );

        $bcc = $this->createElement( 'text', 'bcc', [ 'size' => 100 ] );
        $bcc->addValidator( 'stringLength', false, array( 1, 4096, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'BCC' )
            ->setAttrib( 'class', 'span9' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $bcc );

        $subject = $this->createElement( 'text', 'subject', [ 'size' => 100 ] );
        $subject->addValidator( 'stringLength', false, array( 1, 4096, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Subject' )
            ->setAttrib( 'class', 'span9' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $subject );


        $message = $this->createElement( 'textarea', 'message', [ 'cols' => 80, 'rows' => 20 ] );
        $message->addValidator( 'stringLength', false, array( 1, 40960, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Message' )
            ->setAttrib( 'class', 'span9' )
            ->setAttrib( 'style', 'font-family: Menlo, Monaco, "Courier New", monospace;' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $message );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }
}
