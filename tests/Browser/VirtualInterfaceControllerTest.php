<?php

namespace Tests\Browser;

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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
    PhysicalInterface,
    VirtualInterface,
    VlanInterface
};

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Test Virtual interface Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VirtualInterfaceControllerTest extends DuskTestCase
{
    /**
     * Test the whole Interfaces functionalities (virtuel, physical, vlan)
     *
     * @return void
     *
     * @throws
     */
    public function testAddWizard(): void
    {
        $this->browse( function ( Browser $browser ) {
            $browser->maximize()
                ->visit('/logout' )
                ->visit('/login' )
                ->type('username', 'travis' )
                ->type('password', 'travisci' )
                ->press('#login-btn' )
                ->waitForLocation('/admin' );

            $vi = $this->intTestVi( $browser );

            $this->intTestPi( $browser, $vi );
            $this->intTestVli( $browser, $vi );

            // Delete Virtual interface
            $browser->press( "#delete-vi-" . $vi->id )
                ->waitForText( 'Do you really want to delete this Virtual Interface?' )
                ->press( "Delete" )
                ->waitForLocation( route( 'customer@overview', [ 'cust' => $vi->custid, 'tab' => 'ports' ] ) )
                ->assertSee('Virtual interface deleted.' );
        });
    }

    /**
     * Test the Virtual interface add/edit/delete functions
     *
     * @param Browser $browser
     *
     * @return VirtualInterface $vi
     *
     * @throws
     */
    private function intTestVi( Browser $browser ): VirtualInterface
    {
        $browser->visit( route( 'virtual-interface@create-wizard-for-cust', 5 ) )
            ->assertSee('Virtual Interface Settings' );

        // Create a new Vitural interface Via wizard form
        $browser->select('vlanid',  '2' )
                ->check( 'ipv4enabled' )
                ->waitFor( "#ipv4-area" )
                ->check( 'ipv6enabled' )
                ->waitFor( "#ipv6-area" )
                ->select( 'switch', '2' )
                ->waitUntilMissing( "Choose a switch port" )
                ->waitForText( "Choose a switch port" )
                ->select( 'switchportid','28'    )
                ->select( 'status',     '4'     )
                ->select( 'speed',      '1000'  )
                ->select( 'duplex',     'full'  )
                ->check( 'rsclient'     )
                ->check( 'irrdbfilter'  )
                ->check( 'as112client'  )
                ->select( 'ipv4address',   '10.2.0.22'         )
                ->select( 'ipv6address',   '2001:db8:2::22'    )
                ->type( 'ipv4hostname',    'v4.example.com'    )
                ->type( 'ipv6hostname',    'v6.example.com'    )
                ->type( 'ipv4bgpmd5secret', 'soopersecret'   )
                ->type( 'ipv6bgpmd5secret', 'soopersecret'   )
                ->type( 'ipv4maxbgpprefix', '200'   )
                ->type( 'ipv6maxbgpprefix', '100'   )
                ->check( 'ipv4canping'        )
                ->check( 'ipv6canping'        )
                ->check( 'ipv4monitorrcbgp'   )
                ->check( 'ipv6monitorrcbgp'        )
                ->press( 'Create' )
                ->waitForText('Virtual interface created', 10000 );

        $url = explode( '/', $browser->driver->getCurrentURL() );

        // Check data in DB
        /** @var $vi VirtualInterface */
        $this->assertInstanceOf( VirtualInterface::class , $vi = VirtualInterface::find( array_pop( $url ) ) );

        // check the values of the Virtual interface object
        $this->assertEquals( 5,     $vi->custid         );
        $this->assertEquals( "",    $vi->name           );
        $this->assertEquals( null,  $vi->mtu            );
        $this->assertEquals( false, $vi->trunk          );
        $this->assertEquals( null,  $vi->channelgroup   );
        $this->assertEquals( false, $vi->lag_framing    );
        $this->assertEquals( false, $vi->fastlacp       );

        // check that we have 1 physical interface for the virtual interface
        $this->assertEquals( 1, $vi->vlanInterfaces()->count() );

        // check the values of the Vlan interface object
        /** @var VlanInterface $vli */
        $vli = $vi->vlanInterfaces()->first();

        $this->assertEquals( "10.2.0.22",       $vli->ipv4address->address  );
        $this->assertEquals( "2001:db8:2::22",  $vli->ipv6address->address  );
        $this->assertEquals( 2,                 $vli->vlanid                );
        $this->assertEquals( true,              $vli->ipv4enabled           );
        $this->assertEquals( true,              $vli->ipv6enabled           );
        $this->assertEquals( "v4.example.com",  $vli->ipv4hostname          );
        $this->assertEquals( "v6.example.com",  $vli->ipv6hostname          );
        $this->assertEquals( "200",             $vli->ipv4maxbgpprefix      );
        $this->assertEquals( "100",             $vli->ipv6maxbgpprefix      );
        $this->assertEquals( false,             $vli->mcastenabled          );
        $this->assertEquals( true,              $vli->irrdbfilter           );
        $this->assertEquals( "soopersecret",    $vli->ipv4bgpmd5secret      );
        $this->assertEquals( "soopersecret",    $vli->ipv6bgpmd5secret      );
        $this->assertEquals( true,              $vli->rsclient              );
        $this->assertEquals( true,              $vli->ipv4canping           );
        $this->assertEquals( true,              $vli->ipv6canping           );
        $this->assertEquals( true,              $vli->ipv4monitorrcbgp      );
        $this->assertEquals( true,              $vli->ipv6monitorrcbgp      );
        $this->assertEquals( true,              $vli->as112client           );
        $this->assertEquals( false,             $vli->busyhost              );
        $this->assertEquals( null,              $vli->notes                 );
        $this->assertEquals( false,             $vli->rsmorespecifics       );

        // check that we have 1 physical interface for the virtual interface
        $this->assertEquals( 1, $vi->physicalInterfaces()->count() );

        /** @var $pi PhysicalInterface */
        $pi = $vi->physicalInterfaces[0];

        // check the values of the Physical interface object
        $this->assertEquals( "GigabitEthernet4",    $pi->switchPort->name               );
        $this->assertEquals( "switch2",             $pi->switchPort->switcher->name     );
        $this->assertEquals( 4,                     $pi->status                         );
        $this->assertEquals( 1000,                  $pi->speed                          );
        $this->assertEquals( "full",                $pi->duplex                         );
        $this->assertEquals( null,                  $pi->notes                          );
        $this->assertEquals( true,                  $pi->autoneg                        );


        // Go on edit page
        $browser->visit( route( 'virtual-interface@edit', $vi->id ) )
            ->assertSee('Edit Virtual Interface');

        // Check the form values
        $browser->assertSelected('custid', '5' )
                ->assertNotChecked('trunk'          )
                ->assertNotChecked('lag_framing'    )
                ->assertNotChecked('fastlacp'       )
                ->click(        "#advanced-options" )
                ->assertInputValue('name', ''           )
                ->assertInputValue('description',   ''  )
                ->assertInputValue('channelgroup', ''  )
                ->assertInputValue('mtu',           ''  );

        // Edit the virtual Interface with new values
        $browser->select('custid', '2')
                ->check('trunk'             )
                ->check('lag_framing'       )
                ->waitFor( "#fastlacp"   )
                ->check('fastlacp'          )
                ->type('name',          'name-test'         )
                ->type('description',   'description-test'  )
                ->type('channelgroup', '666'               )
                ->type('mtu', '666' )
                ->press('Save Changes'  )
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee('Virtual Interface updated');

        // Check value in DB
        $vi->refresh();

        $this->assertEquals( 2,             $vi->custid             );
        $this->assertEquals( "name-test",   $vi->name               );
        $this->assertEquals( 666,           $vi->mtu                );
        $this->assertEquals( true,          (bool)$vi->trunk        );
        $this->assertEquals( 666,           $vi->channelgroup       );
        $this->assertEquals( true,          (bool)$vi->lag_framing  );
        $this->assertEquals( true,          (bool)$vi->fastlacp     );

        // Go on edit page
        $browser->visit( route( 'virtual-interface@edit', $vi->id ) )
            ->assertSee('Edit Virtual Interface');


        // Check the form with new values
        $browser->assertSelected('custid', '2' )
                ->assertChecked('trunk'         )
                ->assertChecked('fastlacp'      )
                ->assertChecked('lag_framing'   )
                ->assertInputValue('name',          'name-test'         )
                ->assertInputValue('description',   'description-test'  )
                ->assertInputValue('channelgroup', '666'               )
                ->assertInputValue('mtu',           '666'               );


        // Edit the virtual Interface, uncheck all checkboxes, change value of select
        $browser->select('custid', '3' )
                ->uncheck('fastlacp'    )
                ->uncheck('trunk'       )
                ->uncheck('lag_framing' )
                ->press('Save Changes' )
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee('Virtual Interface updated' );

        // Check value in DB
        $vi->refresh();

        $this->assertEquals( 3,     $vi->custid             );
        $this->assertEquals( false, (bool)$vi->trunk        );
        $this->assertEquals( false, (bool)$vi->lag_framing  );
        $this->assertEquals( false, (bool)$vi->fastlacp     );

        // Go on edit page
        $browser->visit( route( 'virtual-interface@edit', $vi->id ) )
            ->assertSee('Edit Virtual Interface' );

        // Check the form with new values
        $browser->assertSelected('custid', '3' )
                ->assertNotChecked('trunk'          )
                ->assertNotChecked('lag_framing'    )
                ->assertNotChecked('fastlacp'       );


        // Edit the virtual Interface, check all checkboxes
        $browser->check('trunk' )
                ->check('lag_framing'     )
                ->waitFor( "#fastlacp" )
                ->check('fastlacp'        )
                ->press('Save Changes'  )
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee('Virtual Interface updated.' );

        // Check value in DB
        $vi->refresh();

        $this->assertEquals( true, (bool)$vi->trunk         );
        $this->assertEquals( true, (bool)$vi->lag_framing   );
        $this->assertEquals( true, (bool)$vi->fastlacp      );

        // Go on edit page
        $browser->visit( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee('Edit Virtual Interface' );

        // Check the form with new values
        $browser->assertChecked('trunk'         )
                ->assertChecked('lag_framing'   )
                ->assertChecked('fastlacp'      );


        // Test for the issue : https://github.com/inex/IXP-Manager/issues/513
        $browser->visit( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee('Edit Virtual Interface')
                ->type(     "name" , '"test "')
                ->click( '#submit-form' )
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) );

        $browser->assertSourceHas( 'Virtual Interface updated.' );

        // Check value in DB
        $vi->refresh();

        $this->assertEquals( "test ",     $vi->name );

        $browser->visit( route( 'virtual-interface@edit', $vi->id ) )
                ->assertInputValue('name', '"test "' )
                ->press('Save Changes' )
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee('Virtual Interface updated.' );

        // Check value in DB
        $vi->refresh();

        $this->assertEquals( "test ",     $vi->name );

        return $vi;
    }

    /**
     * Test the Physical interface Add/edit/delete function
     *
     * @param Browser           $browser
     * @param VirtualInterface  $vi
     *
     * @throws
     */
    private function intTestPi(Browser $browser, VirtualInterface $vi ): void
    {
        $browser->visit( route( 'virtual-interface@edit', $vi->id ) );

        $browser->click( "#add-pi" )
            ->waitForLocation( route( 'physical-interface@create', $vi->id ) );

        // Add a new Physical interface
        $browser->select('switch',  '2' )
                ->waitUntilMissing( "Choose a switch port" )
                ->waitForText( "Choose a switch port"   )
                ->select( 'switchportid',    '29'    )
                ->select( 'status',         '1'     )
                ->select( 'speed',          '10'    )
                ->select( 'duplex',         'half'  )
                ->check( 'autoneg' )
                ->type( 'notes', '### note test' )
                ->press( "Create" )
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee( 'Physical Interface created.' );


        // check data in DB
        $this->assertGreaterThan( 1, $vi->physicalInterfaces()->count() );

        $vi->refresh();

        /** @var $pi PhysicalInterface */
        $this->assertInstanceOf( PhysicalInterface::class , $pi = $vi->physicalInterfaces->last() );

        $this->assertEquals( 2,      $pi->switchPort->switchid  );
        $this->assertEquals( 29,     $pi->switchportid          );
        $this->assertEquals( 1,      $pi->status    );
        $this->assertEquals( 10,     $pi->speed     );
        $this->assertEquals( 'half', $pi->duplex    );
        $this->assertEquals( true,   $pi->autoneg   );
        $this->assertEquals( '### note test', $pi->notes );


        $browser->click( "#edit-pi-" . $pi->id )
            ->waitForLocation( route( 'physical-interface@edit-from-virtual-interface', [ 'pi' => $pi->id, 'vi' => $vi->id ] ) )
            ->assertSee( "Physical Interfaces / Edit" );


        // Check the form values
        $browser->assertSelected('switch', '2')
                ->waitUntilMissing( "Choose a switch port" )
                ->waitForText( "Choose a switch port" )
                ->assertSelected('switchportid', '29'    )
                ->assertSelected('status',      '1'     )
                ->assertSelected('speed',       '10'    )
                ->assertSelected('duplex',      'half'  )
                ->assertChecked('autoneg' )
                ->assertInputValue( 'notes' , '### note test' );

        // edit the Physical interface
        $browser->select('switch',  '1')
                ->waitUntilMissing( "Choose a switch port" )
                ->waitForText( "Choose a switch port" )
                ->select( 'switchportid',    '2'     )
                ->select( 'status',         '2'     )
                ->select( 'speed',          '100'   )
                ->select( 'duplex',         'full'  )
                ->uncheck( 'autoneg' )
                ->type( 'notes', '### note test test' )
                ->press( "Save Changes" )
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee( 'Physical Interface updated.' );

        $pi->refresh();

        // check data in DB
        $this->assertEquals( 1,      $pi->switchPort->switchid  );
        $this->assertEquals( 2,      $pi->switchportid          );
        $this->assertEquals( 2,      $pi->status                );
        $this->assertEquals( 100,    $pi->speed                 );
        $this->assertEquals( "full", $pi->duplex                );
        $this->assertEquals( false,  $pi->autoneg               );
        $this->assertEquals( '### note test test', $pi->notes   );


        $browser->click( "#edit-pi-" . $pi->id )
            ->waitForLocation( route( 'physical-interface@edit-from-virtual-interface', [ 'pi' => $pi->id, 'vi' => $vi->id ] ) );


        $browser->assertSee( "Physical Interfaces / Edit" );

        // Check the form values
        $browser->assertSelected('switch', '1')
                ->waitUntilMissing( "Choose a switch port" )
                ->waitForText( "Choose a switch port" )
                ->assertSelected('switchportid', '2'     )
                ->assertSelected('status',      '2'     )
                ->assertSelected('speed',       '100'   )
                ->assertSelected('duplex',      'full'  )
                ->assertNotChecked('autoneg' )
                ->assertInputValue( 'notes' , '### note test test' );


        // check all checkboxes
        $browser->check( 'autoneg' )
                ->press( "Save Changes" )
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee( 'Physical Interface updated.' );

        $pi->refresh();

        $this->assertEquals( true,  (bool)$pi->autoneg );

        $browser->click( "#edit-pi-" . $pi->id )
            ->waitForLocation( route( 'physical-interface@edit-from-virtual-interface', [ 'pi' => $pi->id, 'vi' => $vi->id ] ) );

        $browser->assertSee( "Physical Interfaces / Edit" );

        // Check the form values
        $browser->assertChecked('autoneg' );

        $browser->click( "#cancel-btn" )
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee( "Edit Virtual Interface" );

        // Delete physical interface
        $browser->press("#btn-delete-pi-" . $pi->id )
                ->waitForText( 'Do you really want to delete this Physical Interface?' )
                ->press('Delete')
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee( 'Physical Interface deleted.' );
    }



    /**
     * Test the Vlan interface Add/edit/delete function
     *
     * @param Browser               $browser
     * @param VirtualInterface      $vi
     *
     * @throws
     */
    private function intTestVli( Browser $browser, VirtualInterface $vi ): void
    {
        $browser->visit( route( 'virtual-interface@edit', $vi->id ) );

        $browser->click( "#add-vli" );

        $browser->waitForLocation( route( 'vlan-interface@create', $vi->id ) );

        // Add a new Vlan interface
        $browser->select('vlanid',  '2' )
                ->check( "mcastenabled"     )
                ->check( "busyhost"         )
                ->check( 'ipv6enabled'     )
                ->waitFor( "#ipv6-area"  )
                ->check( 'ipv4enabled'     )
                ->waitFor( "#ipv4-area"  )
                ->check( "rsclient"         )
                ->check( 'irrdbfilter'      )
                ->check( 'rsmorespecifics'  )
                ->select( 'ipv4address', "10.2.0.1"        )
                ->select( 'ipv6address', '2001:db8:2::1'   )
                ->type( 'ipv4hostname', 'v4.example.com'   )
                ->type( 'ipv6hostname', 'v6.example.com'   )
                ->type( "ipv4maxbgpprefix", '250' )
                ->type( "ipv6maxbgpprefix", '150' )
                ->type( 'ipv4bgpmd5secret', 'soopersecret' )
                ->type( 'ipv6bgpmd5secret', 'soopersecret' )
                ->check( 'ipv4canping' )
                ->check( 'ipv6canping' )
                ->check( 'ipv4monitorrcbgp' )
                ->check( 'ipv6monitorrcbgp' )
                ->press('Create')
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee('VLAN Interface created.');

        // check data in DB
        $this->assertGreaterThan( 1, $vi->vlanInterfaces()->count() );

        $vi->refresh();

        /** @var $vli VlanInterface */
        $this->assertInstanceOf( VlanInterface::class , $vli = $vi->vlanInterfaces->last() );

        $this->assertEquals( "10.2.0.1",        $vli->ipv4address->address  );
        $this->assertEquals( "2001:db8:2::1",   $vli->ipv6address->address  );
        $this->assertEquals( 2,                 $vli->vlanid                );
        $this->assertEquals( true,              $vli->ipv4enabled           );
        $this->assertEquals( true,              $vli->ipv6enabled           );
        $this->assertEquals( "v4.example.com",  $vli->ipv4hostname          );
        $this->assertEquals( "v6.example.com",  $vli->ipv6hostname          );
        $this->assertEquals( true,              $vli->mcastenabled          );
        $this->assertEquals( true,              $vli->irrdbfilter           );
        $this->assertEquals( "soopersecret",    $vli->ipv4bgpmd5secret      );
        $this->assertEquals( "soopersecret",    $vli->ipv6bgpmd5secret      );
        $this->assertEquals( "250",             $vli->ipv4maxbgpprefix      );
        $this->assertEquals( "150",             $vli->ipv6maxbgpprefix      );
        $this->assertEquals( true,              $vli->rsclient              );
        $this->assertEquals( true,              $vli->ipv4canping           );
        $this->assertEquals( true,              $vli->ipv6canping           );
        $this->assertEquals( true,              $vli->ipv4monitorrcbgp      );
        $this->assertEquals( true,              $vli->ipv6monitorrcbgp      );
        $this->assertEquals( true,              $vli->busyhost              );
        $this->assertEquals( null,              $vli->notes                 );
        $this->assertEquals( true,              $vli->rsmorespecifics       );


        // Edit the VLAN Interface
        $browser->click( "#edit-vli-" . $vli->id )
            ->waitForLocation( route( 'vlan-interface@edit-from-virtual-interface', [ 'vli' => $vli->id,  'vi' => $vi->id ] ) )
            ->assertSee( "Edit VLAN Interface" );


        // Check the form values
        $browser->assertSelected('vlanid', '2' )
                ->assertChecked('mcastenabled'  )
                ->assertChecked('busyhost'      )
                ->assertChecked('ipv6enabled'  )
                ->assertChecked('ipv4enabled'  )
                ->assertChecked('rsclient'          )
                ->assertChecked('irrdbfilter'       )
                ->assertChecked('rsmorespecifics'   )
                ->assertSelected('ipv4address',            '10.2.0.1'         )
                ->assertSelected('ipv6address',            '2001:db8:2::1'    )
                ->assertInputValue( 'ipv4hostname',        'v4.example.com'    )
                ->assertInputValue( 'ipv6hostname',        'v6.example.com'    )
                ->assertInputValue( 'ipv4bgpmd5secret',  'soopersecret'      )
                ->assertInputValue( 'ipv6bgpmd5secret',  'soopersecret'      )
                ->assertInputValue('ipv4maxbgpprefix', '250' )
                ->assertInputValue('ipv6maxbgpprefix', '150' )
                ->assertChecked( 'ipv4canping' )
                ->assertChecked( 'ipv6canping' )
                ->assertChecked( 'ipv4monitorrcbgp' )
                ->assertChecked( 'ipv6monitorrcbgp' );


        // Change value of the vlan interface
        // Check the form values
        $browser->select('vlanid', '1' )
                ->uncheck( "mcastenabled"   )
                ->uncheck( "busyhost"       )
                ->uncheck( "rsclient"    )
                ->uncheck( 'irrdbfilter' )
                ->uncheck( 'rsmorespecifics' )
                ->select( 'ipv4address',       "10.1.0.1"          )
                ->select( 'ipv6address',       '2001:db8:1::1'     )
                ->type( 'ipv4hostname',        'v4-2.example.com'  )
                ->type( 'ipv6hostname',        'v6-2.example.com'  )
                ->type( 'ipv4bgpmd5secret',  'soopersecrets'     )
                ->type( 'ipv6bgpmd5secret',  'soopersecrets'     )
                ->type( "ipv4maxbgpprefix", '300' )
                ->type( "ipv6maxbgpprefix", '180' )
                ->uncheck( 'ipv4canping' )
                ->uncheck( 'ipv6canping' )
                ->uncheck( 'ipv4monitorrcbgp' )
                ->uncheck( 'ipv6monitorrcbgp' )
                ->press('Save Changes')
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee('VLAN Interface updated');

        $vli->refresh();

        $this->assertEquals( "10.1.0.1",            $vli->ipv4address->address  );
        $this->assertEquals( "2001:db8:1::1",       $vli->ipv6address->address  );
        $this->assertEquals( 1,                     $vli->vlanid                );
        $this->assertEquals( true,                  $vli->ipv4enabled           );
        $this->assertEquals( true,                  $vli->ipv6enabled           );
        $this->assertEquals( "v4-2.example.com",    $vli->ipv4hostname          );
        $this->assertEquals( "v6-2.example.com",    $vli->ipv6hostname          );
        $this->assertEquals( 300,                   $vli->ipv4maxbgpprefix      );
        $this->assertEquals( 180,                   $vli->ipv6maxbgpprefix      );
        $this->assertEquals( false,                 $vli->mcastenabled          );
        $this->assertEquals( false,                 $vli->irrdbfilter           );
        $this->assertEquals( "soopersecrets",       $vli->ipv4bgpmd5secret      );
        $this->assertEquals( "soopersecrets",       $vli->ipv6bgpmd5secret      );
        $this->assertEquals( false,                 $vli->rsclient              );
        $this->assertEquals( false,                 $vli->ipv4canping           );
        $this->assertEquals( false,                 $vli->ipv6canping           );
        $this->assertEquals( false,                 $vli->ipv4monitorrcbgp      );
        $this->assertEquals( false,                 $vli->ipv6monitorrcbgp      );
        $this->assertEquals( false,                 $vli->busyhost              );
        $this->assertEquals( null,                  $vli->notes                 );
        $this->assertEquals( false,                 $vli->rsmorespecifics       );

        // Edit the VLAN Interface
        $browser->click( "#edit-vli-" . $vli->id )
                ->waitForLocation( route( 'vlan-interface@edit-from-virtual-interface', [ 'vli' => $vli->id,  'vi' => $vi->id ] ) )
                ->assertSee( "Edit VLAN Interface" );

        // Check the form values
        $browser->assertSelected('vlanid', '1')
                ->assertNotChecked('mcastenabled')
                ->assertNotChecked('busyhost'       )
                ->assertChecked('ipv6enabled'      )
                ->assertChecked('ipv4enabled'      )
                ->assertNotChecked('rsclient'       )
                ->assertNotChecked('irrdbfilter'    )
                ->assertNotChecked('rsmorespecifics')
                ->assertSelected('ipv4address', '10.1.0.1'     )
                ->assertSelected('ipv6address', '2001:db8:1::1')
                ->assertInputValue( 'ipv4hostname', 'v4-2.example.com' )
                ->assertInputValue( 'ipv6hostname', 'v6-2.example.com' )
                ->assertInputValue( 'ipv4bgpmd5secret', 'soopersecrets' )
                ->assertInputValue( 'ipv6bgpmd5secret', 'soopersecrets' )
                ->assertInputValue('ipv4maxbgpprefix', '300')
                ->assertInputValue('ipv6maxbgpprefix', '180')
                ->assertNotChecked( 'ipv4canping' )
                ->assertNotChecked( 'ipv6canping' )
                ->assertNotChecked( 'ipv4monitorrcbgp' )
                ->assertNotChecked( 'ipv6monitorrcbgp' );


        // Check all the checkboxes
        $browser->check( "mcastenabled" )
                ->check( "busyhost"     )
                ->check( "rsclient"     )
                ->check( 'irrdbfilter'  )
                ->waitFor( "#div-rsmorespecifics" )
                ->check('rsmorespecifics')
                ->check( 'ipv4canping' )
                ->check( 'ipv6canping' )
                ->check( 'ipv4monitorrcbgp' )
                ->check( 'ipv6monitorrcbgp' )
                ->press('Save Changes')
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee('VLAN Interface updated.');

        $vli->refresh();

        $this->assertEquals( true, $vli->mcastenabled       );
        $this->assertEquals( true, $vli->irrdbfilter        );
        $this->assertEquals( true, $vli->rsclient           );
        $this->assertEquals( true, $vli->ipv4canping        );
        $this->assertEquals( true, $vli->ipv6canping        );
        $this->assertEquals( true, $vli->ipv4monitorrcbgp   );
        $this->assertEquals( true, $vli->ipv6monitorrcbgp   );
        $this->assertEquals( true, $vli->busyhost           );
        $this->assertEquals( true, $vli->rsmorespecifics    );

        // Edit the VLAN Interface
        $browser->click( "#edit-vli-" . $vli->id )
            ->waitForLocation( route( 'vlan-interface@edit-from-virtual-interface', [ 'vli' => $vli->id,  'vi' => $vi->id ] ) )
            ->assertSee( "Edit VLAN Interface" );

        // Check the form values
        $browser->assertChecked('mcastenabled'          )
                ->assertChecked('busyhost'              )
                ->assertChecked('rsclient'              )
                ->assertChecked('irrdbfilter'           )
                ->assertChecked( 'ipv4canping'          )
                ->assertChecked( 'ipv6canping'          )
                ->assertChecked( 'ipv4monitorrcbgp'     )
                ->assertChecked( 'ipv6monitorrcbgp'     );

        $browser->click( "#cancel-btn" )
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee( "Edit Virtual Interface" );



        // Edit the VLAN Interface
        $browser->click( "#edit-vli-" . $vli->id )
            ->waitForLocation( route( 'vlan-interface@edit-from-virtual-interface', [ 'vli' => $vli->id,  'vi' => $vi->id ] ) )
            ->assertSee( "Edit VLAN Interface" );

        // Check max prefixes
        $browser->assertInputValue('ipv4maxbgpprefix', '300')
            ->assertInputValue('ipv6maxbgpprefix', '180');

        // Check all the checkboxes
        $browser->type( "ipv4maxbgpprefix", '' )
            ->type( "ipv6maxbgpprefix", '0' )
            ->press('Save Changes')
            ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
            ->assertSee('VLAN Interface updated.');

        $vli->refresh();

        $this->assertNull( $vli->ipv4maxbgpprefix );
        $this->assertEquals( 0, $vli->ipv6maxbgpprefix );

        //reset
        $vli->ipv4maxbgpprefix = 300;
        $vli->ipv6maxbgpprefix = 180;
        $vli->save();


        // test the duplication functionality
        $browser->click( "#btn-duplicate-vli-" . $vli->id )
                ->waitForText( 'Duplicate the VLAN Interface' )
                ->select( "#duplicateTo" , '2' )
                ->press('Duplicate')
                ->waitForLocation( route( 'vlan-interface@duplicate-form', [ 'vli' => $vli->id, 'v' => 2 ] ) )
                ->assertSee( 'This form allows you to duplicate the selected' );

        // check that the form match with the Vlan interface information
        $browser->assertSelected('vlanid', '2')
                ->assertChecked('mcastenabled'  )
                ->assertChecked('busyhost'      )
                ->assertChecked('ipv6enabled'  )
                ->assertChecked('ipv4enabled'  )
                ->assertChecked('rsclient'          )
                ->assertChecked('irrdbfilter'       )
                ->assertChecked('rsmorespecifics'   )
                ->assertSelected('ipv4address', '10.1.0.1'         )
                ->assertSelected('ipv6address', '2001:db8:1::1'    )
                ->assertInputValue( 'ipv4hostname', 'v4-2.example.com' )
                ->assertInputValue( 'ipv6hostname', 'v6-2.example.com' )
                ->assertInputValue( 'ipv4bgpmd5secret', 'soopersecrets' )
                ->assertInputValue( 'ipv6bgpmd5secret', 'soopersecrets' )
                ->assertInputValue('ipv4maxbgpprefix', '300')
                ->assertInputValue('ipv6maxbgpprefix', '180')
                ->assertChecked( 'ipv4canping' )
                ->assertChecked( 'ipv6canping' )
                ->assertChecked( 'ipv4monitorrcbgp' )
                ->assertChecked( 'ipv6monitorrcbgp' )
                ->press( "Duplicate" )
                ->waitForText( "VLAN Interface duplicated" );

        $vi->refresh();

        /** @var VlanInterface $vliDuplicated */
        $vliDuplicated = $vi->vlanInterfaces->last();

        // check if the value of the duplicated Vlan interface match
        $this->assertEquals( "10.1.0.1",            $vliDuplicated->ipv4address->address    );
        $this->assertEquals( "2001:db8:1::1",       $vliDuplicated->ipv6address->address    );
        $this->assertEquals( "2",                   $vliDuplicated->vlanid                  );
        $this->assertEquals( true,                  $vliDuplicated->ipv4enabled             );
        $this->assertEquals( true,                  $vliDuplicated->ipv6enabled             );
        $this->assertEquals( "v4-2.example.com",    $vliDuplicated->ipv4hostname            );
        $this->assertEquals( "v6-2.example.com",    $vliDuplicated->ipv6hostname            );
        $this->assertEquals( "300",                 $vliDuplicated->ipv4maxbgpprefix        );
        $this->assertEquals( "180",                 $vliDuplicated->ipv6maxbgpprefix        );
        $this->assertEquals( true,                  $vliDuplicated->mcastenabled            );
        $this->assertEquals( true,                  $vliDuplicated->irrdbfilter             );
        $this->assertEquals( "soopersecrets",       $vliDuplicated->ipv4bgpmd5secret        );
        $this->assertEquals( "soopersecrets",       $vliDuplicated->ipv6bgpmd5secret        );
        $this->assertEquals( true,                  $vliDuplicated->rsclient                );
        $this->assertEquals( true,                  $vliDuplicated->ipv4canping             );
        $this->assertEquals( true,                  $vliDuplicated->ipv6canping             );
        $this->assertEquals( true,                  $vliDuplicated->ipv4monitorrcbgp        );
        $this->assertEquals( true,                  $vliDuplicated->ipv4monitorrcbgp        );
        $this->assertEquals( true,                  $vliDuplicated->busyhost                );
        $this->assertEquals( null,                  $vliDuplicated->notes                   );

        // Delete Vlan interface
        $browser->press("#btn-delete-vli-" . $vli->id )
                ->waitForText( 'Do you really want to delete this VLAN Interface?' )
                ->press('Delete')
                ->waitForLocation( route( 'virtual-interface@edit', $vi->id ) )
                ->assertSee( 'VLAN Interface deleted' );

    }
}