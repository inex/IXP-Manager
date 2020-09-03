<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Tests\Browser;

use D2EM;

use Entities\{
    CoreBundle          as CoreBundleEntity,
    CoreInterface       as CoreInterfaceEntity,
    CoreLink            as CoreLinkEntity,
    PhysicalInterface   as PhysicalInterfaceEntity,
    SwitchPort          as SwitchPortEntity,
    VirtualInterface    as VirtualInterfaceEntity
};

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;


class CoreBundleControllerTest extends DuskTestCase
{

    public function tearDown(): void
    {
        foreach( [ 51, 55 ] as $spid ) {
            $sp = D2EM::getRepository( SwitchPortEntity::class )->find( $spid );
            D2EM::refresh($sp);

            if( $sp ) {
                $sp->setType( SwitchPortEntity::TYPE_CORE );
                D2EM::flush();
            }
        }

        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @throws
     */
    public function testAddWizard()
    {



        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->assertPathIs( '/admin' );

            $coreBundlesList = [
                CoreBundleEntity::TYPE_ECMP => [
                    "cust1" => 1,
                    "cost1" => 10,
                    "description1" => "Test description",
                    "graph-title1" => "Test description",
                    "preference1" => 11,
                    "cust2" => 1,
                    "cost2" => 12,
                    "description2" => "Test description 2",
                    "graph-title2" => "Test description 2",
                    "preference2" => 13,
                    "mtu" => 9000,
                    "switch-a" => 1,
                    "switch-a-name" => 'switch1',
                    "switch-b" => 2,
                    "switch-b-name" => 'switch2',
                    "bfd" => 0,
                    "cb-subnet" => null,
                    "cb-subnet2" => null,
                    "stp" => 0,
                    "stp2" => 0,
                    "fast-lacp" => 0,
                    "lag-framing" => 0,
                    'framing' => 1,
                    "speed" => 1000,
                    "switch-port-a-1" => 49,
                    "switch-port-b-1" => 53,
                    "subnet-cl-1" => '192.168.1.0/24',
                    "bfd-cl-1" => 1,
                    "switch-port-a-2" => 50,
                    "switch-port-b-2" => 54,
                    "subnet-cl-2" => '192.168.2.0/24',
                    "bfd-cl-2" => 1,
                    "switch-port-a-3" => 51,
                    "switch-port-a-3-name" => 'GigabitEthernet27',
                    "switch-port-b-3" => 55,
                    "switch-port-b-3-name" => 'GigabitEthernet31',
                    "bfd-cl-3" => 1,
                    "subnet-cl-3" => '192.168.3.0/24',
                    "subnet-cl-4" => '192.168.3.0/24',
                    "bfd-cl-4" => 1,
                ],

                CoreBundleEntity::TYPE_L2_LAG => [
                    "cust1" => 1,
                    "cost1" => 10,
                    "description1" => "Test description",
                    "graph-title1" => "Test description",
                    "preference1" => 11,
                    "cust2" => 1,
                    "cost2" => 12,
                    "description2" => "Test description 2",
                    "graph-title2" => "Test description 2",
                    "preference2" => 13,
                    "mtu" => 9000,
                    "switch-a" => 1,
                    "switch-a-name" => 'switch1',
                    "switch-b" => 2,
                    "switch-b-name" => 'switch2',
                    "bfd" => 0,
                    "cb-subnet" => null,
                    "cb-subnet2" => null,
                    "stp" => 1,
                    "stp2" => 0,
                    "fast-lacp" => 1,
                    "fast-lacp2" => 0,
                    "lag-framing" => 1,
                    "vi-name-a" => "virtual interface a",
                    "vi-channel-group-a" => 60,
                    "vi-name-b" => "virtual interface b",
                    "vi-channel-group-b" => 70,
                    'framing' => 1,
                    "speed" => 1000,
                    "switch-port-a-1" => 49,
                    "switch-port-b-1" => 53,
                    "subnet-cl-1" => null,
                    "bfd-cl-1" => 0,
                    "switch-port-a-2" => 50,
                    "switch-port-b-2" => 54,
                    "subnet-cl-2" => null,
                    "bfd-cl-2" => 0,
                    "switch-port-a-3" => 51,
                    "switch-port-a-3-name" => 'GigabitEthernet27',
                    "switch-port-b-3" => 55,
                    "switch-port-b-3-name" => 'GigabitEthernet31',
                    "bfd-cl-3" => 0,
                    "subnet-cl-3" => null,
                    "subnet-cl-4" => null,
                    "bfd-cl-4" => 0,
                ],

                CoreBundleEntity::TYPE_L3_LAG => [
                    "cust1" => 1,
                    "cost1" => 10,
                    "description1" => "Test description",
                    "graph-title1" => "Test description",
                    "preference1" => 11,
                    "cust2" => 1,
                    "cost2" => 12,
                    "description2" => "Test description 2",
                    "graph-title2" => "Test description 2",
                    "preference2" => 13,
                    "mtu" => 9000,
                    "switch-a" => 1,
                    "switch-a-name" => 'switch1',
                    "switch-b" => 2,
                    "switch-b-name" => 'switch2',
                    "bfd" => 1,
                    "bfd2" => 0,
                    "cb-subnet" => '192.0.2.0/31',
                    "cb-subnet2" => '192.0.5.0/31',
                    "stp" => 0,
                    "stp2" => 0,
                    "fast-lacp" => 1,
                    "fast-lacp2" => 0,
                    "lag-framing" => 0,
                    "vi-name-a" => "virtual interface a",
                    "vi-channel-group-a" => 60,
                    "vi-name-b" => "virtual interface b",
                    "vi-channel-group-b" => 70,
                    'framing' => 1,
                    "speed" => 1000,
                    "switch-port-a-1" => 49,
                    "switch-port-b-1" => 53,
                    "subnet-cl-1" => null,
                    "bfd-cl-1" => 0,
                    "switch-port-a-2" => 50,
                    "switch-port-b-2" => 54,
                    "subnet-cl-2" => null,
                    "bfd-cl-2" => 0,
                    "switch-port-a-3" => 51,
                    "switch-port-a-3-name" => 'GigabitEthernet27',
                    "switch-port-b-3" => 55,
                    "switch-port-b-3-name" => 'GigabitEthernet31',
                    "bfd-cl-3" => 0,
                    "subnet-cl-3" => null,
                    "subnet-cl-4" => null,
                    "bfd-cl-4" => 0,
                ]


            ];

            foreach( $coreBundlesList as $type => $coreBundle ) {
                $browser->visit(    '/interfaces/core-bundle/list' )
                    ->assertSee(    'Core Bundle / List' )
                    ->click(    '#add-cb' )
                    ->click(    '#add-cb-wizard' )
                    ->assertPathIs('/interfaces/core-bundle/add-wizard' )
                    ->assertSee(    'Core Bundles / Add Wizard' );

                // filling forms
                $browser->select(   'customer',     $coreBundle[ 'cust1' ] )
                    ->type(     'description',  $coreBundle[ 'description1' ] )
                    ->type(     'cost',         $coreBundle[ 'cost1' ] )
                    ->assertInputValue( 'graph-title', $coreBundle[ 'graph-title1' ] )
                    ->type(     'preference',   $coreBundle[ 'preference1' ] )
                    ->select( 'type', $type )
                    ->pause( 500 )
                    ->check( 'enabled' )
                    ->pause( 500 )
                    ->check( 'framing' )
                    ->pause( 500 )
                    ->type(  'mtu', $coreBundle[ 'mtu' ] );

                if( $type == CoreBundleEntity::TYPE_ECMP ) {

                    $browser->assertMissing( 'fast-lacp' )
                        ->assertMissing( 'stp' )
                        ->assertMissing( 'bfd' )
                        ->assertMissing( 'vi-name-a' )
                        ->assertMissing( 'vi-name-b' )
                        ->assertMissing( 'vi-channel-number-a' )
                        ->assertMissing( 'vi-channel-number-b' )
                        ->assertMissing( 'subnet' );
                } elseif( $type == CoreBundleEntity::TYPE_L2_LAG ) {
                    $browser->assertMissing( 'bfd' )
                        ->assertMissing( 'subnet' );

                    $browser->check( 'stp' )
                        ->check( 'fast-lacp' )
                        ->type( 'vi-name-a', $coreBundle[ 'vi-name-a' ] )
                        ->type( 'vi-channel-number-a', $coreBundle[ 'vi-channel-group-a' ] )
                        ->type( 'vi-name-b', $coreBundle[ 'vi-name-b' ] )
                        ->type(  'vi-channel-number-b', $coreBundle[ 'vi-channel-group-b' ] );
                } else {
                    $browser->assertMissing( 'stp' );

                    $browser->check( 'bfd' )
                        ->check( 'fast-lacp' )
                        ->type( 'vi-name-a', $coreBundle[ 'vi-name-a' ] )
                        ->type( 'vi-channel-number-a', $coreBundle[ 'vi-channel-group-a' ] )
                        ->type( 'vi-name-b', $coreBundle[ 'vi-name-b' ] )
                        ->type(  'vi-channel-number-b', $coreBundle[ 'vi-channel-group-b' ] )
                        ->type(  'subnet', $coreBundle[ 'cb-subnet' ] );
                }

                $browser->select( 'switch-a',   $coreBundle[ 'switch-a' ] )
                    ->assertSelectMissingOption( 'switch-b', $coreBundle[ 'switch-a' ] )
                    ->select( 'switch-b',   $coreBundle[ 'switch-b' ] )
                    ->select( 'speed',      $coreBundle[ 'speed' ] );

                $browser->waitUntil( "$( '#sp-a-1 option' ).length > 1" );
                $browser->waitUntil( "$( '#sp-b-1 option' ).length > 1" );

                $browser->assertSelectHasOption( '#sp-a-1' , $coreBundle[ 'switch-port-a-1' ] )
                    ->assertSelectHasOption( '#sp-b-1' , $coreBundle[ 'switch-port-b-1' ] );

                $browser->select( '#sp-a-1', $coreBundle[ 'switch-port-a-1' ] )
                    ->select( '#sp-b-1', $coreBundle[ 'switch-port-b-1' ] )
                    ->check( '#enabled-cl-1' );

                if( $type == CoreBundleEntity::TYPE_ECMP ) {
                    $browser->type( '#subnet-1', $coreBundle[ 'subnet-cl-1' ] )
                        ->check( '#bfd-1' )
                        ->assertSee('The subnet is valid' );
                }

                $browser->click( '#add-new-core-link' );

                $browser->assertSee(    'Link 2' )
                    ->pause( 5000)
                    ->assertSelected(   '#sp-a-2', $coreBundle[ 'switch-port-a-2' ] )
                    ->assertSelected(   '#sp-b-2', $coreBundle[ 'switch-port-b-2' ] )
                    ->assertChecked(    '#enabled-cl-2' );

                if( $type == CoreBundleEntity::TYPE_ECMP ) {
                    $browser->assertChecked(    '#bfd-2' )
                        ->assertInputValue( '#subnet-2', $coreBundle[ 'subnet-cl-2' ] );
                }


                $browser->click(         '#add-new-core-link' )
                    ->assertSee(        'Link 3' )
                    ->pause( 5000)
                    ->assertSelected( '#sp-a-3', $coreBundle[ 'switch-port-a-3' ] )
                    ->assertSelected( '#sp-b-3', $coreBundle[ 'switch-port-b-3' ] );

                if( $type == CoreBundleEntity::TYPE_ECMP ) {
                    $browser->assertChecked(  '#bfd-3' )
                        ->assertInputValue('#subnet-3', $coreBundle[ 'subnet-cl-3' ] );
                }

                $browser->assertChecked(  '#enabled-cl-3' )
                    ->click( '#delete-cl-3' )
                    ->assertDontSee( 'Link 3' )
                    ->click( '#core-bundle-submit-btn' )
                    ->assertPathIs('/interfaces/core-bundle/list' )
                    ->assertSee( 'Core bundle created' );

                // Checking values inserted in DB
                /** @var CoreBundleEntity $cb */
                $cb = D2EM::getRepository( CoreBundleEntity::class )->findOneBy( [ 'description' => $coreBundle[ 'description1' ] ] );

                // check value of the core bundle
                $this->assertInstanceOf(CoreBundleEntity::class , $cb );
                $this->assertEquals(    $coreBundle[ 'cust1' ],         $cb->getCustomer()->getId() );
                $this->assertEquals(    $coreBundle[ 'description1' ],  $cb->getDescription() );
                $this->assertEquals(    $type ,                         $cb->getType() );
                $this->assertEquals(    $coreBundle[ 'graph-title1' ] , $cb->getGraphTitle() );
                $this->assertEquals(    $coreBundle[ 'bfd' ] ,          $cb->getBFD() );
                $this->assertEquals(    $coreBundle[ 'cb-subnet' ] ,    $cb->getIPv4Subnet() );
                $this->assertEquals(    $coreBundle[ 'cb-subnet' ],    $cb->getIPv4Subnet() );

                $this->assertEquals(    $coreBundle[ 'stp' ],           $cb->getSTP() );
                $this->assertEquals(    $coreBundle[ 'cost1' ],         $cb->getCost() );
                $this->assertEquals(    $coreBundle[ 'preference1' ],   $cb->getPreference() );
                $this->assertTrue(      $cb->getEnabled() );


                // check value Core links
                $this->assertEquals( 2 , count( $cls = $cb->getCoreLinks() ) );

                $i = 1;
                /** @var CoreLinkEntity $cl */
                foreach( $cls as $cl ) {

                    $this->assertInstanceOf(CoreLinkEntity::class , $cl );
                    $this->assertEquals(    $cb->getId(),                   $cl->getCoreBundle()->getId() );
                    $this->assertTrue(      $cl->getEnabled() );
                    $this->assertEquals(    $coreBundle[ 'subnet-cl-' . $i ],   $cl->getIPv4Subnet() );
                    $this->assertEquals( $coreBundle[ "bfd-cl-{$i}" ], $cl->getBFD() );


                    /** @var CoreInterfaceEntity $ci */
                    foreach( [ 'a', 'b' ] as $side ){
                        $getCoreInterfaceSide = "getCoreInterfaceSide" . strtoupper( $side );
                        $ci = $cl->$getCoreInterfaceSide();

                        $this->assertInstanceOf(CoreInterfaceEntity::class , $ci );

                        /** @var PhysicalInterfaceEntity $pi */
                        $this->assertInstanceOf(PhysicalInterfaceEntity::class , $pi = $ci->getPhysicalInterface() );
                        $this->assertTrue(      $pi->getAutoneg() );
                        $this->assertEquals(    $coreBundle[ "switch-port-{$side}-{$i}" ],   $pi->getSwitchPort()->getId() );
                        $this->assertEquals(    $coreBundle[ 'speed' ],             $pi->getSpeed() );
                        $this->assertEquals('full', $pi->getDuplex() );
                        $this->assertEquals( PhysicalInterfaceEntity::STATUS_CONNECTED, $pi->getStatus() );

                        /** @var VirtualInterfaceEntity $vi */
                        $this->assertInstanceOf(VirtualInterfaceEntity::class , $vi = $pi->getVirtualInterface() );

                        $this->assertEquals( $coreBundle[ 'cust1' ] , $vi->getCustomer()->getId() );

                        if( $type == CoreBundleEntity::TYPE_ECMP ){
                            $this->assertNull(  $vi->getName() );
                            $this->assertNull(  $vi->getDescription() );
                            $this->assertNull(      $vi->getChannelgroup() );
                        } else {
                            $this->assertEquals( $coreBundle[ "vi-name-{$side}" ], $vi->getName() );
                            $this->assertEquals( $coreBundle[ "vi-channel-group-{$side}" ], $vi->getChannelgroup() );
                        }

                        $this->assertEquals(    $coreBundle[ 'mtu' ],       $vi->getMtu() );
                        $this->assertEquals(    $coreBundle[ 'framing' ],   $vi->getTrunk() );

                        $this->assertEquals(    $coreBundle[ 'lag-framing' ],   $vi->getLagFraming() );
                        $this->assertEquals(    $coreBundle[ 'fast-lacp' ],     $vi->getFastLACP() );

                    }

                    $i++;
                }


                /**
                 *
                 * Edit Core bundle type ECMP
                 *
                 */
                $browser->visit( '/interfaces/core-bundle/list' )
                    ->click( '#edit-cb-' . $cb->getId() );

                $browser->assertSelected(   'customer',         $coreBundle[ 'cust1' ] )
                    ->assertInputValue( 'description',      $coreBundle[ 'description1' ] )
                    ->assertInputValue( 'cost',             $coreBundle[ 'cost1' ])
                    ->assertInputValue( 'graph-title',      $coreBundle[ 'graph-title1' ] )
                    ->assertInputValue( 'preference',       $coreBundle[ 'preference1' ])
                    ->assertSelected(   'type',             $type )
                    ->assertChecked(    'enabled' );

                $browser->select(   'customer',     $coreBundle[ 'cust2' ] )
                    ->type(     'description',  $coreBundle[ 'description2' ] )
                    ->type(     'graph-title',  $coreBundle[ 'graph-title2' ] )
                    ->type(     'cost',         $coreBundle[ 'cost2' ] )
                    ->type(     'preference',   $coreBundle[ 'preference2' ] )
                    ->uncheck(  'enabled' );

                if( $type == CoreBundleEntity::TYPE_L2_LAG ) {
                    $browser->uncheck(   'stp' );
                }

                if( $type == CoreBundleEntity::TYPE_L3_LAG ) {
                    $browser->type(  'subnet', $coreBundle[ 'cb-subnet2' ] );
                }

                $browser->click( '#core-bundle-submit-btn' );

                if( $type == CoreBundleEntity::TYPE_L3_LAG ){
                    $browser->click( '#core-bundle-submit-btn' );
                }

                $browser->assertSee('Core bundle updated' );


                D2EM::refresh( $cb );

                foreach( $cb->getCoreLinks() as $cl ){
                    D2EM::refresh( $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getVirtualInterface() );
                }

                $this->assertEquals(    $coreBundle[ 'cust2' ],         $cb->getCustomer()->getId() );
                $this->assertEquals(    $coreBundle[ 'description2' ],  $cb->getDescription() );
                $this->assertEquals(    $type , $cb->getType() );
                $this->assertEquals(    $coreBundle[ 'graph-title2' ],  $cb->getGraphTitle() );
                $this->assertEquals(    $coreBundle[ 'bfd' ],           $cb->getBFD() );
                $this->assertEquals(    $coreBundle[ 'cb-subnet2' ],    $cb->getIPv4Subnet() );

                $this->assertEquals(    $coreBundle[ 'stp2' ] , $cb->getSTP() );
                $this->assertEquals(    $coreBundle[ 'cost2' ] , $cb->getCost() );
                $this->assertEquals(    $coreBundle[ 'preference2' ] , $cb->getPreference() );
                $this->assertFalse(     $cb->getEnabled() );


                /**
                 *
                 * Edit Core links for Core Bundle
                 *
                 */
                $browser->visit( '/interfaces/core-bundle/edit/' . $cb->getId() );

                $browser->assertSee( 'Side A' )
                    ->assertSee( 'Side B' )
                    ->assertSee( 'Switch A : ' . $coreBundle[ 'switch-a-name' ] )
                    ->assertSee( 'Switch B : ' . $coreBundle[ 'switch-b-name' ] );

                /** @var CoreLinkEntity $cl */
                foreach( $cb->getCoreLinks() as $cl ){
                    $browser->assertSee( $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getSwitchPort()->getName() );
                    $browser->assertSee( $cl->getCoreInterfaceSideB()->getPhysicalInterface()->getSwitchPort()->getName() );

                    if( $type == CoreBundleEntity::TYPE_ECMP ) {
                        $browser->assertChecked( 'bfd-' . $cl->getId() );
                        $browser->assertInputValue( 'subnet-' . $cl->getId(), $cl->getIPv4Subnet() );
                        $browser->uncheck(  'bfd-' . $cl->getId() )
                            ->uncheck(  'bfd-' . $cl->getId() )
                            ->type(     'subnet-' . $cl->getId() , $coreBundle[ 'subnet-cl-4' ] );
                    }



                    $browser->uncheck(  'enabled-' . $cl->getId() )
                        ->uncheck(  'enabled-' . $cl->getId() );

                }

                $browser->click( '#core-links-submit-btn' )
                    ->click( '#core-links-submit-btn' )
                    ->assertSee( 'Core links updated.' );

                D2EM::refresh( $cb );

                foreach( $cb->getCoreLinks() as $cl ){
                    D2EM::refresh( $cl );
                    $this->assertEquals( 0, $cl->getEnabled() );
                    $this->assertEquals( 0, $cl->getBFD() );
                    $this->assertEquals( $coreBundle[ 'subnet-cl-4' ] , $cl->getIPv4Subnet() );

                }


                $browser->click( '#add-new-core-link' )
                    ->assertSee( 'New Core Link' )
                    ->select( '#sp-a-1', $coreBundle[ 'switch-port-a-3' ] )
                    ->select( '#sp-b-1', $coreBundle[ 'switch-port-b-3' ] )
                    ->check( '#enabled-1' );

                if( $type == CoreBundleEntity::TYPE_ECMP ) {
                    $browser->check( '#bfd-1' )
                        ->type( '#cl-subnet-1', $coreBundle[ 'subnet-cl-3' ] );
                }

                $browser->click('#new-core-links-submit-btn');

                if( $type == CoreBundleEntity::TYPE_ECMP ) {
                    $browser->click('#new-core-links-submit-btn');
                }

                $browser->assertSee( 'Core link added.' );

                D2EM::refresh( $cb );

                $this->assertEquals( 3 , count( $cb->getCoreLinks() ) );
                /** @var $cl3 CoreLinkEntity */
                $this->assertInstanceOf( CoreLinkEntity::class ,  $cl3 = $cb->getCoreLinks()->last() );

                $this->assertEquals( $coreBundle[ 'switch-port-a-3-name' ] , $cl3->getCoreInterfaceSideA()->getPhysicalInterface()->getSwitchPort()->getName() );
                $this->assertEquals( $coreBundle[ 'switch-port-b-3-name' ] , $cl3->getCoreInterfaceSideB()->getPhysicalInterface()->getSwitchPort()->getName() );

                $this->assertEquals( $coreBundle[ 'bfd-cl-3' ] , $cl3->getBFD() );
                $this->assertEquals( true , $cl3->getEnabled() );
                $this->assertEquals( $coreBundle[ 'subnet-cl-4' ], $cl3->getIPv4Subnet() );

                $cl3id = $cl3->getId();

                $browser->visit( '/interfaces/core-bundle/edit/' . $cb->getId() )
                    ->click( '#delete-cl-' . $cl3->getId() )
                    ->pause( 500 )
                    ->press( 'Delete' );


                D2EM::refresh( $cl3 );

                $this->assertEquals( null, D2EM::getRepository( CoreLinkEntity::class )->findOneBy( [ 'id' => $cl3id ] ) );

                $browser->visit( '/interfaces/core-bundle/edit/' . $cb->getId() )
                    ->click( '#cb-delete-' . $cb->getId() )
                    ->pause( 500 )
                    ->press( 'Delete' );


                $cbid = $cb->getId();

                D2EM::refresh( $cb );

                $this->assertEquals( null, D2EM::getRepository( CoreBundleEntity::class )->findOneBy( [ 'id' => $cbid ] ) );


            }
        });
    }
}