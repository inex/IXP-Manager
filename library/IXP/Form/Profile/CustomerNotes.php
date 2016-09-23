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
 * A form to allow a user to change his customer notes notifications
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     Nerijus Barauskas <nerijus@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Profile_CustomerNotes extends IXP_Form
{
    public function init()
    {
        $this->setAttrib( 'id', 'customer_notes' )
            ->setAttrib( 'name', 'customer_notes' )
            ->setAction( OSS_Utils::genUrl( 'profile', 'update-customer-notes' ) );

        $notify = $this->createElement( 'radio','notify' );
        $notify->addMultiOptions([
                'none'     => 'Disable all email notifications',
                'default'  => 'Email me on changes to only watched customers and notes',
                'all'      => 'Email me on any change to any customer note'
            ])
            ->setValue( 'default' );
        $this->addElement( $notify );
        
        $this->addElement( OSS_Form::createSubmitElement( 'submit', _( 'Change Notification Preference' ) ) );
    }
}

