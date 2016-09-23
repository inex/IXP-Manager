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
 * Controller: NetInfo management
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class NetInfoController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\NetInfo',
            'pagetitle'     => 'Network Information',
        
            'titleSingular' => 'NetInfo',
            'nameSingular'  => 'a NetInfo',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'addWhenEmpty'   => false,
        
            'listColumns'    => [
            
                'id'        => [ 'title' => 'UID', 'display' => false ],
                'property'      => 'Property',
                'value'    => 'Value',
                'protocol'        => [
                    'title'          => 'Protocol',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => \Entities\NetInfo::$PROTOCOLS
                ]
            ]
        ];
    
    }
    
    /**
     * Provide array of VLANs for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $this->view->Vlan = $vlan = $this->getD2R( '\\Entities\\Vlan' )->find( $this->getParam( 'vlid', 0 ) );
        
        if( !$vlan )
        {
            $this->addMessage( "Requested object was not found.", OSS_Message::ERROR );
            $this->redirect( "vlan/list" );

        }

        $this->view->registerClass( 'NetInfo', '\Entities\NetInfo' );
    
        return $vlan->getNetInfos();
    }
    

    public function addAction()
    {
        $vlan = $this->getD2R( '\\Entities\\Vlan' )->find( $this->getParam( 'vlid', 0 ) );
        if( !$vlan )
        {
            $this->addMessage( "Requested object was not found.", OSS_Message::ERROR );
            $this->redirect( "vlan/list" );
        }

        if( $this->getRequest()->isPost() )
        {
            $ix = $_POST['ix'];
            $protocol = $_POST['protocol'];
            unset( $_POST['protocol'] );
            foreach( $_POST as $property => $value )
            {
                $property = str_replace( "%dot%", '.', $property );
                if( strpos( $property, '.' ) )
                    $prop = explode('.', $property )[0];
                else
                    $prop = $property;

                if( $ix != '' )
                {
                    $vlan->setNetInfo( $property, $value, $protocol, $ix );
                }
                else if( !isset( $this->_options['netinfo']['property'][$prop]['singleton'] ) || $this->_options['netinfo']['property'][$prop]['singleton'] )
                {
                    if( $vlan->hasNetInfo( $property, $protocol ) )
                    {
                        $this->addMessage( "This property already exists. Use edit instead.", OSS_Message::ERROR );
                        $this->redirect( "net-info/list/vlid/" . $vlan->getId() );
                    }

                    $vlan->setNetInfo( $property, $value, $protocol );
                }
                else
                    $vlan->addIndexedNetInfo( $property, $value, $protocol );
            }

            $this->getD2EM()->flush();
            $this->addMessage( "Network information was updated successfully", OSS_Message::SUCCESS );
            $this->redirect( "net-info/list/vlid/" . $vlan->getId() );
        }
    }

    public function deleteAction()
    {
        $vlan = $this->getD2R( '\\Entities\\Vlan' )->find( $this->getParam( 'vlid', 0 ) );
        if( !$vlan )
        {
            $this->addMessage( "Requested object was not found.", OSS_Message::ERROR );
            $this->redirect( "vlan/list" );

        }

        $ix = $this->getParam( 'ix', false );
        $name = $this->getParam( 'name', false );
        $protocol = $this->getParam( 'protocol', false );

        if( $ix === false || $name === false || $protocol === false)
        {
            $this->addMessage( "Missing arguments for this action.", OSS_Message::ERROR );
            $this->redirect( "net-info/list/vlid/" . $vlid->getId() );
        }

        if( strpos( $name, '.' ) )
        {
            $name = substr( $name, 0, strpos( $name, '.' ) );
            $result = $vlan->deleteAssocNetInfo( $name, $protocol, $ix );
        }
        else
            $result = $vlan->deleteNetInfo( $name, $protocol, $ix );

        $this->getD2EM()->flush();
        $this->addMessage( "Network information was updated successfully", OSS_Message::SUCCESS );
        $this->redirect( "net-info/list/vlid/" . $vlan->getId() );
    }
}

