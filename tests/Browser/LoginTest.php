<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/auth/login')
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( 'submit' )
                ->assertPathIs( '/admin' );
        });
    }
}
