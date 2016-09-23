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
 * A Router for CLI Scripts
 *
 * See: http://webfractor.wordpress.com/2008/08/14/using-zend-framework-from-the-command-line/
 *
 * @package IXP_Controller
 * @subpackage Router
 */
class IXP_Controller_Router_Cli extends Zend_Controller_Router_Abstract implements Zend_Controller_Router_Interface
{

    public function assemble( $userParams, $name = null, $reset = false, $encode = true )
    { }

    public function route( Zend_Controller_Request_Abstract $dispatcher )
    {}

}
