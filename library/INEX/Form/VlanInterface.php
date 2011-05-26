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
class INEX_Form_VlanInterface extends INEX_Form
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options, $isEdit );

        ////////////////////////////////////////////////
        // Create and configure elements
        ////////////////////////////////////////////////

        $virtualInterface = $this->createElement( 'hidden', 'virtualinterfaceid' );
        $this->addElement( $virtualInterface );


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

        $collection = Doctrine_Query::create()
        ->from( 'Ipv4address ipv4' )
        ->leftJoin( 'ipv4.Vlaninterface vli' )
        ->leftJoin( 'ipv4.Vlan v' )
        ->where( 'vli.id IS NULL' )
        ->orWhere( 'vli.id = ?', Zend_Controller_Front::getInstance()->getRequest()->getParam( 'id' ) )
        ->orderBy( 'ipv4.address ASC, ipv4.vlanid ASC' )
        ->execute();

        $options = array( '0' => '' );
        $maxId = 0;

        foreach( $collection as $c )
        {
            $options[ $c['id'] ] = "VLAN {$c['Vlan']['number']} - {$c['address']}";

            if( $c['id'] > $maxId ) $maxId = $c['id'];
        }


        $ipv4addressid->setMultiOptions( $options )
        ->setRegisterInArrayValidator( true )
        ->setLabel( 'IPv4 Address' )
        ->addValidator( 'between', false, array( 0, $maxId ) )
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
        array( 'ipv4enabled', 'ipv4addressid', 'ipv4hostname', 'ipv4bgpmd5secret', 'ipv4canping', 'ipv4monitorrcbgp' ),
            'ipv4DisplayGroup'
            );
            $this->getDisplayGroup( 'ipv4DisplayGroup' )->setLegend( 'IPv4 Details' );











            $ipv6enabled = $this->createElement( 'checkbox', 'ipv6enabled' );
            $ipv6enabled->setLabel( 'IPv6 Enabled?' )
            ->setCheckedValue( '1' );
            $this->addElement( $ipv6enabled );




            $ipv6addressid = $this->createElement( 'select', 'ipv6addressid' );

            $collection = Doctrine_Query::create()
            ->from( 'Ipv6address ipv6' )
            ->leftJoin( 'ipv6.Vlaninterface vli' )
            ->leftJoin( 'ipv6.Vlan v' )
            ->where( 'vli.id IS NULL' )
            ->orWhere( 'vli.id = ?', Zend_Controller_Front::getInstance()->getRequest()->getParam( 'id' ) )
            ->orderBy( 'ipv6.address ASC, ipv6.vlanid ASC' )
            ->execute();

            $options = array( '0' => '' );
            $maxId = 0;

            foreach( $collection as $c )
            {
                $options[ $c['id'] ] = "VLAN {$c['Vlan']['number']} - {$c['address']}";

                if( $c['id'] > $maxId ) $maxId = $c['id'];
            }


            $ipv6addressid->setMultiOptions( $options )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'IPv6 Address' )
            ->addValidator( 'between', false, array( 0, $maxId ) )
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
            array( 'ipv6enabled', 'ipv6addressid', 'ipv6hostname', 'ipv6bgpmd5secret', 'ipv6canping', 'ipv6monitorrcbgp' ),
            'ipv6DisplayGroup'
            );
            $this->getDisplayGroup( 'ipv6DisplayGroup' )->setLegend( 'IPv6 Details' );









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




            $notes = $this->createElement( 'textarea', 'notes' );
            $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'rows', 5 );
            $this->addElement( $notes );



            $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
            . $cancelLocation . "'" ) );

            $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );
    }

}

?>