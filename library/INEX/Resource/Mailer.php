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

/**
 * INEX Internet Exchange Point Application :: INEX IXP
 *
 * Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 * All rights reserved.
 *
 * @category INEX
 * @version $Id: Mailer.php 447 2011-05-26 13:53:52Z barryo $
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 *
 */


/**
 * Class to instantiate Mailer
 *
 * @category INEX
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 */
class INEX_Resource_Mailer extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Holds the Logger instance
     *
     * @var
     */
    protected $_mailer;


    public function init()
    {
        // Return logger so bootstrap will store it in the registry
        return $this->getMailer();
    }


    public function getMailer()
    {
        if( null === $this->_mailer )
        {
            $options = $this->getOptions();

            if( count( $options ) )
            {
                if( isset( $options['auth'] ) )
                {
                    $config = array(
                    	'auth' => $options['auth'],
                		'username' => $options['username'],
                		'password' => $options['password']
                    );
                }
                else
                $config = array();

                $transport = new Zend_Mail_Transport_Smtp( $options['smtphost'], $config );
                Zend_Mail::setDefaultTransport( $transport );

                $this->_mailer = $transport;
            }
        }

        return $this->_mailer;
    }


}
