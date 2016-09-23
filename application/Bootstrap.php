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


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Register the IXP library autoloader
     *
     * This function ensures that classes from library/IXP are automatically
     * loaded from the subdirectories where subdirectories are indicated by
     * underscores in the same manner as Zend.
     *
     */
    protected function _initIXPAutoLoader()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace( 'IXP' );
    }


    /**
     * Register the OSS library autoloader
     *
     * This function ensures that classes from library/OSS are automatically
     * loaded from the subdirectories where subdirectories are indicated by
     * underscores in the same manner as Zend.
     *
     */
    protected function _initOSSAutoLoader()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('OSS');
    }
}

