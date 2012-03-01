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
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class MrtgController extends Zend_Controller_Action
{

    public static $GRAPH_CATEGORIES = array (
        'bits' => 'Bits',
        'pkts' => 'Packets',
        'errs' => 'Errors',
        'discs' => 'Discards',
    );

    /**
     * A variable to hold an instance of the bootstrap object
     *
     * @var object An instance of the bootstrap object
     */
    protected $_bootstrap;

    /**
     * A variable to hold an instance of the configuration object
     *
     * @var object An instance of the configuration object
     */
    protected $config = null;

    /**
     * A variable to hold the identity object
     *
     * @var object An instance of the user's identity or false
     */
    protected $auth = null;

    /**
     * A variable to hold an identify of the user
     *
     * Will be !false if there is a valid identity
     *
     * @var object An instance of the user's identity or false
     */
    protected $identity = false;

    /**
     * A variable to hold the user record
     *
     * @var object An instance of the user record
     */
    protected $user = null;


    protected $_flock = null;

    /**
     * Override the Zend_Controller_Action's constructor (which is called
     * at the very beginning of this function anyway).
     *
     * @param object $request See Parent class constructer
     * @param object $response See Parent class constructer
     * @param object $invokeArgs See Parent class constructer
     */
    public function __construct(
            Zend_Controller_Request_Abstract  $request,
            Zend_Controller_Response_Abstract $response,
            array $invokeArgs = null )
    {
        // get the bootstrap object
        $this->_bootstrap = $invokeArgs['bootstrap'];

        // and from the bootstrap, we can get other resources:
        $this->config  = $this->_bootstrap->getApplication()->getOptions();
        $this->_bootstrap->getResource( 'namespace' );
        $this->auth    = $this->_bootstrap->getResource( 'auth' );

        if( $this->auth->hasIdentity() )
        {
            $this->identity = $this->auth->getIdentity();
            $this->user     = Doctrine::getTable( 'User' )->find( $this->identity['user']['id'] );
            $this->customer = Doctrine::getTable( 'Cust' )->find( $this->identity['user']['custid'] );
        }
        else
        {
            die();
        }

        // call the parent's version where all the Zend magic happens
        parent::__construct( $request, $response, $invokeArgs );
    }


    private function checkShortname( $shortname )
    {
        return Doctrine::getTable( 'Cust' )->findByShortname( $shortname );
    }


    function retrieveImageAction()
    {
        header( 'Content-Type: image/png' );
        header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );

        $monitorindex = $this->getRequest()->getParam( 'monitorindex', 'aggregate' );
        $period       = $this->getRequest()->getParam( 'period', INEX_Mrtg::$PERIODS['Day'] );
        $shortname    = $this->getRequest()->getParam( 'shortname' );
        $category     = $this->getRequest()->getParam( 'category', INEX_Mrtg::$CATEGORIES['Bits'] );
        $graph        = $this->getRequest()->getParam( 'graph', '' );

        $this->getLogger()->debug( "Request for $shortname-$monitorindex-$category-$period-$graph by {$this->user->username}" );

        if( !$this->identity )
            exit(0);

        if( $shortname == 'X_Trunks' )
        {
            $filename = $this->config['mrtg']['path']
                . '/../trunks/' . $graph . '-' . $period . '.png';
        }
        else if( $shortname == 'X_SwitchAggregate' )
        {
            $filename = $this->config['mrtg']['path']
                . '/../switches/switch-aggregate-' . $graph . '-'
                . $category . '-' . $period . '.png';
        }
        else if( $shortname == 'X_Peering' )
        {
            $filename = $this->config['mrtg']['path']
                . '/../inex_peering-' . $graph . '-'
                . $category . '-' . $period . '.png';
        }
        else
        {
            if( $this->user['privs'] < User::AUTH_SUPERUSER || !$this->checkShortname( $shortname ) )
                $shortname = $this->customer['shortname'];

            $filename = INEX_Mrtg::getMrtgFilePath( $this->config['mrtg']['path'], 'PNG',
                $monitorindex, $category, $shortname, $period
            );
        }

        $this->getLogger()->debug( "Serving $filename to {$this->user->username}" );

        $stat = @readfile( $filename );

        if( $stat === false )
        {
            $this->getLogger()->err( 'Could not load ' . $filename . ' for mrtg/retrieveImageAction' );
            echo readfile(
                APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR
                    . 'image-missing.png'
            );
        }
    }


}


