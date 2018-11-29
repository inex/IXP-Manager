<?php

namespace Tests\Browser;

use D2EM;

use Entities\Switcher as SwitcherEntity;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SwitchControllerTest extends DuskTestCase
{
    public function tearDown()
    {
        foreach( [ 'phpunit', 'phpunit2' ] as $name ) {
            $switch = D2EM::getRepository( SwitcherEntity::class )->findOneBy( [ 'name' => $name ] );

            if( $switch ) {
                D2EM::remove( $switch );
                D2EM::flush();
            }
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
                    ->visit('/auth/login')
                    ->type( 'username', 'travis' )
                    ->type( 'password', 'travisci' )
                    ->press( 'submit' )
                    ->assertPathIs( '/admin' );

            $browser->visit( '/switch/list' )
                ->assertSee( 'switch1' )
                ->assertSee( 'switch2' );

            $browser->visit( '/switch/add-by-snmp' )
                ->assertSee( 'Add Switch via SNMP' )
                ->assertSee( 'Hostname' )
                ->assertSee( 'SNMP Community' );


            // 1. test add by snmp and flow to next step:
            $browser->type( 'hostname', 'phpunit.test.example.com' )
                ->type( 'snmppasswd', 'mXPOSpC52cSFg1qN' )
                ->press('Next ≫' )
                ->assertPathIs('/switch/store-by-snmp' )
                ->assertInputValue( 'name',       'phpunit' )
                ->assertInputValue( 'hostname',   'phpunit.test.example.com' )
                ->assertInputValue( 'snmppasswd', 'mXPOSpC52cSFg1qN' )
                ->assertInputValue( 'model',      'FESX648' )
                ->assertSelected(   'vendorid',   '12')
                ->assertChecked( 'active' );

            // 2. test add step 2
            $browser->select( 'cabinetid', 1 )
                ->select( 'infrastructure', 1 )
                ->type( 'ipv4addr', '192.0.2.1' )
                ->type( 'ipv6addr', '2001:db8::999' )
                ->type( 'mgmt_mac_address', 'AA:00.11:BB.22-87' )
                ->type( 'asn', '65512' )
                ->type( 'loopback_ip', '127.0.0.1' )
                ->type( 'loopback_name', 'lo0' )
                ->type( 'notes', 'Test note' )
                ->press( 'Add' )
                ->assertPathIs('/switch/list' )
                ->assertSee( 'phpunit' )
                ->assertSee( 'FESX648' );

            // get the switch:
            /** @var SwitcherEntity $switch */
            $switch = D2EM::getRepository( SwitcherEntity::class )->findOneBy( [ 'name' => 'phpunit' ] );

            // test the values:
            $this->assertEquals( 'phpunit',                  $switch->getName() );
            $this->assertEquals( 'phpunit.test.example.com', $switch->getHostname() );
            $this->assertEquals( 'mXPOSpC52cSFg1qN',         $switch->getSnmppasswd() );
            $this->assertEquals( 1,                          $switch->getCabinet()->getId() );
            $this->assertEquals( 1,                          $switch->getInfrastructure()->getId() );
            $this->assertEquals( 12,                         $switch->getVendor()->getId() );
            $this->assertEquals( 'FESX648',                  $switch->getModel() );
            $this->assertEquals( true,                       $switch->getActive() );
            $this->assertEquals( '192.0.2.1',                $switch->getIpv4addr() );
            $this->assertEquals( '2001:db8::999',            $switch->getIpv6addr() );
            $this->assertEquals( 'aa0011bb2287',             $switch->getMgmtMacAddress() );
            $this->assertEquals( 65512,                      $switch->getAsn() );
            $this->assertEquals( '127.0.0.1',                $switch->getLoopbackIP() );
            $this->assertEquals( 'lo0',                      $switch->getLoopbackName() );
            $this->assertEquals( 'Test note',                $switch->getNotes() );

            // test that editing while not making any changes and saving changes nothing

            $browser->visit( '/switch/edit/' . $switch->getId() )
                ->assertPathIs('/switch/edit/' . $switch->getId() )
                ->press( 'Save Changes' )
                ->assertPathIs('/switch/list' )
                ->assertSee( 'phpunit' )
                ->assertSee( 'FESX648' );

            // test the values:
            $this->assertEquals( 'phpunit',                  $switch->getName() );
            $this->assertEquals( 'phpunit.test.example.com', $switch->getHostname() );
            $this->assertEquals( 'mXPOSpC52cSFg1qN',         $switch->getSnmppasswd() );
            $this->assertEquals( 1,                          $switch->getCabinet()->getId() );
            $this->assertEquals( 1,                          $switch->getInfrastructure()->getId() );
            $this->assertEquals( 12,                         $switch->getVendor()->getId() );
            $this->assertEquals( 'FESX648',                  $switch->getModel() );
            $this->assertEquals( true,                       $switch->getActive() );
            $this->assertEquals( '192.0.2.1',                $switch->getIpv4addr() );
            $this->assertEquals( '2001:db8::999',            $switch->getIpv6addr() );
            $this->assertEquals( 'aa0011bb2287',             $switch->getMgmtMacAddress() );
            $this->assertEquals( 65512,                      $switch->getAsn() );
            $this->assertEquals( '127.0.0.1',                $switch->getLoopbackIP() );
            $this->assertEquals( 'lo0',                      $switch->getLoopbackName() );
            $this->assertEquals( 'Test note',                $switch->getNotes() );


            // now test that editing while making changes works

            $browser->visit( '/switch/edit/' . $switch->getId() )
                ->assertPathIs('/switch/edit/' . $switch->getId() )
                ->type( 'name', 'phpunit2' )
                ->type( 'hostname', 'phpunit2.test.example.com' )
                ->type( 'snmppasswd', 'newpassword' )
                ->select( 'infrastructure', 2 )
                ->select( 'vendorid', 11 )
                ->type( 'model', 'TI24X' )
                ->uncheck( 'active' )
                ->type( 'ipv4addr', '192.0.2.2' )
                ->type( 'ipv6addr', '2001:db8::9999' )
                ->type( 'mgmt_mac_address', 'AA:00.11:BB.22-88' )
                ->type( 'asn', '65513' )
                ->type( 'loopback_ip', '127.0.0.2' )
                ->type( 'loopback_name', 'lo1' )
                ->type( 'notes', 'Test note 2' )
                ->press( 'Save Changes' )
                ->assertPathIs('/switch/list' )
                ->assertSee( 'phpunit2' )
                ->assertSee( 'TI24X' );

            D2EM::refresh($switch);

            // test the values:
            $this->assertEquals( 'phpunit2',                  $switch->getName() );
            $this->assertEquals( 'phpunit2.test.example.com', $switch->getHostname() );
            $this->assertEquals( 'newpassword',               $switch->getSnmppasswd() );
            $this->assertEquals( 1,                           $switch->getCabinet()->getId() );
            $this->assertEquals( 2,                           $switch->getInfrastructure()->getId() );
            $this->assertEquals( 11,                          $switch->getVendor()->getId() );
            $this->assertEquals( 'TI24X',                     $switch->getModel() );
            $this->assertEquals( false,                       $switch->getActive() );
            $this->assertEquals( '192.0.2.2',                 $switch->getIpv4addr() );
            $this->assertEquals( '2001:db8::9999',            $switch->getIpv6addr() );
            $this->assertEquals( 'aa0011bb2288',              $switch->getMgmtMacAddress() );
            $this->assertEquals( 65513,                       $switch->getAsn() );
            $this->assertEquals( '127.0.0.2',                 $switch->getLoopbackIP() );
            $this->assertEquals( 'lo1',                       $switch->getLoopbackName() );
            $this->assertEquals( 'Test note 2',               $switch->getNotes() );

            // test that editing while not making any changes and saving changes nothing
            // (this is a retest for, e.g. unchecked checkboxes)

            $browser->visit( '/switch/edit/' . $switch->getId() )
                ->assertPathIs('/switch/edit/' . $switch->getId() )
                ->press( 'Save Changes' )
                ->assertPathIs('/switch/list' )
                ->assertSee( 'phpunit2' )
                ->assertSee( 'TI24X' );

            // test the values:
            $this->assertEquals( 'phpunit2',                  $switch->getName() );
            $this->assertEquals( 'phpunit2.test.example.com', $switch->getHostname() );
            $this->assertEquals( 'newpassword',               $switch->getSnmppasswd() );
            $this->assertEquals( 1,                           $switch->getCabinet()->getId() );
            $this->assertEquals( 2,                           $switch->getInfrastructure()->getId() );
            $this->assertEquals( 11,                          $switch->getVendor()->getId() );
            $this->assertEquals( 'TI24X',                     $switch->getModel() );
            $this->assertEquals( false,                       $switch->getActive() );
            $this->assertEquals( '192.0.2.2',                 $switch->getIpv4addr() );
            $this->assertEquals( '2001:db8::9999',            $switch->getIpv6addr() );
            $this->assertEquals( 'aa0011bb2288',              $switch->getMgmtMacAddress() );
            $this->assertEquals( 65513,                       $switch->getAsn() );
            $this->assertEquals( '127.0.0.2',                 $switch->getLoopbackIP() );
            $this->assertEquals( 'lo1',                       $switch->getLoopbackName() );
            $this->assertEquals( 'Test note 2',               $switch->getNotes() );

            // delete this switch
            $browser->press( '#d2f-list-delete-' . $switch->getId() )
                ->waitForText( 'Do you really want to delete this' )
                ->press( 'Delete' )
                ->assertPathIs('/switch/list' )
                ->assertDontSee( 'phpunit2' )
                ->assertDontSee( 'TI24X' );


//            D2EM::remove( $switch );
//            D2EM::flush();

//            // 1. test add
//            $browser->type('handle',  'dusk-ci-test')
//                ->select( 'vlan',     '2' )
//                ->select( 'protocol', '6' )
//                ->select( 'type', '1' )
//                ->type( 'name', 'Travis CI Test' )
//                ->type( 'shortname', 'citest' )
//                ->type( 'router_id', '192.0.2.1' )
//                ->type( 'peering_ip', '2001:db8::1' )
//                ->type( 'asn', '65544' )
//                ->select( 'software', 1 )
//                ->type( 'mgmt_host', '192.0.2.1' )
//                ->select( 'api_type', 1 )
//                ->type( 'api', 'https://api.example.com' )
//                ->select( 'lg_access', 2 )
//                ->check('quarantine')
//                ->check('bgp_lc')
//                ->check('skip_md5')
//                ->type( 'template', 'api/v4/router/server/bird/standard' )
//                ->press('Add Router')
//                ->assertPathIs('/router/list');
//
//            /** @var RouterEntity $router */
//            $router = D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => 'dusk-ci-test' ] );
//
//
//            // 2. test added data in database against expected values
//            $this->assertInstanceOf( RouterEntity::class, $router );
//            $this->assertEquals( 'dusk-ci-test', $router->getHandle() );
//            $this->assertEquals( '2', $router->getVlan()->getId() );
//            $this->assertEquals( '6', $router->getProtocol() );
//            $this->assertEquals( '1', $router->getType() );
//            $this->assertEquals( 'Travis CI Test', $router->getName() );
//            $this->assertEquals( 'citest', $router->shortname() );
//            $this->assertEquals( '192.0.2.1', $router->getRouterId() );
//            $this->assertEquals( '2001:db8::1', $router->peeringIp() );
//            $this->assertEquals( '65544', $router->getAsn() );
//            $this->assertEquals( '1', $router->getSoftware() );
//            $this->assertEquals( '192.0.2.1', $router->getMgmtHost() );
//            $this->assertEquals( '1', $router->getApiType() );
//            $this->assertEquals( 'https://api.example.com', $router->getApi() );
//            $this->assertEquals( '2', $router->getLgAccess() );
//            $this->assertEquals( true, $router->getQuarantine() );
//            $this->assertEquals( true, $router->getBgpLc() );
//            $this->assertEquals( true, $router->getSkipMd5() );
//            $this->assertEquals( 'api/v4/router/server/bird/standard', $router->getTemplate() );
//
//            // 3. browse to edit router object: $browser->visit( '/router/edit/' . $router->getId() )
//            $browser->visit( '/router/edit/' .  $router->getId() );
//
//            // 4. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
//            $browser->assertInputValue('handle', 'dusk-ci-test')
//                ->assertSelected('vlan', '2')
//                ->assertSelected('protocol', '6')
//                ->assertSelected('type', '1')
//                ->assertInputValue('name', 'Travis CI Test')
//                ->assertInputValue('shortname', 'citest')
//                ->assertInputValue('router_id', '192.0.2.1')
//                ->assertInputValue('peering_ip', '2001:db8::1')
//                ->assertInputValue('asn', '65544')
//                ->assertSelected('software', '1')
//                ->assertInputValue('mgmt_host', '192.0.2.1')
//                ->assertSelected('api_type', '1')
//                ->assertInputValue( 'api', 'https://api.example.com' )
//                ->assertSelected( 'lg_access', '2' )
//                ->assertChecked( 'quarantine' )
//                ->assertChecked( 'bgp_lc' )
//                ->assertChecked( 'skip_md5' )
//                ->assertInputValue( 'template', 'api/v4/router/server/bird/standard' );
//
//
//
//
//            // 5. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
//            $browser->select( 'vlan',     '1' )
//                ->select( 'protocol',  '4' )
//                ->select( 'type', '2' )
//                ->type( 'name', 'Travis CI Test 2' )
//                ->type( 'shortname', 'citest2' )
//                ->type( 'router_id', '192.0.2.20' )
//                ->type( 'peering_ip', '192.0.2.21' )
//                ->type( 'asn', '65545' )
//                ->select( 'software', '2' )
//                ->type( 'mgmt_host', '192.0.2.10' )
//                ->select( 'api_type', '0' )
//                ->type( 'api', 'https://api2.example.com' )
//                ->select( 'lg_access', '1' )
//                ->uncheck('quarantine')
//                ->uncheck('bgp_lc')
//                ->uncheck('skip_md5')
//                ->type( 'template', 'api/v4/router/as112/bird/standard' )
//                ->press('Save Changes')
//                ->assertPathIs('/router/list');
//
//
//            // 6. repeat database load and database object check for new values (repeat 2)
//            D2EM::refresh( $router );
//
//            $this->assertInstanceOf( RouterEntity::class, $router );
//            $this->assertEquals( 'dusk-ci-test', $router->getHandle() );
//            $this->assertEquals( '1', $router->getVlan()->getId() );
//            $this->assertEquals( '4', $router->getProtocol() );
//            $this->assertEquals( '2', $router->getType() );
//            $this->assertEquals( 'Travis CI Test 2', $router->getName() );
//            $this->assertEquals( 'citest2', $router->shortname() );
//            $this->assertEquals( '192.0.2.20', $router->getRouterId() );
//            $this->assertEquals( '192.0.2.21', $router->peeringIp() );
//            $this->assertEquals( '65545', $router->getAsn() );
//            $this->assertEquals( '2', $router->getSoftware() );
//            $this->assertEquals( '192.0.2.10', $router->getMgmtHost() );
//            $this->assertEquals( '0', $router->getApiType() );
//            $this->assertEquals( 'https://api2.example.com', $router->getApi() );
//            $this->assertEquals( '1', $router->getLgAccess() );
//            $this->assertEquals( false, $router->getQuarantine() );
//            $this->assertEquals( false, $router->getBgpLc() );
//            $this->assertEquals( false, $router->getSkipMd5() );
//            $this->assertEquals( 'api/v4/router/as112/bird/standard', $router->getTemplate() );
//
//
//            // 7. edit again and assert that all checkboxes are unchecked and assert select values are as expected
//            $browser->visit( '/router/edit/' .  $router->getId() )
//                ->assertSee( 'Handle' );
//
//            $browser->assertSelected('vlan', '1')
//                ->assertSelected('protocol', '4')
//                ->assertSelected('type', '2')
//                ->assertSelected('software', '2')
//                ->assertSelected('api_type', '0')
//                ->assertSelected( 'lg_access', '1' )
//                ->assertNotChecked( 'quarantine' )
//                ->assertNotChecked( 'bgp_lc' )
//                ->assertNotChecked( 'skip_md5' );
//
//
//            // 8. submit with no changes and verify no changes in database
//            $browser->press('Save Changes')
//                ->assertPathIs('/router/list');
//
//
//            // 6. repeat database load and database object check for new values (repeat 2)
//            D2EM::refresh( $router );
//
//            $this->assertInstanceOf( RouterEntity::class, $router );
//            $this->assertEquals( 'dusk-ci-test', $router->getHandle() );
//            $this->assertEquals( '1', $router->getVlan()->getId() );
//            $this->assertEquals( '4', $router->getProtocol() );
//            $this->assertEquals( '2', $router->getType() );
//            $this->assertEquals( 'Travis CI Test 2', $router->getName() );
//            $this->assertEquals( 'citest2', $router->shortname() );
//            $this->assertEquals( '192.0.2.20', $router->getRouterId() );
//            $this->assertEquals( '192.0.2.21', $router->peeringIp() );
//            $this->assertEquals( '65545', $router->getAsn() );
//            $this->assertEquals( '2', $router->getSoftware() );
//            $this->assertEquals( '192.0.2.10', $router->getMgmtHost() );
//            $this->assertEquals( '0', $router->getApiType() );
//            $this->assertEquals( 'https://api2.example.com', $router->getApi() );
//            $this->assertEquals( '1', $router->getLgAccess() );
//            $this->assertEquals( false, $router->getQuarantine() );
//            $this->assertEquals( false, $router->getBgpLc() );
//            $this->assertEquals( false, $router->getSkipMd5() );
//            $this->assertEquals( 'api/v4/router/as112/bird/standard', $router->getTemplate() );
//
//            // 9. edit again and check all checkboxes and submit
//            $browser->visit( '/router/edit/' .  $router->getId() )
//                ->assertSee( 'Handle' );
//
//            $browser->check('quarantine')
//                ->check('bgp_lc')
//                ->check('skip_md5')
//                ->press('Save Changes')
//                ->assertPathIs('/router/list');
//
//
//            // 10. verify checkbox bool elements in database are all true
//            D2EM::refresh( $router );
//
//            $this->assertEquals( true, $router->getQuarantine() );
//            $this->assertEquals( true, $router->getBgpLc() );
//            $this->assertEquals( true, $router->getSkipMd5() );
//
//            // 11. delete the router in the UI and verify via success message text and location
//            $browser->visit( '/router/list/' )
//                ->press('#delete-router-' . $router->getId() )
//                ->waitForText( 'Do you want to delete this router' )
//                ->press('Confirm' );
//
//            $browser->assertSee( 'The router has been successfully deleted.' );
//
//            // 12. do a D2EM findOneBy and verify false/null
//            $this->assertEquals( null, D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => 'dusk-ci-test' ] ) );
        });

    }
}
