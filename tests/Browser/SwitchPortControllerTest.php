<?php

namespace Tests\Browser;

use D2EM;

use Tests\DuskTestCase;

use Laravel\Dusk\Browser;

use Entities\{
    SwitchPort              as SwitchPortEntity
};

class SwitchPortControllerTest extends DuskTestCase
{
    /**
     * Test the switch port functionnalities (add, edit, delete)
     *
     * @return void
     *
     * @throws
     */
    public function testSwitchPort()
    {
        $this->browse( function ( Browser $browser ) {

            $browser->resize( 1600, 1200 )
                ->visit( '/auth/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( 'submit' )
                ->assertPathIs( '/admin' );

            /**
             * Test view Switch information
             */

                $browser->press( "#lhs-menu-switches" )
                    ->press( "#lhs-menu-switch-ports" )
                    ->assertPathIs( '/switch-port/list' );

                /** @var array SwitchPortEntity */
                $sps = D2EM::getRepository( SwitchPortEntity::class )->getAllForFeList( (object)[] );

                /** @var SwitchPortEntity $sp */
                $sp = D2EM::getRepository( SwitchPortEntity::class )->find( reset($sps )[ "id" ] );

                $this->assertInstanceOf( SwitchPortEntity::class, $sp );

                $browser->press( "#d2f-list-view-" . $sp->getId() )
                    ->assertSee( "Details for switch port: " . $sp->getSwitcher()->getName() . " :: " . $sp->getName() . " (DB ID: " . $sp->getId() . ")" );

                $browser->press( "#d2f-list-a" )
                ->assertPathIs( "/switch-port/list" );


            /**
             * Test add Switch port form
             */

                $browser->press( "#add-switch-port" )
                    ->assertSee( "Add Switch Port" );

                // Fill the form with new value
                $browser->select(   'switchid', 2   )
                        ->select(   'type',     1   )
                        ->type(     'numfirst', '1' )
                        ->type(     'numports', '5' )
                        ->type(     'prefix',   'travistest%d')
                        ->click( "#generate-btn" )
                        ->click( "#btn-submit" )
                        ->assertPathIs( "/switch-port/list" )
                        ->assertSee( "Switch Port added" );



                $newSp = D2EM::getRepository( SwitchPortEntity::class )->findOneBy( [ "name" => "travistest1"] );

                /** @var SwitchPortEntity $newSp */

                // test added data in database against expected values
                $this->assertInstanceOf( SwitchPortEntity::class, $newSp );

                $this->assertEquals( "travistest1",     $newSp->getName() );
                $this->assertEquals( 2,                 $newSp->getSwitcher()->getId() );
                $this->assertEquals( 1,                 $newSp->getType() );
                $this->assertEquals( true,              $newSp->getActive() );
                $this->assertEquals( null,              $newSp->getIfIndex() );
                $this->assertEquals( null,              $newSp->getIfName() );
                $this->assertEquals( null,              $newSp->getIfAlias() );
                $this->assertEquals( null,              $newSp->getIfHighSpeed() );
                $this->assertEquals( null,              $newSp->getIfMtu() );
                $this->assertEquals( null,              $newSp->getIfPhysAddress() );
                $this->assertEquals( null,              $newSp->getIfAdminStatus() );
                $this->assertEquals( null,              $newSp->getIfOperStatus() );
                $this->assertEquals( null,              $newSp->getIfLastChange() );
                $this->assertEquals( null,              $newSp->getLastSnmpPoll() );
                $this->assertEquals( null,              $newSp->getLagIfIndex() );
                $this->assertEquals( null,              $newSp->getMauType() );
                $this->assertEquals( null,              $newSp->getMauState() );
                $this->assertEquals( null,              $newSp->getMauAvailability() );
                $this->assertEquals( null,              $newSp->getMauJacktype() );
                $this->assertEquals( null,              $newSp->getMauAutoNegSupported() );
                $this->assertEquals( null,              $newSp->getMauAutoNegAdminState() );



            /**
             * Test edit Switch port form
             */

                $browser->press( "#d2f-list-edit-" . $newSp->getId() )
                    ->assertSee( "Edit Switch" );

                // test that form is filled with all and the correct object informations
                $browser->assertSelected(   'switchid',     2 )
                        ->assertInputValue( 'name',         "travistest1" )
                        ->assertSelected(   'type',         1 )
                        ->assertChecked(    'active' );


                // submit unchanged form
                $browser->press(    'Save Changes')
                    ->assertPathIs('/switch-port/list')
                    ->assertSee( "Switch Port edited" );

                D2EM::refresh( $newSp );

                $this->assertInstanceOf( SwitchPortEntity::class, $newSp );

                $this->assertEquals( "travistest1",     $newSp->getName() );
                $this->assertEquals( 2,                 $newSp->getSwitcher()->getId() );
                $this->assertEquals( 1,                 $newSp->getType() );
                $this->assertEquals( true,              $newSp->getActive() );
                $this->assertEquals( null,              $newSp->getIfIndex() );
                $this->assertEquals( null,              $newSp->getIfName() );
                $this->assertEquals( null,              $newSp->getIfAlias() );
                $this->assertEquals( null,              $newSp->getIfHighSpeed() );
                $this->assertEquals( null,              $newSp->getIfMtu() );
                $this->assertEquals( null,              $newSp->getIfPhysAddress() );
                $this->assertEquals( null,              $newSp->getIfAdminStatus() );
                $this->assertEquals( null,              $newSp->getIfOperStatus() );
                $this->assertEquals( null,              $newSp->getIfLastChange() );
                $this->assertEquals( null,              $newSp->getLastSnmpPoll() );
                $this->assertEquals( null,              $newSp->getLagIfIndex() );
                $this->assertEquals( null,              $newSp->getMauType() );
                $this->assertEquals( null,              $newSp->getMauState() );
                $this->assertEquals( null,              $newSp->getMauAvailability() );
                $this->assertEquals( null,              $newSp->getMauJacktype() );
                $this->assertEquals( null,              $newSp->getMauAutoNegSupported() );
                $this->assertEquals( null,              $newSp->getMauAutoNegAdminState() );

                $browser->press( "#d2f-list-edit-" . $newSp->getId() )
                    ->assertSee( "Edit Switch" );

                // test that form is filled with all and the correct object informations
                $browser->assertSelected(   'switchid',     2 )
                        ->assertInputValue( 'name',         "travistest1" )
                        ->assertSelected(   'type',         1 )
                        ->assertChecked(    'active' );

                // Fill the form with new value
                $browser->select(   'switchid', 2 )
                        ->type(     'name',     'travistest6' )
                        ->select(   'type',     2 )
                        ->uncheck(  'active' )
                        ->press(    'Save Changes')
                        ->assertPathIs('/switch-port/list')
                        ->assertSee( "Switch Port edited" );


                $browser->press( "#d2f-list-edit-" . $newSp->getId() )
                        ->assertSee( "Edit Switch Port" );

                D2EM::refresh( $newSp );

                $this->assertInstanceOf( SwitchPortEntity::class, $newSp );

                $this->assertEquals( "travistest6",     $newSp->getName() );
                $this->assertEquals( 2,                 $newSp->getSwitcher()->getId() );
                $this->assertEquals( 2,                 $newSp->getType() );
                $this->assertEquals( false,             $newSp->getActive() );
                $this->assertEquals( null,              $newSp->getIfIndex() );
                $this->assertEquals( null,              $newSp->getIfName() );
                $this->assertEquals( null,              $newSp->getIfAlias() );
                $this->assertEquals( null,              $newSp->getIfHighSpeed() );
                $this->assertEquals( null,              $newSp->getIfMtu() );
                $this->assertEquals( null,              $newSp->getIfPhysAddress() );
                $this->assertEquals( null,              $newSp->getIfAdminStatus() );
                $this->assertEquals( null,              $newSp->getIfOperStatus() );
                $this->assertEquals( null,              $newSp->getIfLastChange() );
                $this->assertEquals( null,              $newSp->getLastSnmpPoll() );
                $this->assertEquals( null,              $newSp->getLagIfIndex() );
                $this->assertEquals( null,              $newSp->getMauType() );
                $this->assertEquals( null,              $newSp->getMauState() );
                $this->assertEquals( null,              $newSp->getMauAvailability() );
                $this->assertEquals( null,              $newSp->getMauJacktype() );
                $this->assertEquals( null,              $newSp->getMauAutoNegSupported() );
                $this->assertEquals( null,              $newSp->getMauAutoNegAdminState() );


                // test that form is filled with all and the correct object informations
                $browser->assertSelected(   'switchid',     2 )
                        ->assertInputValue( 'name',         "travistest6" )
                        ->assertSelected(   'type',         2 )
                        ->assertNotChecked(    'active' );

                // Test the checkbox (checked)
                $browser->check(  'active' )
                    ->press(    'Save Changes')
                    ->assertPathIs('/switch-port/list')
                    ->assertSee( "Switch Port edited" );

                D2EM::refresh( $newSp );

                $this->assertEquals( true,             $newSp->getActive() );


                $browser->press( "#d2f-list-edit-" . $newSp->getId() )
                    ->assertSee( "Edit Switch Port" );

                // Test the checkbox (unchecked)
                $browser->uncheck(  'active' )
                    ->press(    'Save Changes')
                    ->assertPathIs('/switch-port/list')
                    ->assertSee( "Switch Port edited" );

                // refresh the object
                D2EM::refresh( $newSp );

                // check that the attribute is false (unchecked checkbox)
                $this->assertEquals( false,             $newSp->getActive() );



            $browser->press( "#d2f-list-edit-" . $newSp->getId() )
                ->assertSee( "Edit Switch Port" );


                // Test the select
                $browser->select(  'type', 3 )
                    ->press(    'Save Changes')
                    ->assertPathIs('/switch-port/list')
                    ->assertSee( "Switch Port edited" );

                D2EM::refresh( $newSp );

                $this->assertEquals( 3,             $newSp->getType() );


                $browser->press( "#d2f-list-edit-" . $newSp->getId() )
                    ->assertSee( "Edit Switch Port" );

                // Test the select
                $browser->select(  'type', 4 )
                    ->press(    'Save Changes')
                    ->assertPathIs('/switch-port/list')
                    ->assertSee( "Switch Port edited" );

                D2EM::refresh( $newSp );

                $this->assertEquals( 4,             $newSp->getType() );

            /**
             * Test delete Switch port
             */

            // delete the switch
            $browser->press( "#d2f-list-delete-" . $newSp->getId() )
                ->waitForText( "Delete Switch Port" )
                ->press( "Delete" )
                ->assertSee( "Switch Port deleted." );
        });
    }

}