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

use Entities\Router as RouterEntity;
use Entities\User as UserEntity;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RouterControllerTest extends DuskTestCase
{

    public function tearDown(): void
    {
        $router = D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => 'dusk-ci-test' ] );
        if( $router ) {
            D2EM::remove( $router );
            D2EM::flush();
        }

        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testAdd()
    {

        $this->browse(function (Browser $browser) {
            $browser->resize( 1600,1200 )
                    ->visit('/login')
                    ->type( 'username', 'travis' )
                    ->type( 'password', 'travisci' )
                    ->press( '#login-btn' )
                    ->assertPathIs( '/admin' );

            $browser->visit( '/router/add' )
                    ->assertSee( 'Handle' );

            // 1. test add
            $browser->type('handle',  'dusk-ci-test')
                ->select( 'vlan',     '2' )
                ->select( 'protocol', '6' )
                ->select( 'type', '1' )
                ->type( 'name', 'Travis CI Test' )
                ->type( 'shortname', 'citest' )
                ->type( 'router_id', '192.0.2.1' )
                ->type( 'peering_ip', '2001:db8::1' )
                ->type( 'asn', '65544' )
                ->select( 'software', 1 )
                ->type( 'software_version', '2.0.4' )
                ->type( 'operating_system', 'Ubuntu Linux' )
                ->type( 'operating_system_version', '18.04 LTS' )
                ->type( 'mgmt_host', '192.0.2.1' )
                ->select( 'api_type', 1 )
                ->type( 'api', 'https://api.example.com' )
                ->select( 'lg_access', 2 )
                ->check('quarantine')
                ->check('bgp_lc')
                ->check('rpki')
                //->check('rfc1997_passthru')
                ->check('skip_md5')
                ->type( 'template', 'api/v4/router/server/bird/standard' )
                ->press('Add Router')
                ->assertPathIs('/router/list');

            /** @var RouterEntity $router */
            $router = D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => 'dusk-ci-test' ] );


            // 2. test added data in database against expected values
            $this->assertInstanceOf( RouterEntity::class, $router );
            $this->assertEquals( 'dusk-ci-test', $router->getHandle() );
            $this->assertEquals( '2', $router->getVlan()->getId() );
            $this->assertEquals( '6', $router->getProtocol() );
            $this->assertEquals( '1', $router->getType() );
            $this->assertEquals( 'Travis CI Test', $router->getName() );
            $this->assertEquals( 'citest', $router->shortname() );
            $this->assertEquals( '192.0.2.1', $router->getRouterId() );
            $this->assertEquals( '2001:db8::1', $router->peeringIp() );
            $this->assertEquals( '65544', $router->getAsn() );
            $this->assertEquals( '1', $router->getSoftware() );
            $this->assertEquals( '2.0.4', $router->getSoftwareVersion() );
            $this->assertEquals( 'Ubuntu Linux', $router->getOperatingSystem() );
            $this->assertEquals( '18.04 LTS', $router->getOperatingSystemVersion() );
            $this->assertEquals( '192.0.2.1', $router->getMgmtHost() );
            $this->assertEquals( '1', $router->getApiType() );
            $this->assertEquals( 'https://api.example.com', $router->getApi() );
            $this->assertEquals( '2', $router->getLgAccess() );
            $this->assertEquals( true, $router->getQuarantine() );
            $this->assertEquals( true, $router->getBgpLc() );
            //$this->assertEquals( true, $router->getRFC1997Passthru() );
            $this->assertEquals( true, $router->getRPKI() );
            $this->assertEquals( true, $router->getSkipMd5() );
            $this->assertEquals( 'api/v4/router/server/bird/standard', $router->getTemplate() );

            // 3. browse to edit router object: $browser->visit( '/router/edit/' . $router->getId() )
            $browser->visit( '/router/edit/' .  $router->getId() );

            // 4. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue('handle', 'dusk-ci-test')
                ->assertSelected('vlan', '2')
                ->assertSelected('protocol', '6')
                ->assertSelected('type', '1')
                ->assertInputValue('name', 'Travis CI Test')
                ->assertInputValue('shortname', 'citest')
                ->assertInputValue('router_id', '192.0.2.1')
                ->assertInputValue('peering_ip', '2001:db8::1')
                ->assertInputValue('asn', '65544')
                ->assertSelected('software', '1')
                ->assertInputValue( 'software_version', '2.0.4' )
                ->assertInputValue( 'operating_system', 'Ubuntu Linux' )
                ->assertInputValue( 'operating_system_version', '18.04 LTS' )
                ->assertInputValue('mgmt_host', '192.0.2.1')
                ->assertSelected('api_type', '1')
                ->assertInputValue( 'api', 'https://api.example.com' )
                ->assertSelected( 'lg_access', '2' )
                ->assertChecked( 'quarantine' )
                ->assertChecked( 'bgp_lc' )
                //->assertChecked( 'rfc1997_passthru' )
                ->assertChecked( 'rpki' )
                ->assertChecked( 'skip_md5' )
                ->assertInputValue( 'template', 'api/v4/router/server/bird/standard' );




            // 5. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->select( 'vlan',     '1' )
                ->select( 'protocol',  '4' )
                ->select( 'type', '2' )
                ->type( 'name', 'Travis CI Test 2' )
                ->type( 'shortname', 'citest2' )
                ->type( 'router_id', '192.0.2.20' )
                ->type( 'peering_ip', '192.0.2.21' )
                ->type( 'asn', '65545' )
                ->select( 'software', '2' )
                ->type( 'software_version', '1.6.4' )
                ->type( 'operating_system', 'FreeBSD' )
                ->type( 'operating_system_version', '11.4' )
                ->type( 'mgmt_host', '192.0.2.10' )
                ->select( 'api_type', '0' )
                ->type( 'api', 'https://api2.example.com' )
                ->select( 'lg_access', '1' )
                ->uncheck('quarantine')
                ->uncheck('bgp_lc')
                ->uncheck('rpki')
                //->uncheck('rfc1997_passthru')
                ->uncheck('skip_md5')
                ->type( 'template', 'api/v4/router/as112/bird/standard' )
                ->press('Save Changes')
                ->assertPathIs('/router/list');


            // 6. repeat database load and database object check for new values (repeat 2)
            D2EM::refresh( $router );

            $this->assertInstanceOf( RouterEntity::class, $router );
            $this->assertEquals( 'dusk-ci-test', $router->getHandle() );
            $this->assertEquals( '1', $router->getVlan()->getId() );
            $this->assertEquals( '4', $router->getProtocol() );
            $this->assertEquals( '2', $router->getType() );
            $this->assertEquals( 'Travis CI Test 2', $router->getName() );
            $this->assertEquals( 'citest2', $router->shortname() );
            $this->assertEquals( '192.0.2.20', $router->getRouterId() );
            $this->assertEquals( '192.0.2.21', $router->peeringIp() );
            $this->assertEquals( '65545', $router->getAsn() );
            $this->assertEquals( '2', $router->getSoftware() );
            $this->assertEquals( '1.6.4', $router->getSoftwareVersion() );
            $this->assertEquals( 'FreeBSD', $router->getOperatingSystem() );
            $this->assertEquals( '11.4', $router->getOperatingSystemVersion() );
            $this->assertEquals( '192.0.2.10', $router->getMgmtHost() );
            $this->assertEquals( '0', $router->getApiType() );
            $this->assertEquals( 'https://api2.example.com', $router->getApi() );
            $this->assertEquals( '1', $router->getLgAccess() );
            $this->assertEquals( false, $router->getQuarantine() );
            $this->assertEquals( false, $router->getBgpLc() );
            $this->assertEquals( false, $router->getRPKI() );
            //$this->assertEquals( false, $router->getRFC1997Passthru() );
            $this->assertEquals( false, $router->getSkipMd5() );
            $this->assertEquals( 'api/v4/router/as112/bird/standard', $router->getTemplate() );


            // 7. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( '/router/edit/' .  $router->getId() )
                ->assertSee( 'Handle' );

            $browser->assertSelected('vlan', '1')
                ->assertSelected('protocol', '4')
                ->assertSelected('type', '2')
                ->assertSelected('software', '2')
                ->assertSelected('api_type', '0')
                ->assertSelected( 'lg_access', '1' )
                ->assertNotChecked( 'quarantine' )
                ->assertNotChecked( 'bgp_lc' )
                ->assertNotChecked( 'rpki' )
                //->assertNotChecked( 'rfc1997_passthru' )
                ->assertNotChecked( 'skip_md5' );


            // 8. submit with no changes and verify no changes in database
            $browser->press('Save Changes')
                ->assertPathIs('/router/list');


            // . repeat database load and database object check for new values (repeat 2)
            D2EM::refresh( $router );

            $this->assertInstanceOf( RouterEntity::class, $router );
            $this->assertEquals( 'dusk-ci-test', $router->getHandle() );
            $this->assertEquals( '1', $router->getVlan()->getId() );
            $this->assertEquals( '4', $router->getProtocol() );
            $this->assertEquals( '2', $router->getType() );
            $this->assertEquals( 'Travis CI Test 2', $router->getName() );
            $this->assertEquals( 'citest2', $router->shortname() );
            $this->assertEquals( '192.0.2.20', $router->getRouterId() );
            $this->assertEquals( '192.0.2.21', $router->peeringIp() );
            $this->assertEquals( '65545', $router->getAsn() );
            $this->assertEquals( '2', $router->getSoftware() );
            $this->assertEquals( '1.6.4', $router->getSoftwareVersion() );
            $this->assertEquals( 'FreeBSD', $router->getOperatingSystem() );
            $this->assertEquals( '11.4', $router->getOperatingSystemVersion() );
            $this->assertEquals( '192.0.2.10', $router->getMgmtHost() );
            $this->assertEquals( '0', $router->getApiType() );
            $this->assertEquals( 'https://api2.example.com', $router->getApi() );
            $this->assertEquals( '1', $router->getLgAccess() );
            $this->assertEquals( false, $router->getQuarantine() );
            $this->assertEquals( false, $router->getBgpLc() );
            $this->assertEquals( false, $router->getRPKI() );
            //$this->assertEquals( false, $router->getRFC1997Passthru() );
            $this->assertEquals( false, $router->getSkipMd5() );
            $this->assertEquals( 'api/v4/router/as112/bird/standard', $router->getTemplate() );

            // 9. edit again and check all checkboxes and submit
            $browser->visit( '/router/edit/' .  $router->getId() )
                ->assertSee( 'Handle' );

            $browser->check('quarantine')
                ->check('bgp_lc')
                ->check('rpki')
                //->check('rfc1997_passthru')
                ->check('skip_md5')
                ->press('Save Changes')
                ->assertPathIs('/router/list');


            // 10. verify checkbox bool elements in database are all true
            D2EM::refresh( $router );

            $this->assertEquals( true, $router->getQuarantine() );
            $this->assertEquals( true, $router->getBgpLc() );
            $this->assertEquals( true, $router->getRPKI() );
            //$this->assertEquals( true, $router->getRFC1997Passthru() );
            $this->assertEquals( true, $router->getSkipMd5() );

            // 11. delete the router in the UI and verify via success message text and location
            $browser->visit( '/router/list/' )
                ->press('#delete-router-' . $router->getId() )
                ->waitForText( 'Do you want to delete this router' )
                ->press('Delete' );

            $browser->assertSee( 'The router has been successfully deleted.' );

            // 12. do a D2EM findOneBy and verify false/null
            $this->assertEquals( null, D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => 'dusk-ci-test' ] ) );
        });

    }
}
