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

class VirtualInterfaceController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Virtualinterface';
        $this->frontend['name']            = 'VirtualInterface';
        $this->frontend['pageTitle']       = 'Virtual Interfaces';

        // add new button in postContent with QuickAdd
        $this->frontend['disableAddNew']   = true;
        
        $this->frontend[ 'columns' ] = array(

            'displayColumns' => array(
                'id', 'member', 'shortname', 'location', 'switch', 'port', 'speed'
            ),

	        'viewPanelRows' => array(
	            'member' //, 'name', 'description', 'mtu', 'trunk', 'channelgroup'
	        ),

	        'viewPanelTitle' => 'member',

	        'sortDefaults' => array(
	            'column' => 'member', 'order' => 'asc'
	        ),
	        
	        'id' => array(
	            'label' => 'ID', 'hidden' => true
	        ),

	        'member' => array(
	            'type' => 'aHasOne', 
	            'controller' => 'customer', 
	            'ifield' => 'memberid', 
	            'label' => 'Customer', 
	            'sortable' => true
	        ),
	        
	        'shortname' => array(
	            'type' => 'aHasOne', 
	            'controller' => 'customer', 
	            'ifield' => 'memberid', 
	            'label' => 'Shortname', 
	            'sortable' => true
	        ),

	        'location' => array(
	            'type' => 'aHasOne', 
	            'controller' => 'location', 
	            'ifield' => 'locationid', 
	            'label' => 'Location', 
	            'sortable' => true
	        ),
	        
	        'switch' => array(
	            'type' => 'aHasOne', 
	            'controller' => 'switch', 
	            'ifield' => 'switchid', 
	            'label' => 'Switch', 
	            'sortable' => true
	        ),
	        
	        'port' => array(
	            'label' => 'Port', 
	            'sortable' => true
	        ),
	        
	        'speed' => array(
	            'label' => 'Speed', 
	            'sortable' => true
	        )
        );

        
        parent::feInit();
    }

    /**
     * If deleting a virtual interface, we should also the delete the physical and vlan interfaces
     * if they exist.
     *
     */
    protected function preDelete( $object = null )
    {
        if( ( $oid = $this->getRequest()->getParam( 'id', null ) ) === null )
            return false;

        if( !( $vint = Doctrine::getTable( $this->getModelName() )->find( $oid ) ) )
            return false;

        foreach( $vint->Physicalinterface as $pi )
        {
            $this->logger->notice( "Deleting physical interface with id #{$pi->id} while deleting virtual interface #{$vint->id}" );
            $pi->delete();
        }

        foreach( $vint->Vlaninterface as $vl )
        {
            $this->logger->notice( "Deleting vlan interface with id #{$vl['id']} while deleting virtual interface #{$vint['id']}" );
            $vl->delete();
        }
    }


    //addEditPreDisplay
    function addEditPreDisplay( $form, $object )
    {
        // did we get a customer id from the provisioning controller?
        if( $this->_getParam( 'prov_cust_id', false ) )
        {
            $form->getElement( 'custid' )->setValue( $this->_getParam( 'prov_cust_id' ) );

            $form->getElement( 'cancel' )->setAttrib( 'onClick',
                "parent.location='" . $this->config['identity']['ixp']['url']
                    . '/provision/interface-overview/id/' . $this->session->provisioning_interface_active_id . "'"
            );
        }

        $dataQuery1 = Doctrine_Query::create()
	        ->from( 'Physicalinterface pi' )
	        ->leftJoin( 'pi.Switchport sp' )
	        ->leftJoin( 'sp.SwitchTable s' )
	        ->leftJoin( 's.Cabinet cb' )
	        ->leftJoin( 'cb.Location l' )
	        ->where( 'pi.Virtualinterface.id = ?', $this->getRequest()->getParam( 'id' ) );

        $this->view->phyInts = $dataQuery1->execute();

        $dataQuery2 = Doctrine_Query::create()
	        ->from( 'Vlaninterface vli' )
	        ->leftJoin( 'vli.Virtualinterface vi' )
	        ->leftJoin( 'vli.Ipv4address v4' )
	        ->leftJoin( 'vli.Ipv6address v6' )
	        ->leftJoin( 'vli.Vlan v' )
	        ->where( 'vi.id = ?', $this->getRequest()->getParam( 'id' ) );

        $this->view->vlanInts = $dataQuery2->execute();

    }


    public function _customlist()
    {
        $dataQuery = Doctrine_Query::create()
	        ->from( 'Virtualinterface vi' )
	        ->leftJoin( 'vi.Cust c' )
	        ->leftJoin( 'vi.Physicalinterface pi' )
	        ->leftJoin( 'pi.Switchport sp' )
	        ->leftJoin( 'sp.SwitchTable s' )
	        ->leftJoin( 's.Cabinet cb' )
	        ->leftJoin( 'cb.Location l' )
	        ->orderBy( 'c.shortname ASC' );

        $results = $dataQuery->execute();

        $rows = array();
        foreach( $results as $r )
        {
            $row = array();
            
            $row["member"]      = $r['Cust']['name'];
            $row["memberid"]    = $r['Cust']['id'];
            $row["id"]          = $r['id'];
            $row["description"] = $r['description'];
            $row["shortname"]   = $r['Cust']['shortname'];
            $row["location"]    = $r['Physicalinterface'][0]['Switchport']['SwitchTable']['Cabinet']['Location']['name'];
            $row["locationid"]  = $r['Physicalinterface'][0]['Switchport']['SwitchTable']['Cabinet']['Location']['id'];
            $row["switch"]      = $r['Physicalinterface'][0]['Switchport']['SwitchTable']['name'];
            $row["switchid"]    = $r['Physicalinterface'][0]['Switchport']['SwitchTable']['id'];
            
            if( count( $r['Physicalinterface'] ) > 1 )
            {
                $row["port"]        = '(trunk)';
                $row["speed"]       = $r['Physicalinterface'][0]['speed'] * count( $r['Physicalinterface'] );
            }
            else
            {
                $row["port"]        = $r['Physicalinterface'][0]['Switchport']['name'];
                $row["speed"]       = $r['Physicalinterface'][0]['speed'];
            }
               
            $rows[] = $row;
        }

        return $rows;
    }
    
    
    /**
     * Hook function to set a customer return.
     * 
     * We want to display the virtual interface which was added / edited.
	 *
     * @param INEX_Form_SwitchPort $f
     * @param Switchport $o
     */
    protected function _addEditSetReturnOnSuccess( $f, $o )
    {
        return 'virtual-interface/edit/id/' . $o['id'];
    }
    

}

