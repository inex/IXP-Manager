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
 * Controller: API V1 Error controller
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Apiv1_ErrorController extends IXP_Controller_API_V1Action
{

    public function errorAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
        $errorHandler = $this->getParam( 'error_handler' );

        $this->log();
        
        if( $errorHandler && isset( $errorHandler['exception'] ) )
        {
            $e = $errorHandler['exception'];
            
            $this->getResponse()->clearBody();
            $this->getResponse()->clearHeaders();
            $this->getResponse()->setHttpResponseCode( ( $e && $e->getCode() ? $e->getCode() : 400 ) );
            $this->getResponse()->setRawHeader( "HTTP/1.1 {$e->getCode()} {$e->getMessage()}" );
        }
        else
        {
            $this->getResponse()->clearBody();
            $this->getResponse()->clearHeaders();
            $this->getResponse()->setHttpResponseCode( 404 );
            $this->getResponse()->setRawHeader( "HTTP/1.1 404 Not Found" );
        }
    }


    private function log()
    {
        $this->getLogger()->debug( "\n" );
        $this->getLogger()->debug( 'ErrorController::errorAction()' );
    
        $log = "\n\n************************************************************************\n"
            . "****************************** EXCEPTIONS *******************************\n"
                . "************************************************************************\n\n";
    
        $exceptions = $this->getResponse()->getException();
    
        if( is_array( $exceptions ) )
        {
            foreach( $exceptions as $e )
            {
                $log .= "--------------------------- EXCEPTION --------------------------\n\n"
                    . "Message: " . $e->getMessage()
                    . "\nLine: "  . $e->getLine()
                    . "\nFile: "  . $e->getFile();
    
                $log .= "\n\nTrace:\n\n"
                    . $e->getTraceAsString() . "\n\n"
                        . print_r( OSS_Debug::compact_debug_backtrace(), true )
                        . "\n\n";
            }
        }
    
        $log .= "------------------------\n\n"
            . "HTTP_HOST : {$_SERVER['HTTP_HOST']}\n"
            . ( isset( $_SERVER['HTTP_USER_AGENT'] ) ? "HTTP_USER_AGENT: {$_SERVER['HTTP_USER_AGENT']}\n" : "" )
            . ( isset( $_SERVER['HTTP_COOKIE']     ) ? "HTTP_COOKIE: {$_SERVER['HTTP_COOKIE']}\n" : "" )
            . "REMOTE_PORT: {$_SERVER['REMOTE_PORT']}\n"
            . "REQUEST_METHOD: {$_SERVER['REQUEST_METHOD']}\n"
            . "REQUEST_URI: {$_SERVER['REQUEST_URI']}\n\n";
    
        $this->getLogger()->err( $log );
    }
}
