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
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class ErrorController extends INEX_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam( 'error_handler' );

        $this->getResponse()->clearBody();
        ob_clean();

        switch( $errors->type )
        {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()
                ->setRawHeader( 'HTTP/1.1 404 Not Found' );

                $this->view->display( 'error' . DIRECTORY_SEPARATOR . 'error-404.tpl' );
                break;

            default:
                // application error; display error page, but don't change
                // status code

                if( isset( $errors->exception ) )
                $exception = $errors->exception;
                else if( Zend_Registry::isRegistered( 'exception' ) )
                $exception = Zend_Registry::get( 'exception' );

                if( isset( $exception ) )
                {
                    // Log the exception:
                    $this->getLogger()->debug( $exception->getMessage() . "\n" .
                    $exception->getTraceAsString()
                    );

                    // print it appropriately
                    if( isset( $this->config->debug->enabled ) && $this->config->debug->enabled )
                    {
                        $this->view->errorException = $exception;
                        $this->view->display( 'error' . DIRECTORY_SEPARATOR . 'error-debug.tpl' );
                    }
                    else
                    $this->view->display( 'error' . DIRECTORY_SEPARATOR . 'error.tpl' );
                }
                else
                $this->view->display( 'error' . DIRECTORY_SEPARATOR . 'error.tpl' );
                break;
        }
    }

}

?>