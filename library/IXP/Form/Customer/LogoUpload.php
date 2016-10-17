<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Limited.
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
 * Form: logo upload
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 *
 */
class IXP_Form_Customer_LogoUpload extends IXP_Form
{

    public function init()
    {
        $this->setAttrib('enctype', 'multipart/form-data');

        $logo = $this->createElement( 'file', 'logo' )
            ->setLabel('Upload a PNG logo:')
            ->setDestination( APPLICATION_PATH . '/../var/uploads')
            ->addValidator('Count', false, 1)
            // limit to 1MB
            ->addValidator('Size', false, 1024000)
            // PNGs
            ->addValidator('Extension', false, 'png')
            ->setRequired(true);

        $this->addElement( $logo );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Upload' ) ) );
        $this->addElement( $this->createCancelElement( 'cancel', OSS_Utils::genUrl() ) );
    }
}
