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
 * Form: adding / editing VLAN interfaces
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Interface_Vlan extends IXP_Form
{

    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'vlan-interface/forms/vlan-interface.phtml' ] ] ] );

        $virtualInterface = $this->createElement( 'hidden', 'virtualinterfaceid' );
        $this->addElement( $virtualInterface );

        $this->addElement( IXP_Form_Vlan::getPopulatedSelect( 'vlanid' ) );
        $this->getElement( 'vlanid' )
            ->setAttrib( 'class', 'chzn-select span6' );


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
            array( 'ipv6addressid', 'ipv6hostname', 'ipv6bgpmd5secret', 'ipv6canping', 'ipv6monitorrcbgp' ),
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

        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );

        $preselectIPv4Address = $this->createElement( 'hidden', 'preselectIPv4Address' );
        $this->addElement( $preselectIPv4Address );

        $preselectIPv6Address = $this->createElement( 'hidden', 'preselectIPv6Address' );
        $this->addElement( $preselectIPv6Address );

        $preselectVlanInterface = $this->createElement( 'hidden', 'preselectVlanInterface' );
        $this->addElement( $preselectVlanInterface );

        $preselectCustomer = $this->createElement( 'hidden', 'preselectCustomer' );
        $this->addElement( $preselectCustomer );

    }

}
