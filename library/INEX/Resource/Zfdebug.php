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
 * @version $Id: Zfdebug.php 447 2011-05-26 13:53:52Z barryo $
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 *
 */


/**
 * Class to instantiate Zfdebug
 *
 * @category INEX
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 */
class INEX_Resource_Zfdebug extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Holds the Logger instance
     *
     * @var
     */
    protected $_zfdebug;


    public function init()
    {
        // Return session so bootstrap will store it in the registry
        return $this->getZfdebug();
    }


    public function getZfdebug()
    {
        if( null === $this->_zfdebug )
        {
            $this->getBootstrap()->bootstrap( 'session' );

            // Get Zfdebug configuration options from the application.ini file
            $zfdebugConfig = $this->getOptions();

            if( $zfdebugConfig['enabled'] )
            {
                $autoloader = Zend_Loader_Autoloader::getInstance();
                $autoloader->registerNamespace('ZFDebug');

                $options = array(
			        'plugins' => $zfdebugConfig['plugins']
                );

                # Instantiate the database adapter and setup the plugin.
                # Alternatively just add the plugin like above and rely on the autodiscovery feature.
                if( $this->getBootstrap()->hasPluginResource( 'db' ) )
                {
                    $this->getBootstrap()->bootstrap('db');
                    $db = $this->getBootstrap()->getPluginResource( 'db' )->getDbAdapter();
                    $options['plugins']['Database']['adapter'] = $db;
                }

                # Setup the cache plugin
                if( $this->getBootstrap()->hasPluginResource( 'cache' ) )
                {
                    $this->getBootstrap()->bootstrap( 'cache' );
                    $cache = $this->getBootstrap()->getPluginResource( 'cache' )->getDbAdapter();
                    $options['plugins']['Cache']['backend'] = $cache->getBackend();
                }

                $this->getBootstrap()->bootstrap( 'INEXAutoLoader' );
                $this->getBootstrap()->bootstrap( 'Doctrine' );
                $options['plugins']['INEX_ZFDebug_Controller_Plugin_Debug_Plugin_Doctrine']['manager']
                    = $this->getBootstrap()->getResource( 'Doctrine' );

                $this->_zfdebug = new ZFDebug_Controller_Plugin_Debug( $options );

                $this->getBootstrap()->bootstrap( 'FrontController' );
                $frontController = $this->getBootstrap()->getResource('FrontController');
                $frontController->registerPlugin($this->_zfdebug);
            }
        }

        return $this->_zfdebug;
    }


}
