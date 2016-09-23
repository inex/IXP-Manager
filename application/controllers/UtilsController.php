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
 * Controller: Misc utils
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UtilsController extends IXP_Controller_AuthRequiredAction
{

    public function preDispatch()
    {
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
            return $this->forward( 'insufficient-permissions', 'error' );
    }


    /**
     * Display apcinfo()
     */
    public function apcinfoAction()
    {}

    /**
     * Display apcinfo()
     */
    public function realApcinfoAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
        @ob_end_clean();
        $BU = Zend_Controller_Front::getInstance()->getBaseUrl() . '/utils/apcinfo';
        require( APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'apcinfo.php' );
    }
    
    
    /**
     * Display phpinfo
     */
    public function phpinfoAction()
    {}
    
    /**
     * Display real phpinfo()
     */
    public function realPhpinfoAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
        @ob_end_clean();
        phpinfo();
    }
}

