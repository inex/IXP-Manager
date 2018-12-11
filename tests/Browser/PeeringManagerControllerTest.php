<?php

namespace Tests\Browser;

use Auth,D2EM;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

use Entities\{
    Customer            as CustomerEntity,
    PeeringManager      as PeeringManagerEntity,
    User                as UserEntity,
    Vlan                as VlanEntity
};

class PeeringManagerControllerTest extends DuskTestCase
{
    /**
     * Test the whole Interfaces functionalities (virtuel, physical, vlan)
     *
     * @return void
     *
     * @throws
     */
    public function testPeeringManager()
    {
        $this->browse( function ( Browser $browser ) {

            $browser->resize(1600, 1200 )
                ->visit('/auth/login' )
                ->type('username', 'hecustadmin' )
                ->type('password', 'travisci' )
                ->press('submit' )
                ->assertPathIs('/dashboard/index' );


            $browser->press( "#peering-manager-a" )
                ->waitForText( 'Potential Peers' );

            /** @var UserEntity $user */
            $user = D2EM::getRepository( UserEntity::class )->findOneBy( [ "username" => "hecustadmin" ] );

            /** @var CustomerEntity $cust */
            $cust = $user->getCustomer();

            $peers = D2EM::getRepository( CustomerEntity::class )->getPeeringManagerArrayByType( $cust , D2EM::getRepository( VlanEntity::class )->getPeeringManagerVLANs(), [ 4, 6 ] );

            foreach( $peers[ "potential" ] as  $as => $p ){
                if($p){
                    $c = $peers[ "custs" ][ $as ];
                    break;
                }

            }

            // Check data in DB
            /** @var $pm PeeringManagerEntity */
            $this->assertEquals( null , D2EM::getRepository( PeeringManagerEntity::class )->findOneBy( [ 'Customer' => $cust, 'Peer' => $c[ "id" ] ] ) );


            $browser->click( "#peering-notes-icon-" . $c[ "id" ] )
                ->waitForText( "Peering Notes for " . $c[ "name" ] )
                ->type('#peering-manager-notes', 'test note' );

            $browser->click('#modal-peering-notes-save' )
                ->waitForText( "Peering notes updated for Imagine" )
                ->click( ".bootbox-close-button" )
                ->waitUntilMissing( ".modal-backdrop" );

            // we need a pause here as it can sometimes break CI tests
            sleep(5);

            // Check value in DB
            $this->assertInstanceOf( PeeringManagerEntity::class , $pm = D2EM::getRepository( PeeringManagerEntity::class )->findOneBy( [ 'Customer' => $cust, 'Peer' => $c[ "id" ] ] ) );
            $this->assertEquals( "test note", $pm->getNotes() );


            /** Test peering request */

            // Test Mark as sent
            $browser->click( "#peering-request-" . $c[ "id" ] )
                ->waitForText( "Send Peering Request by Email" )
                ->click('#modal-peering-request-marksent' )
                ->waitForText( "Peering request marked as sent in your Peering Manager." )
                ->click( ".bootbox-close-button" )
                ->waitUntilMissing( ".modal-backdrop" );

            // Check value in DB
            D2EM::refresh( $pm );

            $this->assertEquals( 1 ,$pm->getEmailsSent() );

            $this->sendEmail( $browser, $pm, $c, $user, true, 1);
            $this->sendEmail( $browser, $pm, $c, $user, false, 2);

            // Test Mark Peering

            $this->markPeering( $browser, $pm, $c, "peered");
            $this->markPeering( $browser, $pm, $c, "rejected");
        });
    }

    /**
     * Test the peering manager request send to me function or send to the customer
     *
     * @param Browser                   $browser
     * @param PeeringManagerEntity      $pm
     * @param CustomerEntity            $c
     * @param UserEntity                $user
     * @param boolean                   $sentToMe
     * @param integer                   $nbSent
     *
     * @return void
     */
    public function sendEmail( $browser, $pm, $c, $user, $sentToMe, $nbSent ){

        // Test Send email to me
        $browser->click( "#peering-request-" . $c[ "id" ] )
            ->waitForText( "Are you sure you want to send a peering request to this member? You already sent one today." )
            ->press('OK' )
            ->waitForText( "Send Peering Request by Email" )
            ->click( $sentToMe ? '#modal-peering-request-sendtome' : '#modal-peering-request-send' )
            ->waitForText( "Success" )
            ->assertSee( $sentToMe ? "Peering request sample sent to your own email address (" . $user->getEmail() . ")." : "Peering request sent to" )
            ->click( ".bootbox-close-button" )
            ->waitUntilMissing( ".modal-backdrop" );

        // Check value in DB
        D2EM::refresh( $pm );

        $this->assertEquals( $nbSent ,$pm->getEmailsSent() );

    }

    /**
     * @param Browser               $browser
     * @param PeeringManagerEntity  $pm
     * @param CustomerEntity        $c
     * @param string                $status
     */
    public function markPeering( $browser, $pm , $c, $status ){

        $this->assertEquals( false, $status == "peered" ? $pm->getPeered() : $pm->getRejected() );

        $browser->press( "#dropdown-mark-peering-" . $c[ "id" ] )
                ->press( $status == "peered" ? "#mark-peered-". $c[ "id" ] : "#mark-rejected-". $c[ "id" ] )
                ->assertPathIs( '/peering-manager' )
                ->assertSee( $status == "peered" ? 'Peered flag set for ' . $c[ "name" ] : 'Ignored / rejected flag set for ' . $c[ "name" ] );




        // Check value in DB
        D2EM::refresh( $pm );
        $this->assertEquals( true, $status == "peered" ? $pm->getPeered() : $pm->getRejected() );

        $browser->press( $status == "peered" ? "#peering-peers-li" : "#peering-rejected-li" )
                ->assertSee( $c[ "name" ] )
                ->waitFor(  "#dropdown-mark-peering-" . $c[ "id" ] )
                ->press(    "#dropdown-mark-peering-" . $c[ "id" ] )
                ->press(    $status == "peered" ? "#mark-peered-". $c[ "id" ] : "#mark-rejected-". $c[ "id" ] )
                ->assertPathIs( '/peering-manager' )
                ->assertSee( $status == "peered" ? 'Peered flag cleared for ' . $c[ "name" ] : 'Ignored / rejected flag cleared for ' . $c[ "name" ] );

        // Check value in DB
        D2EM::refresh( $pm );

        $this->assertEquals( false, $status == "peered" ? $pm->getPeered() : $pm->getRejected() );
    }

}