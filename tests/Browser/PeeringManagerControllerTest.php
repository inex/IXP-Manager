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
    Aggregators\CustomerAggregator,
    Customer,
    PeeringManager,
    User,
    Vlan
};

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;



/**
 * Test peering manager Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringManagerControllerTest extends DuskTestCase
{

    public function tearDown(): void
    {
        /** @var Customer $cust */
        $cust = User::whereUsername( 'hecustadmin' )->get()->first()->customer;

        $peers = CustomerAggregator::getPeeringManagerArrayByType(
            $cust,
            Vlan::peeringManager()->orderBy( 'number' )->get(),
            [ 4, 6 ]
        );

        foreach( $peers[ "potential" ] as  $as => $p ){
            if($p){
                $c = $peers[ "custs" ][ $as ];
                break;
            }
        }

        if( $p = PeeringManager::where( 'custid', $cust->id )->where( 'peerid', $c[ "id" ] )->get()->first() ) {
            $p->delete();
        }

        parent::tearDown();
    }

    /**
     * Test the whole Interfaces functionalities (virtuel, physical, vlan)
     *
     * @return void
     *
     * @throws
     */
    public function testPeeringManager(): void
    {
        $this->browse( function ( Browser $browser ) {

            $browser->resize(1600, 1400 )
                    ->visit('/login' )
                    ->type('username', 'hecustadmin' )
                    ->type('password', 'travisci' )
                    ->press( '#login-btn' )
                    ->assertPathIs('/dashboard' );


            $browser->press( "#peering-manager-a" )
                ->waitForText( 'Potential Peers' );

            $user = User::whereUsername( 'hecustadmin' )->get()->first();

            /** @var Customer $cust */
            $cust = $user->customer;

            $peers = CustomerAggregator::getPeeringManagerArrayByType(
                $cust,
                Vlan::peeringManager()->orderBy( 'number' )->get(),
                [ 4, 6 ]
            );

            foreach( $peers[ "potential" ] as  $as => $p ){
                if( $p ) {
                    $c = $peers[ "custs" ][ $as ];
                    break;
                }
            }

            // Check data in DB
            $this->assertEquals( null , PeeringManager::where( 'custid', $cust->id )->where( 'peerid', $c[ "id" ] )->get()->first() );


            $browser->click( "#peering-notes-icon-" . $c[ "id" ] )
                ->whenAvailable( '#modal-peering-request', function ( $modal ) use ( $c ) {
                    $modal->waitForText( "Peering Notes for " . $c[ "name" ] )
                        ->type( '#peering-manager-notes', 'note'  )
                        ->click('#modal-peering-notes-save' );
                    });

            $browser->waitForText( "Peering notes updated for " . $c[ "name" ] )
                ->press( "Close" );

            $browser->waitUntilMissing( ".modal-backdrop" );

            // Check value in DB
            /** @var $pm PeeringManager */
            $this->assertInstanceOf( PeeringManager::class , $pm = PeeringManager::where( 'custid', $cust->id )->where( 'peerid', $c[ "id" ] )->get()->first() );

//            $this->assertEquals( "### " . date( "Y-m-d" ) . " - hecustadmin
//
//
//note", $pm->getNotes() );

            /** Test peering request */
            $browser->click( "#peering-request-" . $c[ "id" ] )
                ->waitForText( "Send Peering Request by Email" )
                ->click('#modal-peering-request-marksent' )
                ->pause( 500 )
                ->waitForText( "Peering request marked as sent in your Peering Manager." )
                ->press( "Close" );

            $browser->waitUntilMissing( ".modal-backdrop" );

            // Check value in DB
            $pm->refresh();

            $this->assertEquals( 1 , $pm->emails_sent );

//            $this->sendEmail( $browser, $pm, $c, $user, true, 1);
//            $this->sendEmail( $browser, $pm, $c, $user, false, 2);

            // Test Mark Peering

            $this->markPeering( $browser, $pm, $c, "peered");
            $this->markPeering( $browser, $pm, $c, "rejected");
        });
    }

    /**
     * Test the peering manager request send to me function or send to the customer
     *
     * @param Browser               $browser
     * @param PeeringManager        $pm
     * @param array                 $c
     * @param User                  $user
     * @param bool                  $sentToMe
     * @param int                   $nbSent
     *
     * @return void
     *
     * @throws
     */
    public function sendEmail( Browser $browser, PeeringManager $pm, array $c, User $user, bool $sentToMe, int $nbSent ): void
    {
        // Test Send email to me
        $browser->click( "#peering-request-" . $c[ "id" ] )
            ->waitForText( "Are you sure you want to send a peering request to this member? You already sent one today." )
            ->press('OK' )
            ->waitForText( "Send Peering Request by Email" )
            ->click( $sentToMe ? '#modal-peering-request-sendtome' : '#modal-peering-request-send' )
            ->waitForText( "Success" )
            ->assertSee( $sentToMe ? "Peering request sample sent to your own email address (" . $user->getEmail() . ")." : "Peering request sent to" )
            ->press( "Close" )
            ->waitUntilMissing( ".modal-backdrop" );

        // Check value in DB
        $pm->refresh();

        $this->assertEquals( $nbSent , $pm->emails_sent );
    }

    /**
     * @param Browser           $browser
     * @param PeeringManager    $pm
     * @param array             $c
     * @param string            $status
     *
     * @throws
     */
    public function markPeering( Browser $browser, PeeringManager $pm , array $c, string $status ): void
    {
        $this->assertEquals( false, $status === "peered" ? $pm->peered : $pm->rejected );

        $browser->press( "#dropdown-mark-peering-" . $c[ "id" ] )
                ->press( $status === "peered" ? "#mark-peered-". $c[ "id" ] : "#mark-rejected-". $c[ "id" ] )
                ->assertPathIs( '/peering-manager' )
                ->assertSee( $status === "peered" ? 'Peered flag set for ' . $c[ "name" ] : 'Ignored / rejected flag set for ' . $c[ "name" ] );

        // Check value in DB
        $pm->refresh();
        $this->assertEquals( true, $status === "peered" ? $pm->peered : $pm->rejected );

        $browser->press( $status === "peered" ? "#peering-peers-li" : "#peering-rejected-li" )
                ->assertSee( $c[ "name" ] )
                ->waitFor(  "#dropdown-mark-peering-" . $c[ "id" ] )
                ->press(    "#dropdown-mark-peering-" . $c[ "id" ] )
                ->press(    $status === "peered" ? "#mark-peered-". $c[ "id" ] : "#mark-rejected-". $c[ "id" ] )
                ->assertPathIs( '/peering-manager' )
                ->assertSee( $status == "peered" ? 'Peered flag cleared for ' . $c[ "name" ] : 'Ignored / rejected flag cleared for ' . $c[ "name" ] );

        // Check value in DB
        $pm->refresh();

        $this->assertEquals( false, $status === "peered" ? $pm->peered : $pm->rejected );
    }
}