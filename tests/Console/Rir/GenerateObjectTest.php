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

namespace Tests\Console\Rir;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Cache;
use IXP\Mail\Raw;
use Tests\TestCase;

class GenerateObjectTest extends TestCase
{
    public function testCannotUseBothUpdateMethods()
    {
        $this->artisan('rir:generate-object as-set-ixp-rs --send-email --update-ripe-db')
            ->expectsOutput( "Cannot update RIPE database and send email at the same time." )
            ->assertExitCode(1)
        ;
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testPrint()
    {
        $rirObject = 'as-set-ixp-rs';
        $cacheKey = 'rir-object-' . $rirObject;

        Cache::store('file')->forget($cacheKey);
        $this->assertNull(Cache::store('file')->get($cacheKey));

        $this->artisan('rir:generate-object as-set-ixp-rs')
            ->expectsOutputToContain('as-set:         AS-SET-IXP-RS')
        ;
    }

    public function testEmailNoConfig()
    {
        $rirObject = 'as-set-ixp-rs';
        $cacheKey = 'rir-object-' . $rirObject;

        Cache::store('file')->forget($cacheKey);
        $this->assertNull(Cache::store('file')->get($cacheKey));

        config()->set('ixp_api.rir.email.to', null);
        $this->artisan('rir:generate-object as-set-ixp-rs --send-email')
            ->expectsOutputToContain( "Please specify the TO email address" );
    }

    public function testEmailBadEmail()
    {
        $rirObject = 'as-set-ixp-rs';
        $cacheKey = 'rir-object-' . $rirObject;

        Cache::store('file')->forget($cacheKey);
        $this->assertNull(Cache::store('file')->get($cacheKey));

        $this->artisan('rir:generate-object as-set-ixp-rs --send-email --to=bademail')
            ->expectsOutputToContain( "Invalid to email address: bademail" );
    }

    public function testEmailUpdateFromCliOpts()
    {
        \Mail::fake();

        $rirObject = 'as-set-ixp-rs';
        $cacheKey = 'rir-object-' . $rirObject;

        Cache::store('file')->forget($cacheKey);
        $this->assertNull(Cache::store('file')->get($cacheKey));

        $this->artisan('rir:generate-object as-set-ixp-rs --send-email --to=recipient@ixp.local --from=sender@ixp.local')
            ->expectsOutput("Email sent.")
        ;

        \Mail::assertSent(Raw::class, function (Mailable $mail) {
            return $mail->hasTo('recipient@ixp.local') &&
                $mail->hasFrom('sender@ixp.local') &&
                $mail->subject('Changes to as-set-ixp-rs via IXP Manager') &&
                $mail->assertSeeInText('as-set:         AS-SET-IXP-RS');
        });
    }

    public function testEmailForce()
    {
        \Mail::fake();

        $rirObject = 'as-set-ixp-rs';
        $cacheKey = 'rir-object-' . $rirObject;

        Cache::store('file')->forget($cacheKey);
        $this->assertNull(Cache::store('file')->get($cacheKey));

        $this->artisan('rir:generate-object as-set-ixp-rs --send-email --to=recipient@ixp.local --from=sender@ixp.local')
            ->expectsOutput("Email sent.")
        ;

        $this->artisan('rir:generate-object as-set-ixp-rs --send-email --to=recipient@ixp.local --from=sender@ixp.local')
            ->expectsOutput("Generated RIR object is identical to cached version, use --force to update anyway.")
        ;

        $this->artisan('rir:generate-object as-set-ixp-rs --send-email --to=recipient@ixp.local --from=sender@ixp.local --force')
            ->expectsOutput("Email sent.")
        ;
    }

    public function testEmailUpdateFromConfig()
    {
        \Mail::fake();

        $rirObject = 'as-set-ixp-rs';
        $cacheKey = 'rir-object-' . $rirObject;

        Cache::store('file')->forget($cacheKey);
        $this->assertNull(Cache::store('file')->get($cacheKey));

        config()->set('ixp_api.rir.email.to', 'rir-recipient@ixp.local');
        config()->set('ixp_api.rir.email.from', 'rir-sender@ixp.local');

        $this->artisan('rir:generate-object as-set-ixp-rs --send-email ')
            ->expectsOutput("Email sent.")
        ;

        \Mail::assertSent(function (Mailable $mail) {
            return $mail->hasTo('rir-recipient@ixp.local') &&
                $mail->hasFrom('rir-sender@ixp.local') &&
                $mail->subject('Changes to as-set-ixp-rs via IXP Manager') &&
                $mail->assertSeeInText('as-set:         AS-SET-IXP-RS');
        });
    }

    public function testEmailUpdateUseDefaultSender()
    {
        \Mail::fake();

        $rirObject = 'as-set-ixp-rs';
        $cacheKey = 'rir-object-' . $rirObject;

        Cache::store('file')->forget($cacheKey);
        $this->assertNull(Cache::store('file')->get($cacheKey));

        config()->set('ixp_api.rir.email.to', null);
        config()->set('ixp_api.rir.email.from', null);
        config()->set('mail.from.address', 'fromaddress@ixp.local');

        $this->artisan('rir:generate-object as-set-ixp-rs --send-email --to=recipient@ixp.local')
            ->expectsOutput("Email sent.")
        ;

        \Mail::assertSent(Raw::class, function (Mailable $mail) {
            return $mail->hasTo('recipient@ixp.local') &&
                $mail->hasFrom('fromaddress@ixp.local') &&
                $mail->subject('Changes to as-set-ixp-rs via IXP Manager') &&
                $mail->assertSeeInText('as-set:         AS-SET-IXP-RS');
        });
    }

    public function testUpdateRipeDb()
    {
        config()->set('ixp_api.rir.ripe_api_key', 'fakeapikey' );
        \Http::fake(
            ['https://rest.db.ripe.net/RIPE/as-set/AS-SET-IXP-RS' => \Http::response('[]', 200)]
        );

        $rirObject = 'as-set-ixp-rs';
        $cacheKey = 'rir-object-' . $rirObject;

        Cache::store('file')->forget($cacheKey);
        $this->assertNull(Cache::store('file')->get($cacheKey));

        $this->artisan('rir:generate-object as-set-ixp-rs --update-ripe-db')
            ->expectsOutput("RIPE DB Updated")
        ;
    }

    public function testUpdateRipeDbForce()
    {
        config()->set('ixp_api.rir.ripe_api_key', 'fakeapikey' );
        \Http::fake(
            ['https://rest.db.ripe.net/RIPE/as-set/AS-SET-IXP-RS' => \Http::response('[]', 200)]
        );

        $rirObject = 'as-set-ixp-rs';
        $cacheKey = 'rir-object-' . $rirObject;

        Cache::store('file')->forget($cacheKey);
        $this->assertNull(Cache::store('file')->get($cacheKey));

        $this->artisan('rir:generate-object as-set-ixp-rs --update-ripe-db')
            ->expectsOutput("RIPE DB Updated")
        ;

        $this->artisan('rir:generate-object as-set-ixp-rs --update-ripe-db')
            ->expectsOutput("Generated RIR object is identical to cached version, use --force to update anyway.")
        ;

        $this->artisan('rir:generate-object as-set-ixp-rs --update-ripe-db --force')
            ->expectsOutput("RIPE DB Updated")
        ;
    }


    public function testUpdateRipeDbError()
    {
        config()->set('ixp_api.rir.ripe_api_key', 'fakeapikey' );
        \Http::fake( [
            'https://rest.db.ripe.net/RIPE/as-set/AS-SET-IXP-RS' => \Http::response(file_get_contents("data/ci/known-good/ripedb-api-error.json"), 401 )
        ] );

        $rirObject = 'as-set-ixp-rs';
        $cacheKey = 'rir-object-' . $rirObject;

        Cache::store('file')->forget($cacheKey);
        $this->assertNull(Cache::store('file')->get($cacheKey));

        $this->artisan('rir:generate-object as-set-ixp-rs --update-ripe-db')
            ->expectsOutputToContain("[401] Unrecognized source: INVALID_SOURCE")
        ;
    }
}