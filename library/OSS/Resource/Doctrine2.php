<?php
/**
 * OSS Framework
 *
 * This file is part of the "OSS Framework" - a library of tools, utilities and
 * extensions to the Zend Framework V1.x used for PHP application development.
 *
 * Copyright (c) 2007 - 2012, Open Source Solutions Limited, Dublin, Ireland
 * All rights reserved.
 *
 * Open Source Solutions Limited is a company registered in Dublin,
 * Ireland with the Companies Registration Office (#438231). We
 * trade as Open Solutions with registered business name (#329120).
 *
 * Contact: Barry O'Donovan - info (at) opensolutions (dot) ie
 *          http://www.opensolutions.ie/
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * It is also available through the world-wide-web at this URL:
 *     http://www.opensolutions.ie/licenses/new-bsd
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@opensolutions.ie so we can send you a copy immediately.
 *
 * @category   OSS
 * @package    OSS_Resource
 * @copyright  Copyright (c) 2007 - 2012, Open Source Solutions Limited, Dublin, Ireland
 * @license    http://www.opensolutions.ie/licenses/new-bsd New BSD License
 * @link       http://www.opensolutions.ie/ Open Source Solutions Limited
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     The Skilled Team of PHP Developers at Open Solutions <info@opensolutions.ie>
 */

/**
 * @category   OSS
 * @package    OSS_Resource
 * @copyright  Copyright (c) 2007 - 2012, Open Source Solutions Limited, Dublin, Ireland
 * @license    http://www.opensolutions.ie/licenses/new-bsd New BSD License
 */
class OSS_Resource_Doctrine2 extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Holds the Doctrine instance(s)
     *
     * @var null|Doctrine\ORM\EntityManager
     */
    protected $_doctrine2 = null;

    /**
     * Initialisation function
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function init()
    {
        // Return Doctrine so bootstrap will store it in the registry
        return $this->getDoctrine2();
    }


    /**
     * Get Doctrine2
     *
     * @param string $db The database instance to get (if using multiple databases)
     * @return Doctrine\ORM\EntityManager
     */
    public function getDoctrine2( $db = 'default' )
    {
        if( $this->_doctrine2 === null || !isset( $this->_doctrine2[ $db ] ) )
        {
            $this->_doctrine2[ 'default' ] = D2EM::getFacadeRoot();
        }

        return $this->_doctrine2[ $db ];
    }

    /**
     * Set the classes $_doctrine member
     *
     * @param Doctrine\ORM\EntityManager $doctrine The object to set
     * @param string $db The database instance to set if using multiple databases.
     * @return void
     */
    public function setDoctrine( $doctrine2, $db = 'default' )
    {
        $this->_doctrine2[ $db ] = $doctrine2;
    }

}
