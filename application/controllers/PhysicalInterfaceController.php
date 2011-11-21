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

class PhysicalInterfaceController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Physicalinterface';
        $this->frontend['name']            = 'PhysicalInterface';
        $this->frontend['pageTitle']       = 'Physical Interfaces';

        $this->frontend['disableAddNew']   = true;

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'switch', 'switchport', 'status', 'speed', 'duplex' ),


            'viewPanelRows'  => array( 'switch', 'switchport', 'status', 'speed', 'duplex', 'monitorindex', 'notes' ),


            'sortDefaults' => array(
                'column' => 'status',
                'order'  => 'desc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),


            'switch' => array(
                'type' => 'l2HasOne',
                'l1model' => 'Switchport',
                'l1controller' => 'switchport',
                'l2model' => 'SwitchTable',
                'l2controller' => 'switch',
                'field' => 'name',
                'label' => 'Switch',
                'sortable' => true
            ),

            'switchport' => array(
                'type' => 'hasOne',
                'model' => 'Switchport',
                'controller' => 'switch-port',
                'field' => 'name',
                'label' => 'Port',
                'sortable' => true
            ),

            'status' => array(
                'label' => 'Status',
                'type' => 'xlate',
                'sortable' => true,
            	'xlator' => Physicalinterface::$STATES_TEXT
            ),

            'speed' => array(
                'label' => 'Speed',
                'sortable' => true,
            ),

            'duplex' => array(
                'label' => 'Duplex',
                'sortable' => true
            ),

            'monitorindex' => array(
                'label' => 'Monitor Index'
            )

        );

        parent::feInit();
    }

    /**
     * addEditPreDisplay
     *
     * @param INEX_Form_PhysicalInterface The form object
     */
    function addEditPreDisplay( $form, $object )
    {
        // did we get a customer id from the provisioning controller?
        if( $this->_getParam( 'prov_virtualinterface_id', false ) )
        {
            $form->getElement( 'cancel' )->setAttrib( 'onClick',
                "parent.location='" . $this->config['identity']['ixp']['url']
                    . '/provision/interface-overview/id/' . $this->session->provisioning_interface_active_id . "'"
            );
        }

        // if provisioning and we're creating an interface:
        if( $this->_getParam( 'prov_physicalinterface_id' ) !== null )
        {
            $form->getElement( 'status' )->setValue( Physicalinterface::STATUS_XCONNECT );
        }


        if( $this->getRequest()->getParam( 'virtualinterfaceid' ) !== null )
        {
            $form->getElement( 'virtualinterfaceid' )->setValue( $this->getRequest()->getParam( 'virtualinterfaceid' ) );

            if( $form->getElement( 'monitorindex' )->getValue() == '' )
            {
                $virtualInterface = Doctrine::getTable( 'Virtualinterface' )->find( $this->getRequest()->getParam( 'virtualinterfaceid' ) );

                $nextMonitorIndex = Doctrine_Query::create()
	                ->select( 'MAX( pi.monitorindex )' )
	                ->from( 'Physicalinterface pi' )
	                ->leftJoin( 'pi.Virtualinterface vi' )
	                ->where( 'vi.custid = ?', $virtualInterface['custid'] )
	                ->execute()
	                ->toArray();

                $form->getElement( 'monitorindex' )->setValue( $nextMonitorIndex[0]['MAX'] + 1 );
            }
        }
    }
    
    
    protected function formPrevalidate( $form, $isEdit, $object )
    {
        // set the switch and port fields of the form if we're editing
        if( $isEdit )
        {
            $form->getElement( 'switch_id')->setValue( $object->Switchport->SwitchTable['id'] );
            $form->getElement( 'preselectSwitchPort' )->setValue( $object->Switchport['id'] );
            $form->getElement( 'preselectPhysicalInterface' )->setValue( $object['id'] );
        }
    }
    
    public function ajaxGetPortsAction()
    {
        $switch = Doctrine::getTable( 'SwitchTable' )->find( $this->_getParam( 'switchid', null ) );

        $ports = '';
        
        if( $switch )
        {
            $ports = Doctrine_Query::create()
                ->from( 'Switchport sp' )
                ->leftJoin( 'sp.Physicalinterface pi' )
                ->where( 'sp.switchid = ?', $switch['id'] );
                
            if( $this->_getParam( 'id', null ) !== null )
                $ports = $ports->andWhere( '( pi.id IS NULL OR pi.id = ? )', $this->_getParam( 'id' ) );
            else
                $ports = $ports->andWhere( 'pi.id IS NULL' );
                
            $ports = $ports->orderBy( 'sp.id' )
                ->fetchArray();
                
            foreach( $ports as $i => $p )
                $ports[$i]['type'] = Switchport::$TYPE_TEXT[ $p['type'] ];
        }
        
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody( Zend_Json::encode( $ports ) )
            ->sendResponse();
        exit();
    }

}

?>