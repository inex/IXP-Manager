<?php

namespace Tests\Browser;

use D2EM;

use Entities\User as UserEntity;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RouterControllerTest extends DuskTestCase
{
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

            $browser->visit( '/router/add' )
                    ->assertSee( 'Handle' );

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
                ->type( 'mgmt_host', '192.0.2.1' )
                ->select( 'api_type', 1 )
                ->type( 'api', 'https://api.example.com' )
                ->select( 'lg_access', 2 )
                ->check('quarantine')
                ->check('bgp_lc')
                ->check('skip_md5')
                ->type( 'template', 'api/v4/router/server/bird/standard' )
                ->press('Add Router')
                ->assertPathIs('/router/list');
        });
    }
}
