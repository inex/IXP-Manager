<?php

namespace Tests\Browser;

use D2EM;

use Entities\Router as RouterEntity;
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

            /** @var RouterEntity $router */
            $router = D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => 'dusk-ci-test' ] );

            // 2. test added data in database against expected values
            $this->assertInstanceOf( RouterEntity::class, $router );
            $this->assertEquals( 'Travis CI Test', $router->getName() );
            // repeat for all elements

            // 3. browse to edit router object: $browser->visit( '/router/edit/' . $router->getId() )
            // 4. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...

            // 5. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            // 6. repeat database load and database object check for new values (repeat 2)

            // 7. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            // 8. submit with no changes and verify no changes in database

            // 9. edit again and check all checkboxes and submit
            // 10. verify checkbox bool elements in database are all true

            // 11. delete the router in the UI and verify via success message text and location
            // 12. do a D2EM findOneBy and verify false/null 
        });
    }
}
