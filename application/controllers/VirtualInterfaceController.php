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

        $this->frontend['columns'] = array( 'ignoreme' );

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


    /**
     * A generic action to list the elements of a database (as represented
     * by a Doctrine model) via Smarty templates.
     */
    public function getDataAction()
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

        if( $this->getRequest()->getParam( 'member' ) !== NULL )
            $dataQuery->andWhere( 'c.name LIKE ?', $this->getRequest()->getParam( 'member' ) . '%' );

        if( $this->getRequest()->getParam( 'shortname' ) !== NULL )
            $dataQuery->andWhere( 'c.shortname LIKE ?', $this->getRequest()->getParam( 'shortname' ) . '%' );


        $rows = $dataQuery->execute();

        // FIXME :: below assumes a single physical interface for a virtual interface so port channels are not catered for

        $count = 0;
        $data = '';
        foreach( $rows as $row )
        {
            if( $count > 0 )
                $data .= ',';

            $count++;

            $data .= <<<END_JSON
    {
        "member":"{$row['Cust']['name']}",
        "memberid":"{$row['Cust']['id']}",
        "id":"{$row['id']}",
        "description":"{$row['description']}",
        "shortname":"{$row['Cust']['shortname']}",
        "location":"{$row['Physicalinterface'][0]['Switchport']['SwitchTable']['Cabinet']['Location']['name']}",
        "locationid":"{$row['Physicalinterface'][0]['Switchport']['SwitchTable']['Cabinet']['Location']['id']}",
        "switch":"{$row['Physicalinterface'][0]['Switchport']['SwitchTable']['name']}",
        "switchid":"{$row['Physicalinterface'][0]['Switchport']['SwitchTable']['id']}",
        "port":"{$row['Physicalinterface'][0]['Switchport']['name']}",
        "speed":"{$row['Physicalinterface'][0]['speed']}",
    }
END_JSON;

        }

        $data = <<<END_JSON
{"ResultSet":{
    "totalResultsAvailable":{$count},
    "totalResultsReturned":{$count},
    "firstResultPosition":0,
    "Result":[{$data}]}}
END_JSON;

        echo $data;

    }

}

