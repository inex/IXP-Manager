<?php

namespace Tests\Browser;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use IXP\Models\{
    CoreBundle,
    CoreInterface,
    CoreLink,
    PhysicalInterface,
    Switcher,
    SwitchPort,
    VirtualInterface
};

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

/**
 * Test CoreBundle Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CoreBundleControllerTest extends DuskTestCase
{

    public function tearDown(): void
    {
        foreach( [ 51, 55 ] as $spid ) {
            $sp = SwitchPort::find( $spid );

            if( $sp ) {
                $sp->type = SwitchPort::TYPE_CORE;
                $sp->save();
            }
        }
        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     *
     * @throws
     */
    public function testAddWizard(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->assertPathIs( '/admin' );

            $coreBundlesList = [
                CoreBundle::TYPE_ECMP => [
                    "cust1"             => 1,
                    "cost1"             => 10,
                    "description1"      => "Test description",
                    "graph-title1"      => "Test description",
                    "preference1"       => 11,
                    "cust2"             => 1,
                    "cost2"             => 12,
                    "description2"      => "Test description 2",
                    "graph-title2"      => "Test description 2",
                    "preference2"       => 13,
                    "mtu"               => 9000,
                    "switch-a"          => 1,
                    "switch-a-name"     => 'switch1',
                    "switch-b"          => 2,
                    "switch-b-name"     => 'switch2',
                    "bfd"               => 0,
                    "cb-subnet"         => null,
                    "cb-subnet2"        => null,
                    "stp"               => 0,
                    "stp2"              => 0,
                    "fast-lacp"         => 0,
                    "lag-framing"       => 0,
                    'framing'           => 1,
                    "speed"             => 1000,
                    "switch-port-a-1"   => 49,
                    "switch-port-b-1"   => 53,
                    "subnet-cl-1"       => '192.168.1.0/24',
                    "bfd-cl-1"          => 1,
                    "switch-port-a-2"   => 50,
                    "switch-port-b-2"   => 54,
                    "subnet-cl-2"       => '192.168.2.0/24',
                    "bfd-cl-2"          => 1,
                    "switch-port-a-3"   => 51,
                    "switch-port-a-3-name"  => 'GigabitEthernet27',
                    "switch-port-b-3"       => 55,
                    "switch-port-b-3-name"  => 'GigabitEthernet31',
                    "bfd-cl-3"              => 1,
                    "subnet-cl-3"           => '192.168.3.0/24',
                    "subnet-cl-4"           => '192.168.3.0/24',
                    "bfd-cl-4"              => 1,
                ],

                CoreBundle::TYPE_L2_LAG => [
                    "cust1"                     => 1,
                    "cost1"                     => 10,
                    "description1"              => "Test description",
                    "graph-title1"              => "Test description",
                    "preference1"               => 11,
                    "cust2"                     => 1,
                    "cost2"                     => 12,
                    "description2"              => "Test description 2",
                    "graph-title2"              => "Test description 2",
                    "preference2"               => 13,
                    "mtu"                       => 9000,
                    "switch-a"                  => 1,
                    "switch-a-name"             => 'switch1',
                    "switch-b"                  => 2,
                    "switch-b-name"             => 'switch2',
                    "bfd"                       => 0,
                    "cb-subnet"                 => null,
                    "cb-subnet2"                => null,
                    "stp"                       => 1,
                    "stp2"                      => 0,
                    "fast-lacp"                 => 1,
                    "fast-lacp2"                => 0,
                    "lag-framing"               => 1,
                    "vi-name-a"                 => "virtual interface a",
                    "vi-channel-group-a"        => 60,
                    "vi-name-b"                 => "virtual interface b",
                    "vi-channel-group-b"        => 70,
                    'framing'                   => 1,
                    "speed"                     => 1000,
                    "switch-port-a-1"           => 49,
                    "switch-port-b-1"           => 53,
                    "subnet-cl-1"               => null,
                    "bfd-cl-1"                  => 0,
                    "switch-port-a-2"           => 50,
                    "switch-port-b-2"           => 54,
                    "subnet-cl-2"               => null,
                    "bfd-cl-2"                  => 0,
                    "switch-port-a-3"           => 51,
                    "switch-port-a-3-name"      => 'GigabitEthernet27',
                    "switch-port-b-3"           => 55,
                    "switch-port-b-3-name"      => 'GigabitEthernet31',
                    "bfd-cl-3"                  => 0,
                    "subnet-cl-3"               => null,
                    "subnet-cl-4"               => null,
                    "bfd-cl-4"                  => 0,
                ],

                CoreBundle::TYPE_L3_LAG => [
                    "cust1"                     => 1,
                    "cost1"                     => 10,
                    "description1"              => "Test description",
                    "graph-title1"              => "Test description",
                    "preference1"               => 11,
                    "cust2"                     => 1,
                    "cost2"                     => 12,
                    "description2"              => "Test description 2",
                    "graph-title2"              => "Test description 2",
                    "preference2"               => 13,
                    "mtu"                       => 9000,
                    "switch-a"                  => 1,
                    "switch-a-name"             => 'switch1',
                    "switch-b"                  => 2,
                    "switch-b-name"             => 'switch2',
                    "bfd"                       => 1,
                    "bfd2"                      => 0,
                    "cb-subnet"                 => '192.0.2.0/31',
                    "cb-subnet2"                => '192.0.5.0/31',
                    "stp"                       => 0,
                    "stp2"                      => 0,
                    "fast-lacp"                 => 1,
                    "fast-lacp2"                => 0,
                    "lag-framing"               => 0,
                    "vi-name-a"                 => "virtual interface a",
                    "vi-channel-group-a"        => 60,
                    "vi-name-b"                 => "virtual interface b",
                    "vi-channel-group-b"        => 70,
                    'framing'                   => 1,
                    "speed"                     => 1000,
                    "switch-port-a-1"           => 49,
                    "switch-port-b-1"           => 53,
                    "subnet-cl-1"               => null,
                    "bfd-cl-1"                  => 0,
                    "switch-port-a-2"           => 50,
                    "switch-port-b-2"           => 54,
                    "subnet-cl-2"               => null,
                    "bfd-cl-2"                  => 0,
                    "switch-port-a-3"           => 51,
                    "switch-port-a-3-name"      => 'GigabitEthernet27',
                    "switch-port-b-3"           => 55,
                    "switch-port-b-3-name"      => 'GigabitEthernet31',
                    "bfd-cl-3"                  => 0,
                    "subnet-cl-3"               => null,
                    "subnet-cl-4"               => null,
                    "bfd-cl-4"                  => 0,
                ]
            ];

            foreach( $coreBundlesList as $type => $coreBundle ) {
                $browser->visit(    '/interfaces/core-bundle/list' )
                    ->assertSee(    'Core Bundle / List' )
//                    ->click(    '#add-cb' )
                    ->click(    '#add-cb-wizard' )
                    ->assertPathIs('/interfaces/core-bundle/create-wizard' )
                    ->assertSee(    'Core Bundles / Create Wizard' );

                // filling forms
                $browser->select(   'custid',       $coreBundle[ 'cust1' ] )
                        ->type(     'description',  $coreBundle[ 'description1' ] )
                        ->type(     'cost',         $coreBundle[ 'cost1' ] )
                        ->assertInputValue( 'graph_title', $coreBundle[ 'graph-title1' ] )
                        ->type(     'preference',   $coreBundle[ 'preference1' ] )
                        ->select( 'type', $type )
                        ->pause( 500 )
                        ->check( 'enabled' )
                        ->pause( 500 )
                        ->check( 'framing' )
                        ->pause( 500 )
                        ->type(  'mtu', $coreBundle[ 'mtu' ] );

                if( $type === CoreBundle::TYPE_ECMP ) {
                    $browser->assertMissing( 'fast-lacp' )
                            ->assertMissing( 'stp' )
                            ->assertMissing( 'bfd' )
                            ->assertMissing( 'vi-name-a' )
                            ->assertMissing( 'vi-name-b' )
                            ->assertMissing( 'vi-channel-number-a' )
                            ->assertMissing( 'vi-channel-number-b' )
                            ->assertMissing( 'subnet' );
                } elseif( $type === CoreBundle::TYPE_L2_LAG ) {
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
                            ->type(  'ipv4_subnet', $coreBundle[ 'cb-subnet' ] );
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

                if( $type === CoreBundle::TYPE_ECMP ) {
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

                if( $type === CoreBundle::TYPE_ECMP ) {
                    $browser->assertChecked(    '#bfd-2' )
                        ->assertInputValue( '#subnet-2', $coreBundle[ 'subnet-cl-2' ] );
                }

                $browser->driver->executeScript('window.scrollTo(0, 1000);');

                $browser->click(         '#add-new-core-link' )
                    ->assertSee(        'Link 3' )
                    ->pause( 5000)
                    ->assertSelected( '#sp-a-3', $coreBundle[ 'switch-port-a-3' ] )
                    ->assertSelected( '#sp-b-3', $coreBundle[ 'switch-port-b-3' ] );

                if( $type === CoreBundle::TYPE_ECMP ) {
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
                /** @var CoreBundle $cb */
                $cb = CoreBundle::whereDescription( $coreBundle[ 'description1' ] )->first();

                // check value of the core bundle
                $this->assertInstanceOf(CoreBundle::class , $cb );
                $this->assertEquals(    $coreBundle[ 'cust1' ],         $cb->customer()->id );
                $this->assertEquals(    $coreBundle[ 'description1' ],  $cb->description );
                $this->assertEquals(    $type ,                         $cb->type );
                $this->assertEquals(    $coreBundle[ 'graph-title1' ] , $cb->graph_title );
                $this->assertEquals(    $coreBundle[ 'bfd' ] ,          $cb->bfd );
                $this->assertEquals(    $coreBundle[ 'cb-subnet' ] ,    $cb->ipv4_subnet );

                $this->assertEquals(    $coreBundle[ 'stp' ],           $cb->stp );
                $this->assertEquals(    $coreBundle[ 'cost1' ],         $cb->cost );
                $this->assertEquals(    $coreBundle[ 'preference1' ],   $cb->preference );
                $this->assertTrue(      (bool)$cb->enabled );


                // check value Core links
                $cls = $cb->corelinks;
                $this->assertEquals( 2 , $cls->count() );

                $i = 1;
                /** @var CoreLink $cl */
                foreach( $cls as $cl ) {
                    $this->assertInstanceOf(CoreLink::class , $cl );
                    $this->assertEquals(    $cb->id,                            $cl->core_bundle_id );
                    $this->assertEquals(    $coreBundle[ 'subnet-cl-' . $i ],   $cl->ipv4_subnet    );
                    $this->assertEquals(    $coreBundle[ "bfd-cl-{$i}" ],       $cl->bfd            );
                    $this->assertTrue(      (bool)$cl->enabled );

                    /** @var CoreInterface $ci */
                    foreach( [ 'a', 'b' ] as $side ){
                        $coreInterfaceSide = "coreInterfaceSide" . strtoupper( $side );
                        $ci = $cl->$coreInterfaceSide;

                        $this->assertInstanceOf(CoreInterface::class , $ci );

                        /** @var PhysicalInterface $pi */
                        $this->assertInstanceOf(PhysicalInterface::class ,              $pi = $ci->physicalInterface    );
                        $this->assertEquals(    $coreBundle[ "switch-port-{$side}-{$i}" ],      $pi->switchportid               );
                        $this->assertEquals(    $coreBundle[ 'speed' ],                         $pi->speed                      );
                        $this->assertEquals('full',                                     $pi->duplex                     );
                        $this->assertEquals( PhysicalInterface::STATUS_CONNECTED,       $pi->status                     );
                        $this->assertTrue(      (bool)$pi->autoneg );

                        /** @var VirtualInterface $vi */
                        $this->assertInstanceOf(VirtualInterface::class , $vi = $pi->virtualInterface );

                        $this->assertEquals( $coreBundle[ 'cust1' ] , $vi->custid );

                        if( $type === CoreBundle::TYPE_ECMP ){
                            $this->assertNull(  $vi->name           );
                            $this->assertNull(  $vi->description    );
                            $this->assertNull(  $vi->channelgroup   );
                        } else {
                            $this->assertEquals( $coreBundle[ "vi-name-{$side}" ],          $vi->name           );
                            $this->assertEquals( $coreBundle[ "vi-channel-group-{$side}" ], $vi->channelgroup   );
                        }

                        $this->assertEquals(    $coreBundle[ 'mtu' ],           $vi->mtu            );
                        $this->assertEquals(    $coreBundle[ 'framing' ],       $vi->trunk          );
                        $this->assertEquals(    $coreBundle[ 'lag-framing' ],   $vi->lag_framing    );
                        $this->assertEquals(    $coreBundle[ 'fast-lacp' ],     $vi->fastlacp       );
                    }
                    $i++;
                }


                /**
                 *
                 * Edit Core bundle type ECMP
                 *
                 */
                $browser->visit( '/interfaces/core-bundle/list' )
                    ->click( '#edit-cb-' . $cb->id );

                $browser->assertSelected(   'custid',           $coreBundle[ 'cust1' ]          )
                        ->assertInputValue( 'description',      $coreBundle[ 'description1' ]   )
                        ->assertInputValue( 'cost',             $coreBundle[ 'cost1' ]          )
                        ->assertInputValue( 'graph_title',      $coreBundle[ 'graph-title1' ]   )
                        ->assertInputValue( 'preference',       $coreBundle[ 'preference1' ]    )
                        ->assertSelected(   'type',             $type                           )
                        ->assertChecked(    'enabled' );

                $browser->select(   'custid',       $coreBundle[ 'cust2' ] )
                        ->type(     'description',  $coreBundle[ 'description2' ] )
                        ->type(     'graph_title',  $coreBundle[ 'graph-title2' ] )
                        ->type(     'cost',         $coreBundle[ 'cost2' ] )
                        ->type(     'preference',   $coreBundle[ 'preference2' ] )
                        ->uncheck(  'enabled' );

                if( $type === CoreBundle::TYPE_L2_LAG ) {
                    $browser->uncheck(   'stp' );
                }

                if( $type === CoreBundle::TYPE_L3_LAG ) {
                    $browser->type(  'ipv4_subnet', $coreBundle[ 'cb-subnet2' ] );
                }

                $browser->click( '#core-bundle-submit-btn' );

                $browser->assertSee('Core bundle updated' );


                $cb->refresh();

                foreach( $cb->corelinks as $cl ){
                    $cl->coreInterfaceSideA->physicalInterface->virtualInterface->refresh();
                }

                $this->assertEquals(    $coreBundle[ 'cust2' ],         $cb->customer()->id );
                $this->assertEquals(    $coreBundle[ 'description2' ],  $cb->description    );
                $this->assertEquals(    $type,                         $cb->type            );
                $this->assertEquals(    $coreBundle[ 'graph-title2' ],  $cb->graph_title    );
                $this->assertEquals(    $coreBundle[ 'bfd' ],           $cb->bfd            );
                $this->assertEquals(    $coreBundle[ 'cb-subnet2' ],    $cb->ipv4_subnet    );
                $this->assertEquals(    $coreBundle[ 'stp2' ],          $cb->stp            );
                $this->assertEquals(    $coreBundle[ 'cost2' ],         $cb->cost           );
                $this->assertEquals(    $coreBundle[ 'preference2' ],   $cb->preference     );
                $this->assertFalse(     (bool)$cb->enabled );

                /**
                 *
                 * Edit Core links for Core Bundle
                 *
                 */
                $browser->visit( '/interfaces/core-bundle/edit/' . $cb->id );

                $browser->assertSee( 'Side A' )
                        ->assertSee( 'Side B' )
                        ->assertSee( 'Switch A: ' . $coreBundle[ 'switch-a-name' ] )
                        ->assertSee( 'Switch B: ' . $coreBundle[ 'switch-b-name' ] );

                foreach( $cb->corelinks as $cl ){
                    $browser->assertSee( $cl->coreInterfaceSideA->physicalInterface->switchPort->name );
                    $browser->assertSee( $cl->coreInterfaceSideB->physicalInterface->switchPort->name );

                    if( $type === CoreBundle::TYPE_ECMP ) {
                        $browser->assertChecked( 'bfd-' . $cl->id );
                        $browser->assertInputValue( 'subnet-' . $cl->id, $cl->ipv4_subnet );
                        $browser->driver->executeScript('window.scrollTo(0, 1000);');
                        $browser->uncheck(  'bfd-' . $cl->id )
                                ->uncheck(  'bfd-' . $cl->id )
                                ->type(     'subnet-' . $cl->id , $coreBundle[ 'subnet-cl-4' ] );
                    }
                    $browser->driver->executeScript('window.scrollTo(0, 3000);');
                    $browser->pause( 500 )
                            ->uncheck(  'enabled-' . $cl->id )
                            ->uncheck(  'enabled-' . $cl->id );
                }

                $browser->driver->executeScript('window.scrollTo(0, 3000);');
                $browser->pause( 500 )
                        ->click( '#core-links-submit-btn' )
                        ->assertSee( 'Core links updated.' );

                $cb->refresh();

                foreach( $cb->corelinks as $cl ){
                    $cl->refresh();
                    $this->assertEquals( 0, $cl->enabled );
                    $this->assertEquals( 0, $cl->bfd );
                    $this->assertEquals( $coreBundle[ 'subnet-cl-4' ] , $cl->ipv4_subnet );
                }

                $browser->driver->executeScript('window.scrollTo(0, 3000);');
                $browser->pause( 500 )
                        ->click( '#btn-create-cl' )
                        ->assertSee( 'New Core Link' )
                        ->select( '#sp-a-1', $coreBundle[ 'switch-port-a-3' ] )
                        ->select( '#sp-b-1', $coreBundle[ 'switch-port-b-3' ] )
                        ->check( '#enabled-1' );

                if( $type === CoreBundle::TYPE_ECMP ) {
                    $browser->driver->executeScript('window.scrollTo(0, 3000);');
                    $browser->pause( 500 )
                            ->check( '#bfd-1' )
                            ->type( '#cl-subnet-1', $coreBundle[ 'subnet-cl-3' ] );
                }
                $browser->driver->executeScript('window.scrollTo(0, 3000);');
                $browser->pause( 500 )
                        ->click('#new-core-links-submit-btn');

                if( $type === CoreBundle::TYPE_ECMP ) {
                    $browser->click('#new-core-links-submit-btn');
                }

                $browser->assertSee( 'Core link created.' );

                $cb->refresh();

                $this->assertEquals( 3 , $cb->corelinks->count() );

                /** @var $cl3 CoreLink */
                $this->assertInstanceOf( CoreLink::class ,  $cl3 = $cb->corelinks->last() );
                $this->assertEquals( $coreBundle[ 'switch-port-a-3-name' ], $cl3->coreInterfaceSideA->physicalInterface->switchPort->name );
                $this->assertEquals( $coreBundle[ 'switch-port-b-3-name' ], $cl3->coreInterfaceSideB->physicalInterface->switchPort->name );
                $this->assertEquals( $coreBundle[ 'bfd-cl-3' ],             $cl3->bfd           );
                $this->assertEquals( true,                          $cl3->enabled       );
                $this->assertEquals( $coreBundle[ 'subnet-cl-4' ],          $cl3->ipv4_subnet   );

                $cl3id = $cl3->id;

                $browser->visit( '/interfaces/core-bundle/edit/' . $cb->id );
                $browser->driver->executeScript('window.scrollTo(0, 3000);');
                $browser->pause( 500    )
                        ->click( '#btn-delete-cl-' . $cl3->id )
                        ->pause( 500    )
                        ->press( 'Delete'   );

                $this->assertEquals( null, CoreLink::find( $cl3id ) );

                $cbid = $cb->id;

                $browser->visit( '/interfaces/core-bundle/edit/' . $cb->id );
                $browser->driver->executeScript('window.scrollTo(0, 3000);');
                $browser->pause( 500    )
                        ->click( '#btn-delete-cb' )
                        ->pause( 500    )
                        ->press( 'Delete'   );

                $this->assertEquals( null, CoreBundle::find( $cbid ) );
            }
        });
    }
}