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

use IXP\Models\Infrastructure;

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Test Infrastructure Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class InfrastructureControllerTest extends DuskTestCase
{
    /**
     * @throws
     */
    public function tearDown(): void
    {
        if( $infra = Infrastructure::whereName( 'Infrastructure PHPUnit' )->first() ) {
            $infra->delete();
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
    public function testAdd(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin' );

            $browser->visit( route( 'infrastructure@list' ) )
                ->assertSee( 'Infrastructures' )
                ->assertSee( 'represents a collection of switches which form an IXP\'s peering LAN' );

            $browser->visit( route( 'infrastructure@create' ) )
                ->assertSee( 'Create Infrastructure' )
                ->waitForText( "Choose the matching IX-F IXP" );

            // 1. test add empty inputs
            $browser->press( 'Create' )
                ->waitForLocation( route( 'infrastructure@create' ) )
                ->assertSee( "The name field is required." )
                ->assertSee( "The shortname field is required." );

            // 1. test add
            $browser->type( 'name', 'Infrastructure #1' )
                ->type( 'shortname', 'phpunit' )
                ->select( 'country', 'IE' )
                ->check( 'isPrimary' )
                ->check( 'exclude_from_ixf_export' )
                ->select( 'ixf_ix_id', '1' )
                ->select( 'peeringdb_ix_id', '1' )
                ->type( 'notes', 'I am a note' )
                ->press( 'Create' )
                ->waitForLocation( route( 'infrastructure@create' ) )
                ->assertSee( "The name has already been taken" )
                ->type( 'name', 'Infrastructure PHPUnit' )
                ->select( 'ixf_ix_id', '1' )
                ->select( 'peeringdb_ix_id', '1' )
                ->press( 'Create' )
                ->waitForLocation( route( 'infrastructure@list' ) )
                ->assertSee( "Infrastructure created" )
                ->assertSee( "Infrastructure PHPUnit" )
                ->assertSee( "phpunit" );

            $infra = Infrastructure::whereName( 'Infrastructure PHPUnit' )->first();

            // 2. test added data in database against expected values
            $this->assertInstanceOf( Infrastructure::class, $infra );
            $this->assertEquals( 'Infrastructure PHPUnit', $infra->name );
            $this->assertEquals( 'phpunit', $infra->shortname );
            $this->assertEquals( 'IE', $infra->country );
            $this->assertEquals( 'I am a note', $infra->notes );
            $this->assertEquals( '1', $infra->ixf_ix_id );
            $this->assertEquals( '1', $infra->peeringdb_ix_id );
            $this->assertTrue( $infra->isPrimary );
            $this->assertTrue( $infra->exclude_from_ixf_export );
            
            // 3. browse to edit infrastructure object:
            $browser->click( '#e2f-list-edit-' . $infra->id )
                ->waitForText( 'Edit Infrastructure' );

            // 4. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue( 'name', 'Infrastructure PHPUnit' )
                ->assertInputValue( 'shortname', 'phpunit' )
                ->assertSelected( 'country', 'IE' )
                ->assertInputValue( 'notes', 'I am a note' )
                ->assertChecked( 'isPrimary' )
                ->assertChecked( 'exclude_from_ixf_export' )
                ->assertSelected( 'ixf_ix_id', '1' )
                ->assertSelected( 'peeringdb_ix_id', '1' );


            // 5. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->select( 'ixf_ix_id', '2' )
                ->select( 'peeringdb_ix_id', '2' )
                ->select( 'country', 'FR' )
                ->uncheck( 'isPrimary' )
                ->uncheck( 'exclude_from_ixf_export' )
                ->press( 'Save Changes' )
                ->waitForLocation( route( 'infrastructure@list' ) )
                ->assertSee( "Infrastructure updated" );


            // 6. repeat database load and database object check for new values (repeat 2)
            $infra->refresh();

            $this->assertInstanceOf( Infrastructure::class, $infra );
            $this->assertEquals( 'Infrastructure PHPUnit', $infra->name );
            $this->assertEquals( 'phpunit', $infra->shortname );
            $this->assertEquals( 'I am a note', $infra->notes );
            $this->assertEquals( 'FR', $infra->country );
            $this->assertEquals( '2', $infra->ixf_ix_id );
            $this->assertEquals( '2', $infra->peeringdb_ix_id );
            $this->assertFalse( $infra->isPrimary );
            $this->assertFalse( $infra->exclude_from_ixf_export );


            // 7. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( route( 'infrastructure@edit', $infra->id ) )
                ->assertSee( 'Edit Infrastructure' );

            $browser->assertInputValue( 'name', 'Infrastructure PHPUnit' )
                ->assertInputValue( 'shortname', 'phpunit' )
                ->assertInputValue( 'notes', 'I am a note' )
                ->assertNotChecked( 'isPrimary' )
                ->assertNotChecked( 'exclude_from_ixf_export' )
                ->assertSelected( 'ixf_ix_id', '2' )
                ->assertSelected( 'country', 'FR' )
                ->assertSelected( 'peeringdb_ix_id', '2' );


            // 8. submit with no changes and verify no changes in database
            $browser->press( 'Save Changes' )
                ->waitForLocation( route( 'infrastructure@list' ) );


            // 6. repeat database load and database object check for new values (repeat 2)
            $infra->refresh();

            $this->assertInstanceOf( Infrastructure::class, $infra );
            $this->assertEquals( 'Infrastructure PHPUnit', $infra->name );
            $this->assertEquals( 'phpunit', $infra->shortname );
            $this->assertEquals( 'FR', $infra->country );
            $this->assertEquals( '2', $infra->ixf_ix_id );
            $this->assertEquals( '2', $infra->peeringdb_ix_id );
            $this->assertFalse( $infra->isPrimary );
            $this->assertFalse( $infra->exclude_from_ixf_export );

            // 9. edit again and check all checkboxes and submit
            $browser->visit( route( 'infrastructure@edit', $infra->id ) )
                ->waitForText( 'Edit Infrastructure' )
                ->check( 'isPrimary' )
                ->check( 'exclude_from_ixf_export' )
                ->press( 'Save Changes' )
                ->waitForLocation( route( 'infrastructure@list' ) );


            // 10. verify checkbox bool elements in database are all true
            $infra->refresh();
            
            $this->assertTrue( $infra->isPrimary );
            $this->assertTrue( $infra->exclude_from_ixf_export );

            // 11. delete the router in the UI and verify via success message text and location
            $browser->visit( route( 'infrastructure@list' ) )
                ->press( '#e2f-list-delete-' . $infra->id )
                ->waitForText( 'Do you really want to delete this infrastructure' )
                ->press( 'Delete' );

            $browser->waitForText( 'Infrastructure deleted.' );

            // 12. check if object doesn't exist anymore
            $this->assertTrue( Infrastructure::whereName( 'Infrastructure PHPUnit' )->doesntExist() );
        } );
    }
}