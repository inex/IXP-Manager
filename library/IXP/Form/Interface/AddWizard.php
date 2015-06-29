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
 * Form: wizard for adding virtual, physical and vlan interface
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Interface_AddWizard extends IXP_Form
{
    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'virtual-interface/forms/add-wizard.phtml' ] ] ] );

        $this->addElement( IXP_Form_Customer::getPopulatedSelect( 'custid' ) );
        $this->getElement( 'custid' )->setAttrib( 'class', 'chzn-select span12' );

        ////////////////////////////////////////////////////////////////////////////////////////////////////
        // VIRTUAL INTERFACE DETAILS

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Virtual Interface Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );


        $descr = $this->createElement( 'text', 'description' );
        $descr->setLabel( 'Description' )
            ->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->addFilter( 'StringTrim' );
        $this->addElement( $descr );


        $channel = $this->createElement( 'text', 'channelgroup' );
        $channel->addValidator( 'int' )
            ->setLabel( 'Channel Group Number' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $channel );

        $mtu = $this->createElement( 'text', 'mtu' );
        $mtu->addValidator( 'int' )
            ->setLabel( 'MTU' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
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


        /////////////////////////////////////////////////////////////////////////////////////////////////////
        // PHYSICAL INTERFACE DETAILS

        $this->addElement( IXP_Form_Switch::getPopulatedSelect( 'switchid' ) );
        $this->getElement( 'switchid' )->setAttrib( 'class', 'chzn-select span12' );

        $switchPorts = $this->createElement( 'select', 'switchportid' );
        $switchPorts->setRequired( true )
            ->setRegisterInArrayValidator( false )
            ->setLabel( 'Port' )
            ->setAttrib( 'class', 'chzn-select span12' )
            ->addValidator( 'greaterThan', false, array( 'min' => 1 ) )
            ->setErrorMessages( array( 'Please select a switch port' ) );
        $this->addElement( $switchPorts );


        $status = $this->createElement( 'select', 'status' );
        $status->setMultiOptions( \Entities\PhysicalInterface::$STATES )
            ->setRegisterInArrayValidator( true )
            ->setAttrib( 'class', 'chzn-select span12' )
            ->setLabel( 'Status' )
            ->setErrorMessages( array( 'Please set the status' ) );
        $this->addElement( $status );


        $speed = $this->createElement( 'select', 'speed' );
        $speed->setMultiOptions( \Entities\PhysicalInterface::$SPEED )
            ->setRegisterInArrayValidator( true )
            ->setAttrib( 'class', 'chzn-select span12' )
            ->setLabel( 'Speed' )
            ->setErrorMessages( array( 'Please set the speed' ) );
        $this->addElement( $speed );


        $duplex = $this->createElement( 'select', 'duplex' );
        $duplex->setMultiOptions( \Entities\PhysicalInterface::$DUPLEX )
            ->setRegisterInArrayValidator( true )
            ->setAttrib( 'class', 'chzn-select span12' )
            ->setLabel( 'Duplex' )
            ->setErrorMessages( array( 'Please set the duplex' ) );
        $this->addElement( $duplex );

        $this->addDisplayGroup(
            [ 'switchid', 'switchportid', 'status', 'speed', 'duplex' ],
            'physicalInterfaceDisplayGroup'
        );

        $this->getDisplayGroup( 'physicalInterfaceDisplayGroup' )->setLegend( 'Physical Interface Details' );


        //////////////////////////////////////////////////////////////////////////
        // VLAN INTERFACE DETAILS

        $this->addElement( IXP_Form_Vlan::getPopulatedSelect( 'vlanid' ) );
        $this->getElement( 'vlanid' )
            ->setAttrib( 'class', 'chzn-select span12' );


        $ipv4enabled = $this->createElement( 'checkbox', 'ipv4enabled' );
        $ipv4enabled->setLabel( 'IPv4 Enabled' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv4enabled );

        $ipv4addressid = new OSS_Form_Element_DatabaseDropdown( 'ipv4addressid' );
        $ipv4addressid->setRequired( false )
            ->setChosenOptions( [ "0" => "" ] )
            ->setLabel( 'IPv4 Address' )
            ->addFilter( 'StringTrim' )
            ->setErrorMessages( array( 'Please select or enter a IPv4 address' ) );
        $this->addElement( $ipv4addressid );

        $ipv4hostname = $this->createElement( 'text', 'ipv4hostname' );
        $ipv4hostname->addValidator( 'stringLength', false, array( 1, 64, 'UTF-8' ) )
            ->addValidator( 'hostname', false, [ 'allow' => Zend_Validate_Hostname::ALLOW_DNS ] )
            ->setLabel( 'IPv4 Hostname' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $ipv4hostname  );

        $ipv4bgpmd5secret = $this->createElement( 'text', 'ipv4bgpmd5secret' );
        $ipv4bgpmd5secret->addValidator( 'stringLength', false, array( 1, 64, 'UTF-8' ) )
            ->setLabel( 'IPv4 BGP MD5 Secret' )
            ->setAttrib( 'class', 'span10' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $ipv4bgpmd5secret  );

        $ipv4canping = $this->createElement( 'checkbox', 'ipv4canping' );
        $ipv4canping->setLabel( 'IPv4 Can Ping' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv4canping );

        $ipv4monitorrcbgp = $this->createElement( 'checkbox', 'ipv4monitorrcbgp' );
        $ipv4monitorrcbgp->setLabel( 'IPv4 Monitor RC BGP' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv4monitorrcbgp );

        $this->addDisplayGroup(
            [ 'ipv4addressid', 'ipv4hostname', 'ipv4bgpmd5secret', 'ipv4canping', 'ipv4monitorrcbgp' ],
            'ipv4DisplayGroup'
        );
        $this->getDisplayGroup( 'ipv4DisplayGroup' )->setLegend( 'IPv4 Details' );





        $ipv6enabled = $this->createElement( 'checkbox', 'ipv6enabled' );
        $ipv6enabled->setLabel( 'IPv6 Enabled' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv6enabled );

        $ipv6addressid = new OSS_Form_Element_DatabaseDropdown( 'ipv6addressid' );
        $ipv6addressid->setRequired( false )
            ->setLabel( 'IPv6 Address' )
            ->setChosenOptions( [ "0" => "" ] )
            ->addFilter( 'StringTrim' )
            ->setErrorMessages( array( 'Please select or enter a IPv6 address' ) );
        $this->addElement( $ipv6addressid );

        $ipv6hostname = $this->createElement( 'text', 'ipv6hostname' );
        $ipv6hostname->addValidator( 'stringLength', false, array( 1, 64, 'UTF-8' ) )
            ->addValidator( 'hostname', false, [ 'allow' => Zend_Validate_Hostname::ALLOW_DNS ] )
            ->setLabel( 'IPv6 Hostname' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $ipv6hostname  );

        $ipv6bgpmd5secret = $this->createElement( 'text', 'ipv6bgpmd5secret' );
        $ipv6bgpmd5secret->addValidator( 'stringLength', false, array( 1, 64, 'UTF-8' ) )
            ->setLabel( 'IPv6 BGP MD5 Secret' )
            ->setAttrib( 'class', 'span10' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $ipv6bgpmd5secret  );


        $ipv6canping = $this->createElement( 'checkbox', 'ipv6canping' );
        $ipv6canping->setLabel( 'IPv6 Can Ping' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv6canping );

        $ipv6monitorrcbgp = $this->createElement( 'checkbox', 'ipv6monitorrcbgp' );
        $ipv6monitorrcbgp->setLabel( 'IPv6 Monitor RC BGP' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv6monitorrcbgp );

        $this->addDisplayGroup(
            [ 'ipv6addressid', 'ipv6hostname', 'ipv6bgpmd5secret', 'ipv6canping', 'ipv6monitorrcbgp' ],
            'ipv6DisplayGroup'
        );

        $this->getDisplayGroup( 'ipv6DisplayGroup' )->setLegend( 'IPv6 Details' );



        $irrdbfilter = $this->createElement( 'checkbox', 'irrdbfilter' );
        $irrdbfilter->setLabel( 'Apply IRRDB Filtering' )
            ->setValue( '1' )
            ->setCheckedValue( '1' );
        $this->addElement( $irrdbfilter );

        $mcastenabled = $this->createElement( 'checkbox', 'mcastenabled' );
        $mcastenabled->setLabel( 'Multicast Enabled' )
            ->setCheckedValue( '1' );
        $this->addElement( $mcastenabled );

        $maxbgpprefix = $this->createElement( 'text', 'maxbgpprefix' );
        $maxbgpprefix->addValidator('int')
            ->addValidator( 'greaterThan', false, array( -1 ) )
            ->setRequired( false )
            ->setLabel( 'Max BGP Prefixes' );
        $this->addElement( $maxbgpprefix  );

        $rsclient = $this->createElement( 'checkbox', 'rsclient' );
        $rsclient->setLabel( 'Route Server Client' )
            ->setCheckedValue( '1' );
        $this->addElement( $rsclient );

        $as112client = $this->createElement( 'checkbox', 'as112client' );
        $as112client->setLabel( 'AS112 Client' )
            ->setCheckedValue( '1' );
        $this->addElement( $as112client );

        $busyhost = $this->createElement( 'checkbox', 'busyhost' );
        $busyhost->setLabel( 'Busy host' )
            ->setCheckedValue( '1' );
        $this->addElement( $busyhost );


        $this->addDisplayGroup(
            [ 'irrdbfilter', 'mcastenabled', 'maxbgpprefix', 'rsclient', 'as112client', 'busyhost' ],
            'vlanInterfaceDisplayGroup'
        );

        $this->getDisplayGroup( 'vlanInterfaceDisplayGroup' )->setLegend( 'Other VLAN Interface Settings' );


        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );

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

    /**
     * Enables Fanout ports form elements in customer form
     *
     * @param bool $modeEnabled Status of reseller mode enabled or not.
     * @return IXP_Form_Customer
     */
    public function enableFanoutPort( $modeEnabled )
    {
        if( !$modeEnabled )
            return $this;

        $fanout = $this->createElement( 'checkbox', 'fanout' );
        $fanout->setLabel( 'Associate a fanout port' )
            ->setCheckedValue( '1' );
        $this->addElement( $fanout );

        $switcher = IXP_Form_Switch::getPopulatedSelect( 'fn_switchid' );
        $switcher->setRequired( false )
            ->setAttrib( 'class', 'chzn-select' )
            ->setAttrib( 'chzn-fix-width', '1' )
            ->removeValidator( 'between' );
        $this->addElement( $switcher );

        $switchPorts = $this->createElement( 'select', 'fn_switchportid' );
        $switchPorts->setRequired( false )
            ->setRegisterInArrayValidator( false )
            ->setLabel( 'Port' )
            ->setAttrib( 'class', 'chzn-select' )
            ->setAttrib( 'chzn-fix-width', '1' )
            ->addValidator( 'greaterThan', false, array( 'min' => 1 ) )
            ->setErrorMessages( array( 'Please select a switch port' ) );
        $this->addElement( $switchPorts );

        $preselectSwitchPort = $this->createElement( 'hidden', 'fn_preselectSwitchPort' );
        $this->addElement( $preselectSwitchPort );

        $preselectPhysicalInterface = $this->createElement( 'hidden', 'fn_preselectPhysicalInterface' );
        $this->addElement( $preselectPhysicalInterface );

        $this->addDisplayGroup(
            [ 'fn_switchid', 'fn_switchportid', 'fn_preselectSwitchPort', 'fn_preselectPhysicalInterface' ],
            'fanoutDisplayGroup'
        );

        $this->getDisplayGroup( 'fanoutDisplayGroup' )->setLegend( 'Fanout Port' );

        return $this;
    }

}
