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

class VlanInterfaceController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Vlaninterface';
        $this->frontend['name']            = 'VlanInterface';
        $this->frontend['pageTitle']       = 'VLAN Interfaces';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'vlanName', 'vlanTag', 'ipv4Address', 'ipv6Address', 'custShortname' ),

            'viewPanelRows'  => array( 'custName', 'vlanName', 'vlanTag',
                'ipv4enabled', 'ipv4Address', 'ipv4hostname',
                'ipv6enabled', 'ipv6Address', 'ipv6hostname',
                'irrdbfilter', 'mcastenabled',
                'bgpmd5secret', 'ipv4bgpmd5secret', 'ipv6bgpmd5secret', 'maxbgpprefix',
                'rsclient', 'ipv4canping', 'ipv6canping',
                'ipv4monitorrcbgp', 'ipv6monitorrcbgp', 'as112client', 'busyhost', 'notes'
            ),

            'sortDefaults' => array(
                'column' => 'id',
                'order'  => 'asc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),

            'vlanName' => array(
                'type' => 'hasOne',
                'model' => 'Vlan',
                'controller' => 'vlan',
                'field' => 'name',
                'label' => 'VLAN Name',
                'sortable' => true
            ),

            'vlanTag' => array(
                'type' => 'hasOne',
                'model' => 'Vlan',
                'controller' => 'vlan',
                'field' => 'number',
                'label' => 'VLAN Tag',
                'sortable' => true
            ),

            'ipv4Address' => array(
                'type' => 'hasOne',
                'model' => 'Ipv4address',
                'controller' => 'ipv4-address',
                'field' => 'address',
                'label' => 'IPv4 Address',
                'sortable' => true
            ),

            'ipv6Address' => array(
                'type' => 'hasOne',
                'model' => 'Ipv6address',
                'controller' => 'ipv6-address',
                'field' => 'address',
                'label' => 'IPv6 Address',
                'sortable' => true
            ),

            'custShortname' => array(
                'type' => 'l2HasOne',
                'l1model' => 'Virtualinterface',
                'l1controller' => 'virtual-interface',
                'l2model' => 'Cust',
                'l2controller' => 'customer',
                'field' => 'shortname',
                'label' => 'Member',
                'sortable' => true
            ),

            'custName' => array(
                'type' => 'l2HasOne',
                'l1model' => 'Virtualinterface',
                'l1controller' => 'virtual-interface',
                'l2model' => 'Cust',
                'l2controller' => 'customer',
                'field' => 'name',
                'label' => 'Member',
                'sortable' => true
            ),

            'ipv4enabled' => array(
                'label' => 'IPv4 Enabled'
            ),

            'ipv4hostname' => array(
                'label' => 'IPv4 Hostname'
            ),

            'ipv6enabled' => array(
                'label' => 'IPv6 Enabled'
            ),

            'ipv6hostname' => array(
                'label' => 'IPv6 Hostname'
            ),

            'irrdbfilter' => array(
                'label' => 'Apply IRRDB Filter'
            ),

            'mcastenabled' => array(
                'label' => 'Multicast Enabled'
            ),

            'bgpmd5secret' => array(
                'label' => 'BGP MD5 Secret'
            ),

            'ipv6bgpmd5secret' => array(
                'label' => 'IPv6 BGP MD5 Secret'
            ),

            'ipv4bgpmd5secret' => array(
                'label' => 'IPv4 BGP MD5 Secret'
            ),

            'maxbgpprefix' => array(
                'label' => 'Max BGP Prefix'
            ),

            'rsclient' => array(
                'label' => 'Route Server Client'
            ),

            'ipv4canping' => array(
                'label' => 'IPv4 Can Ping?'
            ),

            'ipv6canping' => array(
                'label' => 'IPv6 Can Ping?'
            ),

            'ipv4monitorrcbgp' => array(
                'label' => 'IPv4 Monitor RC BGP'
            ),

            'ipv6monitorrcbgp' => array(
                'label' => 'IPv6 Monitor RC BGP'
            ),

            'as112client' => array(
                'label' => 'AS112 Client'
            ),

            'busyhost' => array(
                'label' => 'Busy host'
            ),

            'notes' => array(
                'label' => 'Notes'
            )

        );

        parent::feInit();
    }


    //addEditPreDisplay
    function addEditPreDisplay( $form, $object )
    {
        // did we get an id from the provisioning controller?
        if( $this->_getParam( 'prov_virtualinterface_id', false ) )
        {
            $form->getElement( 'cancel' )->setAttrib( 'onClick',
                "parent.location='" . $this->config['identity']['ixp']['url']
                    . '/provision/interface-overview/id/' . $this->session->provisioning_interface_active_id . "'"
            );
        }

        if( $this->getRequest()->getParam( 'virtualinterfaceid' ) !== null )
        {
            $form->getElement( 'virtualinterfaceid' )->setValue( $this->getRequest()->getParam( 'virtualinterfaceid' ) );
        }
    }
    
    
    
    
    
    protected function formPrevalidate( $form, $isEdit, $object )
    {
        // set the switch and port fields of the form if we're editing
        if( $isEdit )
        {
            $form->getElement( 'vlanid')->setValue( $object['vlanid'] );
            
            $form->getElement( 'preselectIPv4Address' )->setValue( $object['ipv4addressid'] );
            $form->getElement( 'preselectIPv6Address' )->setValue( $object['ipv6addressid'] );
            
            $form->getElement( 'preselectVlanInterface' )->setValue( $object['id'] );
        }
    }
    
    public function ajaxGetIpv4Action()
    {
        $vlan = Doctrine::getTable( 'Vlan' )->find( $this->_getParam( 'vlanid', null ) );

        $ips = '';
        
        if( $vlan )
        {
            $ips = Doctrine_Query::create()
                ->from( 'Ipv4address ip' )
                ->leftJoin( 'ip.Vlaninterface vli' )
                ->where( 'ip.vlanid = ?', $vlan['id'] );
                
            if( $this->_getParam( 'id', null ) !== null )
                $ips = $ips->andWhere( '( vli.id IS NULL OR vli.id = ? )', $this->_getParam( 'id' ) );
            else
                $ips = $ips->andWhere( 'vli.id IS NULL' );
                
            $ips = $ips->orderBy( 'ip.id' )
                ->fetchArray();
        }
        
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody( Zend_Json::encode( $ips ) )
            ->sendResponse();
        exit();
    }

    public function ajaxGetIpv6Action()
    {
        $vlan = Doctrine::getTable( 'Vlan' )->find( $this->_getParam( 'vlanid', null ) );

        $ips = '';
        
        if( $vlan )
        {
            $ips = Doctrine_Query::create()
                ->from( 'Ipv6address ip' )
                ->leftJoin( 'ip.Vlaninterface vli' )
                ->where( 'ip.vlanid = ?', $vlan['id'] );
                
            if( $this->_getParam( 'id', null ) !== null )
                $ips = $ips->andWhere( '( vli.id IS NULL OR vli.id = ? )', $this->_getParam( 'id' ) );
            else
                $ips = $ips->andWhere( 'vli.id IS NULL' );
                
            $ips = $ips->orderBy( 'ip.id' )
                ->fetchArray();
        }
        
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody( Zend_Json::encode( $ips ) )
            ->sendResponse();
        exit();
    }
    
    
    
    
    public function quickAddAction()
    {
        $f = new INEX_Form_QuickAddInterface( null, false, 'virtual-interface' );

        // Process a submitted form if it passes initial validation
        if( $this->inexGetPost( 'commit' ) !== null && $f->isValid( $_POST ) )
        {
            do
            {
                // check customer information
                if( !( $c = Doctrine_Core::getTable( 'Cust' )->find( $f->getValue( 'custid' ) ) ) )
                {
                    $f->getElement( 'custid' )->addError( 'Invalid customer' );
                    break;
                }
                
                // create the entities
                $conn = Doctrine_Manager::connection();
                $conn->beginTransaction();
                
                try
                {
                    // virtual interface
                    $vi = new Virtualinterface();
                    $f->assignFormToModel( $vi, $this, false );
                    $vi->save();
                    
                    // and now a physical interface
                    $pi                       = new Physicalinterface();
                    
                    $f->assignFormToModel( $pi, $this, false );

                    $pi['virtualinterfaceid'] = $vi['id'];

                    $nextMonitorIndex = Doctrine_Query::create()
    	                ->select( 'MAX( pi.monitorindex )' )
    	                ->from( 'Physicalinterface pi' )
    	                ->leftJoin( 'pi.Virtualinterface vi' )
    	                ->where( 'vi.custid = ?', $c['id'] )
    	                ->execute()
    	                ->toArray();

                    $pi['monitorindex'] = $nextMonitorIndex[0]['MAX'] + 1;
                    $pi->save();
                    
                    
                    // and lastly, the VLAN interface
                    $vli = new Vlaninterface();
                    
                    $f->assignFormToModel( $vli, $this, false );
                    
                    $vli['virtualinterfaceid'] = $vi['id'];

                    $vli->save();
                    
                    $conn->commit();
                }
                catch( Exceltion $e )
                {
                    $conn->rollback();
                }
                
                $this->getLogger()->notice( 'New virtual, physical and VLAN interface created' );
                $this->session->message = new INEX_Message( "New interface added", "success" );
                $this->_redirect( 'virtual-interface/edit/id/' . $vi['id'] );
                
            }while( false );
            
            $loc = $this->genUrl( 'customer', 'dashboard', array( 'id' => $f->getElement( 'custid' )->getValue() ) );
        }
        else if( $this->_getParam( 'commit', false ) === false && $cid = $this->_getParam( 'custid', false ) )
        {
            $f->getElement( 'custid' )->setValue( $cid );
            $loc = $this->genUrl( 'customer', 'dashboard', array( 'id' => $cid ) );
        }
        else
        {
            $loc = $this->genUrl( 'virtual-interface', 'list' );
        }
        
        $f->getElement( 'cancel' )->setAttrib( 'onClick',
                "parent.location='{$loc}'"
        );

        $this->view->form   = $f->render( $this->view );

        $this->view->display( 'vlan-interface' . DIRECTORY_SEPARATOR . 'quick-add.tpl' );
        
    }
    
    protected function _deleteSetReturnOnSuccess()
    {
        if( $vid = $this->_getParam( 'virtualinterfaceid', false ) )
            return "virtual-interface/edit/id/{$vid}";
    
            return 'vlan-interface/list';
    }
    
}

