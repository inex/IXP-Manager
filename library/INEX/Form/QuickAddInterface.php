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

/**
 *
 * @package INEX_Form
 */
class INEX_Form_QuickAddInterface extends INEX_Form
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options, $isEdit );

        $dbCusts = Doctrine_Query::create()
            ->from( 'Cust c' )
            ->where( 'c.type != ?', Cust::TYPE_ASSOCIATE )
            ->orderBy( 'c.name ASC' )
            ->execute();

        $custs = array( '0' => '' );
        $maxId = 0;

        foreach( $dbCusts as $c )
        {
            $custs[ $c['id'] ] = "{$c['name']}";
            if( $c['id'] > $maxId ) $maxId = $c['id'];
        }

        $cust = $this->createElement( 'select', 'custid' );
        $cust->setMultiOptions( $custs );
        $cust->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( 'Customer' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( 'Please select a customer' ) );
        $this->addElement( $cust );
        
        //////////////////////////////////////////////////////////////////////////
        // 
        // VIRTUAL INTERFACE DETAILS
        //
        //////////////////////////////////////////////////////////////////////////

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Virtual Interface Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $name );


        $descr = $this->createElement( 'text', 'description' );
        $descr->setLabel( 'Description' )
            ->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->addFilter( 'StringTrim' );
        $this->addElement( $descr );


        $channel = $this->createElement( 'text', 'channelgroup' );
        $channel->addValidator( 'int' )
            ->setLabel( 'Channel Group Number' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $channel );

        $mtu = $this->createElement( 'text', 'mtu' );
        $mtu->addValidator( 'int' )
            ->setLabel( 'MTU' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $mtu );



        $trunk = $this->createElement( 'checkbox', 'trunk' );
        $trunk->setLabel( 'Is 802.1q Trunk?' )
            ->setCheckedValue( '1' );
        $this->addElement( $trunk );

        $this->addDisplayGroup(
            array( 'name', 'description', 'channelgroup', 'mtu', 'trunk' ),
            'virtualInterfaceDisplayGroup'
        );
            
        $this->getDisplayGroup( 'virtualInterfaceDisplayGroup' )->setLegend( 'Virtual Interface Details' );

        
        //////////////////////////////////////////////////////////////////////////
        // 
        // PHYSICAL INTERFACE DETAILS
        //
        //////////////////////////////////////////////////////////////////////////

        $switch = $this->createElement( 'select', 'switch_id' );

        $maxSwitchId = $this->createSelectFromDatabaseTable( $switch, 'SwitchTable', 'id',
            array( 'name' ),
            'name'
        );

        $switch->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( 'Switch' )
            ->addValidator( 'between', false, array( 1, $maxSwitchId ) )
            ->setErrorMessages( array( 'Please select a switch' ) );

        $this->addElement( $switch );

        $switchPorts = $this->createElement( 'select', 'switchportid' );

        $switchPorts->setRequired( true )
            ->setRegisterInArrayValidator( false )
            ->setLabel( 'Port' )
            ->addValidator( 'greaterThan', false, array( 'min' => 1 ) )
            ->setErrorMessages( array( 'Please select a switch port' ) );
            
        $this->addElement( $switchPorts );
        
        $status = $this->createElement( 'select', 'status' );
        
        $status->setMultiOptions( Physicalinterface::$STATES_TEXT )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Status' )
            ->setErrorMessages( array( 'Please set the status' ) );

        $this->addElement( $status );


        $speed = $this->createElement( 'select', 'speed' );
        $speed->setMultiOptions( Physicalinterface::$SPEED )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Speed' )
            ->setErrorMessages( array( 'Please set the speed' ) );

        $this->addElement( $speed );


        $duplex = $this->createElement( 'select', 'duplex' );
        $duplex->setMultiOptions( Physicalinterface::$DUPLEX )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Duplex' )
            ->setErrorMessages( array( 'Please set the duplex' ) );

        $this->addElement( $duplex );


        $this->addDisplayGroup(
            array( 'switch_id', 'switchportid', 'status', 'speed', 'duplex' ),
            'physicalInterfaceDisplayGroup'
        );
        
        $this->getDisplayGroup( 'physicalInterfaceDisplayGroup' )->setLegend( 'Physical Interface Details' );
        

        //////////////////////////////////////////////////////////////////////////
        // 
        // VLAN INTERFACE DETAILS
        //
        //////////////////////////////////////////////////////////////////////////

        
        $vlan = $this->createElement( 'select', 'vlanid' );
        $maxId = $this->createSelectFromDatabaseTable( $vlan, 'Vlan', 'id', array( 'name', 'number' ), 'name', 'ASC' );

        $vlan->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( 'VLAN' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( 'Please select a VLAN' ) );
        $this->addElement( $vlan );




        $ipv4enabled = $this->createElement( 'checkbox', 'ipv4enabled' );
        $ipv4enabled->setLabel( 'IPv4 Enabled?' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv4enabled );

        $ipv4addressid = $this->createElement( 'select', 'ipv4addressid' );

        $ipv4addressid->setMultiOptions( array( '--select a VLAN --' ) )
            ->setRegisterInArrayValidator( false )
            ->setLabel( 'IPv4 Address' )
            ->addValidator( 'greaterThan', false, array( 'min' => 1 ) )
            ->setErrorMessages( array( 'Please select a IPv4 address' ) );
        $this->addElement( $ipv4addressid );



        $ipv4hostname = $this->createElement( 'text', 'ipv4hostname' );
        $ipv4hostname->addValidator( 'stringLength', false, array( 1, 64 ) )
            ->setLabel( 'IPv4 Hostname' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $ipv4hostname  );

        
        
        $ipv4bgpmd5secret = $this->createElement( 'text', 'ipv4bgpmd5secret' );
        $ipv4bgpmd5secret->addValidator( 'stringLength', false, array( 1, 64 ) )
            ->setLabel( 'IPv4 BGP MD5 Secret' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $ipv4bgpmd5secret  );



        $ipv4canping = $this->createElement( 'checkbox', 'ipv4canping' );
        $ipv4canping->setLabel( 'IPv4 Can Ping?' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv4canping );

        
        
        $ipv4monitorrcbgp = $this->createElement( 'checkbox', 'ipv4monitorrcbgp' );
        $ipv4monitorrcbgp->setLabel( 'IPv4 Monitor RC BGP?' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv4monitorrcbgp );

        
        $this->addDisplayGroup(
            array( 
            	'ipv4enabled', 'ipv4addressid', 'ipv4hostname', 'ipv4bgpmd5secret', 
            	'ipv4canping', 'ipv4monitorrcbgp' 
            ),
            'ipv4DisplayGroup'
        );
        
        $this->getDisplayGroup( 'ipv4DisplayGroup' )->setLegend( 'IPv4 Details' );




        $ipv6enabled = $this->createElement( 'checkbox', 'ipv6enabled' );
        $ipv6enabled->setLabel( 'IPv6 Enabled?' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv6enabled );

        $ipv6addressid = $this->createElement( 'select', 'ipv6addressid' );
        $ipv6addressid->setMultiOptions( array( '--select a VLAN --' ) )
            ->setRegisterInArrayValidator( false )
            ->setLabel( 'IPv6 Address' )
            ->addValidator( 'greaterThan', false, array( 'min' => 1 ) )
            ->setErrorMessages( array( 'Please select a IPv6 address' ) );
        $this->addElement( $ipv6addressid );
        

        $ipv6hostname = $this->createElement( 'text', 'ipv6hostname' );
        $ipv6hostname->addValidator( 'stringLength', false, array( 1, 64 ) )
            ->setLabel( 'IPv6 Hostname' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $ipv6hostname  );

        $ipv6bgpmd5secret = $this->createElement( 'text', 'ipv6bgpmd5secret' );
        $ipv6bgpmd5secret->addValidator( 'stringLength', false, array( 1, 64 ) )
            ->setLabel( 'IPv6 BGP MD5 Secret' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $ipv6bgpmd5secret  );



        $ipv6canping = $this->createElement( 'checkbox', 'ipv6canping' );
        $ipv6canping->setLabel( 'IPv6 Can Ping?' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv6canping );

        
        $ipv6monitorrcbgp = $this->createElement( 'checkbox', 'ipv6monitorrcbgp' );
        $ipv6monitorrcbgp->setLabel( 'IPv6 Monitor RC BGP?' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv6monitorrcbgp );

        
        $this->addDisplayGroup(
            array( 
            	'ipv6enabled', 'ipv6addressid', 'ipv6hostname', 'ipv6bgpmd5secret', 
            	'ipv6canping', 'ipv6monitorrcbgp' 
            ),
        	'ipv6DisplayGroup'
        );
        
        $this->getDisplayGroup( 'ipv6DisplayGroup' )->setLegend( 'IPv6 Details' );


        

        $irrdbfilter = $this->createElement( 'checkbox', 'irrdbfilter' );
        $irrdbfilter->setLabel( 'Apply IRRDB Filtering?' )
            ->setCheckedValue( '1' );
        $this->addElement( $irrdbfilter );

        
        $mcastenabled = $this->createElement( 'checkbox', 'mcastenabled' );
        $mcastenabled->setLabel( 'Multicast Enabled?' )
            ->setCheckedValue( '1' );
        $this->addElement( $mcastenabled );

        $maxbgpprefix = $this->createElement( 'text', 'maxbgpprefix' );
        $maxbgpprefix->addValidator('int')
            ->addValidator( 'greaterThan', false, array( -1 ) )
            ->setRequired( false )
            ->setLabel( 'Max BGP Prefixes' );
        $this->addElement( $maxbgpprefix  );


        $rsclient = $this->createElement( 'checkbox', 'rsclient' );
        $rsclient->setLabel( 'Route Server Client?' )
            ->setCheckedValue( '1' );
        $this->addElement( $rsclient );

        $as112client = $this->createElement( 'checkbox', 'as112client' );
        $as112client->setLabel( 'AS112 Client?' )
            ->setCheckedValue( '1' );
        $this->addElement( $as112client );

        $busyhost = $this->createElement( 'checkbox', 'busyhost' );
        $busyhost->setLabel( 'Busy host?' )
            ->setCheckedValue( '1' );
        $this->addElement( $busyhost );

        
        $this->addDisplayGroup(
            array( 
            	'irrdbfilter', 'mcastenabled', 'maxbgpprefix', 'rsclient', 
            	'as112client', 'busyhost' 
            ),
        	'vlanInterfaceDisplayGroup'
        );
        
        $this->getDisplayGroup( 'vlanInterfaceDisplayGroup' )->setLegend( 'Other VLAN Interface Settings' );
        

        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
        . $cancelLocation . "'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

            
        $preselectIPv4Address = $this->createElement( 'hidden', 'preselectIPv4Address' );
        $this->addElement( $preselectIPv4Address );
        
        $preselectIPv6Address = $this->createElement( 'hidden', 'preselectIPv6Address' );
        $this->addElement( $preselectIPv6Address );
        
        $preselectVlanInterface = $this->createElement( 'hidden', 'preselectVlanInterface' );
        $this->addElement( $preselectVlanInterface );
            

        $preselectSwitchPort = $this->createElement( 'hidden', 'preselectSwitchPort' );
        $this->addElement( $preselectSwitchPort );
        
        $preselectPhysicalInterface = $this->createElement( 'hidden', 'preselectPhysicalInterface' );
        $this->addElement( $preselectPhysicalInterface );
        
    }

}

?>