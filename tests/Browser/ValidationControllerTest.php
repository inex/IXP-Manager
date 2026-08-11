<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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
declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Support\Facades\Cache;
use IXP\Contracts\Validation\ValidationRunner;
use IXP\Utils\Validation\Dto\JobState;
use IXP\Utils\Validation\ValidationRunnerFactory;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * This test is _weird_.
 *
 * The browser navigates to the ./artisan serve server and starts validations. That endpoint waits until all
 * validation processes finish, and stays open. The ./artisan serve server is single threaded worker, so it
 * is only responding to one request at a time, and if one blocks, nothing else can happen..
 *
 * If it starts a task which _depends_ on that server being available well forget about it!
 * ... and that's how we added timeouts to the Security HTTP self test.
 *
 * Additionally, Herd's PHP has a max execution time of 30s for PHP scripts, so watch out for that also.
 * (ConcurrentJobRunner's usleep will appear to throw.). So it's a good idea to have validations
 * time out before 30s.
 *
 * Wanna know what else?
 * ./artisan serve supports multiple threads, but that feature doesn't work with .env reloading.
 *
 *
 */
class ValidationControllerTest extends DuskTestCase
{
    /**
     * Test that validations can be started, and complete eventually.
     *
     * @return void
     *
     * @throws
     */
    public function testRunValidations(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visit("admin/validation/start")
                ->waitForText("Start System Validation");

            // This is going to block until every validation completes.
            $browser->click("#validation-start");
            // For ^ to return, the request it started needs to end, which means all validators have finished,
            // have timed out, or the request was killed itself for taking too long (herd's max execution time?)

            $browser->waitForText("System Validation");

            $validationsUuid = array_last(explode("/", $browser->driver->getCurrentURL()));

            $jobState = Cache::driver("file")->get(JobState::getCacheKey($validationsUuid));
            $this->assertInstanceOf(JobState::class, $jobState);
            $this->assertEquals($validationsUuid, $jobState->jobId);

            $factory = app(ValidationRunnerFactory::class);
            $runners = $factory->getRunners();

            // Does our jobstate have the right number of runners?
            $this->assertCount(count($runners), $jobState->runners);
            $this->assertNotNull($jobState->finishedAt);
            $this->assertEquals(100.0, $jobState->progress);
        } );
    }
}