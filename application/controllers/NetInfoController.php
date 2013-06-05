<?php

/*
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
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
            'form'          => 'IXP_Form_Vlan',
            'pagetitle'     => 'Network Information',
        
            'titleSingular' => 'NetInfo',
            'nameSingular'  => 'a NetInfo',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'protocol',
            'listOrderByDir' => 'ASC',
            'addWhenEmpty'   => false,
        
            'listColumns'    => [
            
                'id'        => [ 'title' => 'UID', 'display' => false ],
                'property'      => 'Property',
                'value'    => 'Value',
                'ix'    => 'Index',
                'protocol'        => [
                    'title'          => 'Protocol',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => \Entities\NetInfo::$PROTOCOLS
                ]
            ]
        ];
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [ 'notes' => 'Notes' ]
        );
    }
    
    /**
     * Provide array of VLANs for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $vlan = $this->getD2R( '\\Entities\\Vlan' )->find( $this->getParam( 'vlid', 0 ) );
        if( !$vlan )
        {
            $this->addMessage( "Requested object was not found.", OSS_Message::ERROR );
            $this->redirect( "vlan/list" );

        }

        $this->view->Vlan = $vlan;
        $this->view->Protocols = \Entities\NetInfo::$PROTOCOLS;

        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'ni.id AS id, ni.protocol AS protocol, ni.ix as ix,
                    ni.property AS property, ni.value AS value'
            )
            ->from( '\\Entities\\NetInfo', 'ni' )
            ->where( 'ni.Vlan = ?1')
            ->setParameter( 1, $vlan );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        return $qb->getQuery()->getResult();
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
            $protocol = $_POST['protocol'];
            unset( $_POST['protocol'] );
            foreach( $_POST as $property => $value )
            {
                $property = str_replace( "%dot%", '.', $property );
                if( strpos( $property, '.' ) )
                    $prop = explode('.', $property )[0];
                else
                    $prop = $property;

                if( !isset( $this->_options['netinfo']['property'][$prop]['singleton'] ) || $this->_options['netinfo']['property'][$prop]['singleton'] )
                    $vlan->setNetInfo( $property, $value, $protocol );
                else
                    $vlan->addIndexedNetInfo( $property, $value, $protocol );
            }

            $this->getD2EM()->flush();
            $this->addMessage( "Network information was updated successfully", OSS_Message::SUCCESS );
            $this->redirect( "net-info/list/vlid/" . $vlan->getId() );
        }
    }

    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful deletion operation.
     *
     * By default it returns `false`.
     *
     * On `false`, the default action (`index`) is called and a standard success message is displayed.
     *
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function deleteDestinationOnSuccess()
    {
        $this->addMessage( "Network information was updated successfully", OSS_Message::SUCCESS );
        $this->redirect( "net-info/list/vlid/" . $this->getParam( 'vlid', 0 ) );
    }
}

