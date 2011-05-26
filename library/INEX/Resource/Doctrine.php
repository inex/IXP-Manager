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
 * @version $Id: Doctrine.php 447 2011-05-26 13:53:52Z barryo $
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 *
 */


/**
 * Class to instantiate Doctrine
 *
 * @category INEX
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 */
class INEX_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Holds the Doctrine instance
     *
     * @var
     */
    protected $_doctrine;


    public function init()
    {
        // Return Doctrine so bootstrap will store it in the registry
        return $this->getDoctrine();
    }


    public function getDoctrine()
    {
        if( null === $this->_doctrine )
        {
            // Get Doctrine configuration options from the application.ini file
            $doctrineConfig = $this->getOptions();

            require_once 'Doctrine.php';

            $loader = Zend_Loader_Autoloader::getInstance();
            $loader->pushAutoloader( array( 'Doctrine', 'autoload' ) );

            $manager = Doctrine_Manager::getInstance();

            $cacheDriver = new Doctrine_Cache_Apc();
            $manager->setAttribute( Doctrine_Core::ATTR_QUERY_CACHE,  $cacheDriver );

            $manager->setAttribute( Doctrine_Core::ATTR_RESULT_CACHE, $cacheDriver );
            $manager->setAttribute( Doctrine_Core::ATTR_RESULT_CACHE_LIFESPAN, 3600 * 24 );

            $manager->setAttribute( Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE );
            $manager->setAttribute( Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true );

            spl_autoload_register( array( 'Doctrine_Core', 'modelsAutoload' ) );
            Doctrine_Core::loadModels( $doctrineConfig['models_path'], Doctrine_Core::MODEL_LOADING_CONSERVATIVE );

            $manager->openConnection( $doctrineConfig['connection_string'] );

            $manager->connection()->setCollate( 'utf8_unicode_ci' );
            $manager->connection()->setCharset( 'utf8' );

            $this->_doctrine = $manager;
        }

        return $this->_doctrine;
    }

    /**
     * Set the classes $_doctrine member
     *
     * @param $doctrine The object to set
     */
    public function setDoctrine( $doctrine )
    {
        $this->_doctrine = $doctrine;
    }


}
