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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

/**
 *
 * @package INEX_Form
 */
class INEX_Form_MeetingItem extends INEX_Form
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false )
    {
        parent::__construct( $options );



        ////////////////////////////////////////////////
        // Create and configure title element
        ////////////////////////////////////////////////

        $meeting = $this->createElement( 'select', 'meeting_id' );

        $maxMeetingId = $this->createSelectFromDatabaseTable( $meeting, 'Meeting', 'id',
            array( 'title', 'date' ),
            'date'
        );

        $meeting->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( 'Meeting' )
            ->addValidator( 'between', false, array( 1, $maxMeetingId ) )
            ->setErrorMessages( array( 'Please select a meeting' ) );

        $this->addElement( $meeting );


        $title = $this->createElement( 'text', 'title', array( 'size' => '100' ) );
        $title->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Title' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $title );

        $name = $this->createElement( 'text', 'name', array( 'size' => '100' ) );
        $name->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $name );

        $role = $this->createElement( 'text', 'role', array( 'size' => '100' ) );
        $role->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Role' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $role );

        $email = $this->createElement( 'text', 'email', array( 'size' => '100' ) );
        $email->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( false )
            ->setLabel( 'E-Mail' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $email );

        $company = $this->createElement( 'text', 'company', array( 'size' => '100' ) );
        $company->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Company' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $company );

        $company_url = $this->createElement( 'text', 'company_url', array( 'size' => '100' ) );
        $company_url->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Company URL' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $company_url );


        $summary = $this->createElement( 'textarea', 'summary' );
        $summary->setLabel( 'Summary' )
            ->setRequired( false )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->setAttrib( 'cols', 100 )
            ->setAttrib( 'rows', 10 );
        $this->addElement( $summary );

        $presentation = $this->createElement( 'file', 'presentation' );
        $presentation->setLabel( 'Attach Presentation' )
            ->setRequired( false );
        $this->addElement( $presentation );

        $video_url = $this->createElement( 'text', 'video_url', array( 'size' => '100' ) );
        $video_url->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Video' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $video_url );


        $other_content = $this->createElement( 'checkbox', 'other_content' );
        $other_content->setLabel( 'Other Content?' )
            ->setRequired( false );
        $this->addElement( $other_content );

        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='" . Zend_Controller_Front::getInstance()->getBaseUrl() . "/customer/list'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

        // we shouldn't update the presentation file on an edit if it's blank
        $this->onEditSkipIfBlank = array( 'presentation' );

    }

}

?>