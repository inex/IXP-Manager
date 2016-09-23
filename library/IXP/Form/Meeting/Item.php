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
 * Form: adding / editing meeting presentations
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Meeting_Item extends IXP_Form
{
    public function init()
    {
        $this->addElement( IXP_Form_Meeting::getPopulatedSelect( 'meeting_id' ) );
        $this->getElement( 'meeting_id' )->setAttrib( 'class', 'chzn-select span6' );
        
        $title = $this->createElement( 'text', 'title', array( 'size' => '100' ) );
        $title->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Title' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $title );

        $name = $this->createElement( 'text', 'name', array( 'size' => '100' ) );
        $name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( 'Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );

        $role = $this->createElement( 'text', 'role', array( 'size' => '100' ) );
        $role->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( 'Role' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $role );

        $email = $this->createElement( 'text', 'email', array( 'size' => '100' ) );
        $email->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'E-Mail' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $email );

        $company = $this->createElement( 'text', 'company', array( 'size' => '100' ) );
        $company->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Company' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $company );

        $company_url = $this->createElement( 'text', 'company_url', array( 'size' => '100' ) );
        $company_url->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Company URL' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $company_url );


        $summary = $this->createElement( 'textarea', 'summary' );
        $summary->setLabel( 'Summary' )
            ->setRequired( false )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'rows', 10 );
        $this->addElement( $summary );

        $presentation = $this->createElement( 'file', 'presentation' );
        $presentation->setLabel( 'Attach Presentation' )
            ->setAttrib( 'class', 'span6' )
            ->setRequired( false );
        $this->addElement( $presentation );

        $video_url = $this->createElement( 'text', 'video_url', array( 'size' => '100' ) );
        $video_url->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Video' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $video_url );


        $other_content = $this->createElement( 'checkbox', 'other_content' );
        $other_content->setLabel( 'Other Content?' )
            ->setRequired( false );
        $this->addElement( $other_content );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
        
        // we shouldn't update the presentation file on an edit if it's blank
        $this->onEditSkipIfBlank = array( 'presentation' );

    }

}

