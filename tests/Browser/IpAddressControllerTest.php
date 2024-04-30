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

use Illuminate\Support\Facades\DB;
use IXP\Models\IPv4Address;
use IXP\Models\IPv6Address;
use IXP\Models\Vlan;

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Test IP Address Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IpAddressControllerTest extends DuskTestCase
{
    public string $vlan = "Test VLAN";
    public array $category = [
        "ipv4" => [
            "protocol" => "4",
            "net1"     => "192.0.2.24/29",
            "net2"     => "192.0.2.16/28",
            "ip1"     => "192.0.2.17",
            "ip2"     => "10.1.0.6",
            "del1"     => "192.0.2.16/29",
            "del2"     => "192.0.2.0/24",
            "del3"     => "10.1.0.0/29",
        ],
        "ipv6" => [
            "protocol" => "6",
            "net1"     => "2001:db8:23::18/125",
            "net2"     => "2001:db8:23::10/124",
            "ip1"     => "2001:db8:23::12",
            "ip2"     => "2001:db8:1::8",
            "del1"     => "2001:db8:23::10/125",
            "del2"     => "2001:db8:23::0/120",
            "del3"     => "2001:db8:1::0/125",
        ],
    ];

    /**
     * @throws
     */
    public function tearDown(): void
    {
        $vlan = Vlan::whereName( $this->vlan )->first();
        IPv4Address::where( "vlanid", $vlan->id )->delete();
        IPv6Address::where( "vlanid", $vlan->id )->delete();
        Vlan::whereName( $this->vlan )->delete();
        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     *
     * @throws
     */
    public function testIpAddress(): void
    {
        $this->browse( function( Browser $browser ) {
            // I. prerequisites
            $browser->resize( 1600, 1200 )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->assertPathIs( '/admin' );

            $browser->visit( '/vlan/create' )
                ->type( 'name', $this->vlan )
                ->type( 'number', '10' )
                ->select( 'infrastructureid', 1 )
                ->type( 'config_name', 'test-vlan' )
                ->type( 'notes', 'test notes' )
                ->press( 'Create' )
                ->assertPathIs( '/vlan/list' )
                ->assertSee( "VLAN created" )
                ->assertSee( $this->vlan );

            $vlan = Vlan::whereName( $this->vlan )->first();
            $vlanId = $vlan->id;

            // II.tests for IP addresses
            foreach( $this->category as $cat ) {
                $prot = $cat[ "protocol" ];

                // 1. open Test VLAN list, check is empty
                $browser->visit( "/ip-address/list/$prot" )
                    ->assertSee( "IPv$prot Addresses" )
                    ->select( 'vlan', $vlanId )
                    ->assertSee( "There are no IPv$prot addresses in this VLAN." );

                // 2. create some ip addresses in the test VLAN
                $browser->visit( "/ip-address/create/$prot?vlan=$vlanId" )
                    ->assertSee( "IP Addresses / Create IPv$prot Address" )
                    ->assertSelected( 'vlan', $vlanId )
                    ->type( "network", $cat[ "net1" ] )
                    ->check( 'skip' )
                    ->press( "Add Addresses" )
                    ->assertPathIs( "/ip-address/list/$prot/$vlanId" )
                    ->assertSee( "8 new IP addresses added to ".$vlan->name.". There were 0 preexisting address(es)." )
                    ->assertSee( "Showing 1 to 8 of 8 entries" );

                // 3. add again
                $browser->visit( "/ip-address/create/$prot?vlan=$vlanId" )
                    ->type( "network", $cat[ "net1" ] )
                    ->check( 'skip' )
                    ->press( "Add Addresses" )
                    ->assertPathIs( "/ip-address/create/$prot" )
                    ->assertQueryStringHas("vlan",$vlanId)
                    ->assertSee( "No addresses were added. 8 already exist in the database.");

                // 3. add again w/o skip
                $browser->uncheck( 'skip' )
                    ->press( "Add Addresses" )
                    ->assertPathIs( "/ip-address/create/$prot" )
                    ->assertQueryStringHas("vlan",$vlanId)
                    ->assertSee( "No addresses were added as the following addresses already exist in the database:" );

                // 4. add more ip addresses in the test VLAN
                $browser->visit( "/ip-address/create/$prot?vlan=$vlanId" )
                    ->type( "network", $cat[ "net2" ] )
                    ->check( 'skip' )
                    ->press( "Add Addresses" )
                    ->assertPathIs( "/ip-address/list/$prot/$vlanId" )
                    ->assertSee( "8 new IP addresses added to ".$vlan->name.". There were 8 preexisting address(es)." );

                $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

                $browser->assertSee( "Showing 1 to 16 of 16 entries" );


                // 5. select a row to delete
                $pickedIp = DB::table( 'ipv'.$prot.'address' )->select('id')
                    ->where('address', $cat[ "ip1" ] )
                    ->where('vlanid', $vlanId )
                    ->first();

                $deleteUrl = route('ip-address@delete',$pickedIp->id);
                $browser->click('a.delete-ip[href="'.$deleteUrl.'"]')
                    ->waitForText('Do you really want to delete this IP address?')
                    ->press( 'Delete' )
                    ->assertPathIs( "/ip-address/list/$prot/$vlanId" )
                    ->assertSee('The IP has been successfully deleted.')
                    ->assertDontSee($cat["ip1"]);

                // 6. ip addresses mass delete with missing item
                $browser->visit( "/ip-address/delete-by-network/vlan/$vlanId" )
                    ->assertSee('VLANs / Delete Free IP Addresses')
                    ->type('network', $cat[ "del1" ] )
                    ->press( 'Find Free Addresses' )
                    ->waitForText('List of Free IP Addresses To Be Deleted')
                    ->assertDontSee($cat["ip1"]);

                $availableIpAddresses = $browser->elements('#table-ip tbody tr td:not(:empty)');
                $this->assertTrue(count($availableIpAddresses) == 7);

                $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );
                $browser->click( 'a#delete' )
                    ->waitForText('Do you really want to delete this IP address?')
                    ->press( 'Delete' )
                    ->assertPathIs( "/ip-address/list/$prot/$vlanId" )
                    ->assertSee('IP Addresses deleted.');

                $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

                $browser->assertSee( "Showing 1 to 8 of 8 entries" );

                // 7. ip addresses mass delete
                $browser->visit( "/ip-address/delete-by-network/vlan/$vlanId" )
                    ->assertSee('VLANs / Delete Free IP Addresses')
                    ->type('network', $cat[ "del2" ] )
                    ->press( 'Find Free Addresses' )
                    ->waitForText('List of Free IP Addresses To Be Deleted');

                $availableIpAddresses = $browser->elements('#table-ip tbody tr td:not(:empty)');
                $this->assertTrue(count($availableIpAddresses) == 8);

                $browser->click( 'a#delete' )
                    ->waitForText('Do you really want to delete this IP address?')
                    ->press( 'Delete' )
                    ->assertPathIs( "/ip-address/list/$prot/$vlanId" )
                    ->assertSee('IP Addresses deleted.')
                    ->assertSee( "There are no IPv$prot addresses in this VLAN." );

                // 8. check other vlan active address for deletion
                $browser->visit( "/ip-address/list/$prot" )
                    ->assertSee( "IPv$prot Addresses" )
                    ->select( 'vlan', 'Peering LAN1' );

                $pickedActiveIp = DB::table( 'ipv'.$prot.'address' )->select('id')
                    ->where('address', $cat[ "ip2" ] )
                    ->where('vlanid', 1 )
                    ->first();
                $deleteActiveUrl = route('ip-address@delete',$pickedActiveIp->id);
                $availableDeleteButton = $browser->elements('a.delete-ip[href="'.$deleteActiveUrl.'"]');

                $this->assertTrue(count($availableDeleteButton) === 0);

                // 9. check active ip address missing on mass delete
                $browser->visit( "/ip-address/delete-by-network/vlan/1" )
                    ->assertSee('VLANs / Delete Free IP Addresses')
                    ->type('network', $cat[ "del3" ] )
                    ->press( 'Find Free Addresses' )
                    ->waitForText('List of Free IP Addresses To Be Deleted')
                    ->assertDontSee($cat["ip2"]);
            }

            //III. post removal of VLAN by teardown
        } );
    }
}