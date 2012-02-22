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
 * @version $Id: View.php 447 2011-05-26 13:53:52Z barryo $
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 *
 */
/**
 * Class to instantiate View
 *
 * @category INEX
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 */
class INEX_Resource_View extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * Holds the View instance
     * 
     * @var 
     */
    protected $_view;

    public function init()
    {
        // Return view so bootstrap will store it in the registry
        return $this->getView();
    }

    public function getView()
    {
        if( null === $this->_view )
        {
            // Get session configuration options from the application.ini file
            $options = $this->getOptions();

            require_once( APPLICATION_PATH . '/../library/Smarty/Smarty.class.php' );

            // Initialize view
            $view = new INEX_View_Smarty( 
                $options['templates'], 
                array( 
                	'cache_dir'   => $options['cache'], 
                	'config_dir'  => $options['config'], 
                	'compile_dir' => $options['compiled'], 
                	'plugins_dir' => $options['plugins'] 
                ) 
            );
            
            if( isset( $options['skin'] ) )
            {
                $view->setSkin( $options['skin'] );
                $view->assign( '___SKIN', $options['skin'] );
            }
            
            $view->getEngine()->debugging = $options['debugging'];
            
            // Add it to the ViewRenderer
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper( 'ViewRenderer' );
            $viewRenderer->setView( $view );

            $this->_view = $view;
        }
        return $this->_view;
    }
} 
