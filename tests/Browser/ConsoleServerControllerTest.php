<?php

namespace Tests\Browser;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\ConsoleServer;
use IXP\Models\ConsoleServerConnection;
use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Cabinet Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConsoleServerControllerTest extends DuskTestCase
{

    /**
     * @throws
     */
    public function tearDown(): void
    {
        foreach( [ 'Remove test Connect to Console Server Test2', 'Server Connect to Console Server Test2', 'Server Connect2 to Console Server Test2' ] as $description ) {
            if( $c = ConsoleServerConnection::where('description', $description )->first() ) {
                $c->delete();
            }
        }
        foreach( [ 'Console Server Test', 'Console Server Test2' ] as $name ) {
            if( $c = ConsoleServer::whereName( $name )->first() ) {
                $c->delete();
            }
        }

        parent::tearDown();
    }

    /**
     * Console Server list, add, edit, remove test
     *
     * @return ConsoleServerControllerTest
     * @throws \Throwable
     */
    public function testConsoleServers(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin' );

            $browser->visit( '/console-server/list' )
                ->waitForText( 'Console Servers' );

            $browser->visit( '/console-server/create' )
                ->waitForText( 'Console Servers / Create Console Server' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            // 1. test add empty inputs
            $browser->press( 'Create' )
                ->waitForLocation( '/console-server/create' )
                ->assertSee( "The name field is required." )
                ->assertSee( "The hostname field is required." )
                ->assertSee( "The cabinet id field is required." )
                ->assertSee( "The vendor id field is required." );

            // 2. test add #1
            $browser->type( 'name', 'Console Server Test' )
                ->type( 'hostname', 'test.host.local' )
                ->select( 'cabinet_id', 1 )
                ->select( 'vendor_id', 1 )
                ->type( 'model', 'test model' )
                ->type( 'serialNumber', 'Model-11231' )
                ->check( 'active' )
                ->type( 'notes', 'test notes' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Create' )
                ->waitForLocation( '/console-server/list' )
                ->assertSee( "Console Server created." )
                ->assertSee( "Console Server Test" );

            // 3. test create same name
            $browser->visit( '/console-server/create' )
                ->waitForText( 'Console Servers / Create Console Server' );

            $browser->type( 'name', 'Console Server Test' )
                ->type( 'hostname', 'test2.host.local' )
                ->select( 'cabinet_id', 1 )
                ->select( 'vendor_id', 1 )
                ->type( 'model', 'test model2' )
                ->type( 'serialNumber', 'Modal-21231' )
                ->check( 'active' )
                ->type( 'notes', 'test notes2' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Create' )
                ->waitForLocation( '/console-server/create' )
                ->assertSee( "The name has already been taken" );

            $consoleServer = ConsoleServer::whereName( 'Console Server Test' )->first();

            // 4. test added data in database against expected values
            $this->assertInstanceOf( ConsoleServer::class, $consoleServer );

            $this->assertEquals( 'Console Server Test', $consoleServer->name );
            $this->assertEquals( 'test.host.local', $consoleServer->hostname );
            $this->assertEquals( '1', $consoleServer->cabinet_id );
            $this->assertEquals( '1', $consoleServer->vendor_id );
            $this->assertEquals( 'test model', $consoleServer->model );
            $this->assertEquals( 'Model-11231', $consoleServer->serialNumber );
            $this->assertEquals( '1', $consoleServer->active );
            $this->assertEquals( 'test notes', $consoleServer->notes );

            // 5. browse to edit console server object:
            $browser->visit( '/console-server/edit/' . $consoleServer->id )
                ->assertSee( 'Console Servers / Edit Console Server' );

            // 6. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue( 'name', 'Console Server Test' )
                ->assertInputValue( 'hostname', 'test.host.local' )
                ->assertSelected( 'cabinet_id', '1' )
                ->assertSelected( 'vendor_id', '1' )
                ->assertInputValue( 'model', 'test model' )
                ->assertInputValue( 'serialNumber', 'Model-11231' )
                ->assertChecked( 'active')
                ->assertInputValue( 'notes', 'test notes' );

            // 7. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->uncheck( 'active' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Save Changes' )
                ->waitForLocation( '/console-server/list' )
                ->assertSee( "Console Server updated" );


            // 8. repeat database load and database object check for new values (repeat 2)
            $consoleServer->refresh();

            $this->assertEquals( 'Console Server Test', $consoleServer->name );
            $this->assertEquals( 'test.host.local', $consoleServer->hostname );
            $this->assertEquals( '1', $consoleServer->cabinet_id );
            $this->assertEquals( '1', $consoleServer->vendor_id );
            $this->assertEquals( 'test model', $consoleServer->model );
            $this->assertEquals( 'Model-11231', $consoleServer->serialNumber );
            $this->assertEquals( '0', $consoleServer->active );
            $this->assertEquals( 'test notes', $consoleServer->notes );


            // 9. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( '/console-server/edit/' . $consoleServer->id )
                ->assertSee( 'Console Servers / Edit Console Server' );

            $browser->assertInputValue( 'name', 'Console Server Test' )
                ->assertInputValue( 'hostname', 'test.host.local' )
                ->assertSelected( 'cabinet_id', '1' )
                ->assertSelected( 'vendor_id', '1' )
                ->assertInputValue( 'model', 'test model' )
                ->assertInputValue( 'serialNumber', 'Model-11231' )
                ->assertNotChecked( 'active')
                ->assertInputValue( 'notes', 'test notes' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            // 10. submit with no changes and verify no changes in database
            $browser->press( 'Save Changes' )
                ->waitForLocation( '/console-server/list' );

            // 11. repeat database load and database object check for new values (repeat 2)
            $consoleServer->refresh();

            $this->assertEquals( 'Console Server Test', $consoleServer->name );
            $this->assertEquals( 'test.host.local', $consoleServer->hostname );
            $this->assertEquals( '1', $consoleServer->cabinet_id );
            $this->assertEquals( '1', $consoleServer->vendor_id );
            $this->assertEquals( 'test model', $consoleServer->model );
            $this->assertEquals( 'Model-11231', $consoleServer->serialNumber );
            $this->assertEquals( '0', $consoleServer->active );
            $this->assertEquals( 'test notes', $consoleServer->notes );

            // 12. edit object
            $browser->visit( '/console-server/edit/' . $consoleServer->id )
                ->assertSee( 'Console Servers / Edit Console Server' );

            $browser->type( 'name', 'Console Server Test2' )
                ->type( 'hostname', 'test2.host.local' )
                ->select( 'cabinet_id', 1 )
                ->select( 'vendor_id', 1 )
                ->type( 'model', 'test model2' )
                ->type( 'serialNumber', 'Model-21231' )
                ->check( 'active')
                ->type( 'notes', 'test notes2' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Save Changes' )
                ->waitForLocation( '/console-server/list' );

            // 13. verify object values
            $consoleServer->refresh();

            $this->assertEquals( 'Console Server Test2', $consoleServer->name );
            $this->assertEquals( 'test2.host.local', $consoleServer->hostname );
            $this->assertEquals( '1', $consoleServer->cabinet_id );
            $this->assertEquals( '1', $consoleServer->vendor_id );
            $this->assertEquals( 'test model2', $consoleServer->model );
            $this->assertEquals( 'Model-21231', $consoleServer->serialNumber );
            $this->assertEquals( '1', $consoleServer->active );
            $this->assertEquals( 'test notes2', $consoleServer->notes );

            $browser->visit( '/console-server-connection/list' )
                ->assertSee( 'Console Server Connections' );

            $browser->visit( '/console-server-connection/create' )
                ->assertSee( 'Console Server Connections / Create Console Server Connection' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            // 1. test add empty inputs
            $browser->press( 'Create' )
                ->waitForLocation( '/console-server-connection/create' )
                ->assertSee( "The description field is required." )
                ->assertSee( "The console server id field is required." )
                ->assertSee( "The custid field is required." )
                ->assertSee( "The port field is required." );

            // 2. test add #1
            $browser->type( 'description', 'Server Connect to Console Server Test2' )
                ->select( 'console_server_id', $consoleServer->id )
                ->select( 'custid', '1' )
                ->type( 'port', '5' )
                ->select( 'speed', '300' )
                ->select( 'parity', '1' )
                ->select( 'stopbits', '1' )
                ->select( 'flowcontrol', '1' )
                ->type( 'notes', 'test notes' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Create' )
                ->waitForLocation( '/console-server-connection/list' )
                ->assertSee( "Console Server Connection created." )
                ->assertSee( "Server Connect to Console Server Test2" );

            $serverConnection = ConsoleServerConnection::where( 'description','Server Connect to Console Server Test2' )->first();

            // 4. test added data in database against expected values
            $this->assertInstanceOf( ConsoleServerConnection::class, $serverConnection );

            $this->assertEquals( 'Server Connect to Console Server Test2', $serverConnection->description );
            $this->assertEquals( $consoleServer->id, $serverConnection->console_server_id );
            $this->assertEquals( '1', $serverConnection->custid );
            $this->assertEquals( '5', $serverConnection->port );
            $this->assertEquals( '300', $serverConnection->speed );
            $this->assertEquals( '1', $serverConnection->parity );
            $this->assertEquals( '1', $serverConnection->stopbits );
            $this->assertEquals( '1', $serverConnection->flowcontrol );
            $this->assertEquals( '0', $serverConnection->autobaud );
            $this->assertEquals( 'test notes', $serverConnection->notes );

            // 5. create another connection for the single remove test
            $browser->visit( '/console-server-connection/create' )
                ->assertSee( 'Console Server Connections / Create Console Server Connection' )
                ->type( 'description', 'Remove test Connect to Console Server Test2' )
                ->select( 'console_server_id', $consoleServer->id )
                ->select( 'custid', '1' )
                ->type( 'port', '2' )
                ->select( 'speed', '300' )
                ->select( 'parity', '1' )
                ->select( 'stopbits', '1' )
                ->select( 'flowcontrol', '1' )
                ->type( 'notes', 'test notes of Remove test Connect to Console Server Test2' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Create' )
                ->waitForLocation( '/console-server-connection/list' )
                ->assertSee( "Console Server Connection created." )
                ->assertSee( "Remove test Connect to Console Server Test2" );

            $testConnection = ConsoleServerConnection::where( 'description','Remove test Connect to Console Server Test2' )->first();

            // 4. test added data in database against expected values
            $this->assertInstanceOf( ConsoleServerConnection::class, $testConnection );

            $this->assertEquals( 'Remove test Connect to Console Server Test2', $testConnection->description );
            $this->assertEquals( $consoleServer->id, $testConnection->console_server_id );
            $this->assertEquals( '1', $testConnection->custid );
            $this->assertEquals( '2', $testConnection->port );
            $this->assertEquals( '300', $testConnection->speed );
            $this->assertEquals( '1', $testConnection->parity );
            $this->assertEquals( '1', $testConnection->stopbits );
            $this->assertEquals( '1', $testConnection->flowcontrol );
            $this->assertEquals( '0', $testConnection->autobaud );
            $this->assertEquals( 'test notes of Remove test Connect to Console Server Test2', $testConnection->notes );

            // 5. browse to edit console server object:
            $browser->visit( '/console-server-connection/edit/' . $serverConnection->id )
                ->assertSee( 'Console Server Connections / Edit Console Server Connection' );

            // 6. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue( 'description', 'Server Connect to Console Server Test2' )
                ->assertSelected( 'console_server_id', $consoleServer->id )
                ->assertSelected( 'custid', '1' )
                ->assertInputValue( 'port', '5' )
                ->assertSelected( 'speed', '300' )
                ->assertSelected( 'parity', '1' )
                ->assertSelected( 'stopbits', '1' )
                ->assertSelected( 'flowcontrol', '1' )
                ->assertNotChecked( 'autobaud')
                ->assertInputValue( 'notes', 'test notes' );

            // 7. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->check( 'autobaud' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Save Changes' )
                ->waitForLocation( '/console-server-connection/list' )
                ->assertSee( "Console Server Connection updated" );


            // 8. repeat database load and database object check for new values (repeat 2)
            $serverConnection->refresh();

            $this->assertEquals( 'Server Connect to Console Server Test2', $serverConnection->description );
            $this->assertEquals( $consoleServer->id, $serverConnection->console_server_id );
            $this->assertEquals( '1', $serverConnection->custid );
            $this->assertEquals( '5', $serverConnection->port );
            $this->assertEquals( '300', $serverConnection->speed );
            $this->assertEquals( '1', $serverConnection->parity );
            $this->assertEquals( '1', $serverConnection->stopbits );
            $this->assertEquals( '1', $serverConnection->flowcontrol );
            $this->assertEquals( '1', $serverConnection->autobaud );
            $this->assertEquals( 'test notes', $serverConnection->notes );


            // 9. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( '/console-server-connection/edit/' . $serverConnection->id )
                ->assertSee( 'Console Server Connections / Edit Console Server Connection' );

            $browser->assertInputValue( 'description', 'Server Connect to Console Server Test2' )
                ->assertSelected( 'console_server_id', $consoleServer->id )
                ->assertSelected( 'custid', '1' )
                ->assertInputValue( 'port', '5' )
                // the speed, parity, stopbits, flowcontrol fields hidden on enabled autobaud, but the values still there in the DB
                ->assertChecked( 'autobaud')
                ->assertInputValue( 'notes', 'test notes' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            // 10. submit with no changes and verify no changes in database
            $browser->press( 'Save Changes' )
                ->waitForLocation( '/console-server-connection/list' );

            // 11. repeat database load and database object check for new values (repeat 2)
            $serverConnection->refresh();

            $this->assertEquals( 'Server Connect to Console Server Test2', $serverConnection->description );
            $this->assertEquals( $consoleServer->id, $serverConnection->console_server_id );
            $this->assertEquals( '1', $serverConnection->custid );
            $this->assertEquals( '5', $serverConnection->port );
            $this->assertEquals( '300', $serverConnection->speed );
            $this->assertEquals( '1', $serverConnection->parity );
            $this->assertEquals( '1', $serverConnection->stopbits );
            $this->assertEquals( '1', $serverConnection->flowcontrol );
            $this->assertEquals( '1', $serverConnection->autobaud );
            $this->assertEquals( 'test notes', $serverConnection->notes );

            // 12. edit object
            $browser->visit( '/console-server-connection/edit/' . $serverConnection->id )
                ->assertSee( 'Console Server Connections / Edit Console Server Connection' );

            $browser->type( 'description', 'Server Connect2 to Console Server Test2' )
                ->select( 'console_server_id', $consoleServer->id )
                ->select( 'custid', '1' )
                ->type( 'port', '8' )
                ->uncheck( 'autobaud' )
                ->pause(500)
                ->select( 'speed', '600' )
                ->select( 'parity', '2' )
                ->select( 'stopbits', '2' )
                ->select( 'flowcontrol', '2' )
                ->type( 'notes', 'test notes new' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Save Changes' )
                ->waitForLocation( '/console-server-connection/list' );

            // 13. verify object values
            $serverConnection->refresh();

            $this->assertEquals( 'Server Connect2 to Console Server Test2', $serverConnection->description );
            $this->assertEquals( $consoleServer->id, $serverConnection->console_server_id );
            $this->assertEquals( '1', $serverConnection->custid );
            $this->assertEquals( '8', $serverConnection->port );
            $this->assertEquals( '600', $serverConnection->speed );
            $this->assertEquals( '2', $serverConnection->parity );
            $this->assertEquals( '2', $serverConnection->stopbits );
            $this->assertEquals( '2', $serverConnection->flowcontrol );
            $this->assertEquals( '0', $serverConnection->autobaud );
            $this->assertEquals( 'test notes new', $serverConnection->notes );

            // 14. delete the router in the UI and verify via success message text and location
            $browser->visit( '/console-server-connection/list/' )
                ->click( '#d2f-list-delete-' . $testConnection->id )
                ->waitForText( 'Do you really want to delete this' )
                ->press( 'Delete' );

            $browser->waitForText( 'Console Server Connection deleted.' );
            $this->assertTrue( ConsoleServerConnection::where( 'description','Remove test Connect to Console Server Test2' )->doesntExist() );

            $browser->visit( '/console-server/list/' )
                ->click( '#d2f-list-delete-' . $consoleServer->id )
                ->waitForText( 'Do you really want to delete this' )
                ->press( 'Delete' );

            $browser->waitForText( 'Console Server deleted.' );

            $this->assertTrue( ConsoleServer::whereName( 'Console Server Test2' )->doesntExist() );
            $this->assertTrue( ConsoleServerConnection::where( 'description','Server Connect2 to Console Server Test2' )->doesntExist() );
        } );

    }
}