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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Tests\Listeners\Auth;

use Auth;
use IXP\Listeners\Auth\Google2FALoginSucceeded;
use IXP\Models\UserRememberToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Events\LoginSucceeded;
use Tests\TestCase;

class Google2faLoginSucceededTest extends TestCase
{

    public function test_records_2fa_complete_when_recaller_cookie_present(): void
    {
        $tokenValue = 'sample-recaller-token';
        $urt = new UserRememberToken();
        $urt->user()->associate($this->getSuperUser());
        $urt->token = $tokenValue;
        $urt->save();

        // Recaller string format: "id|token|password_hash"
        $recallerString = "1|{$tokenValue}|password-hash-normally-here";

        // Inject a fake request for Laravel to use
        $request = Request::create('/', 'GET', [], [
            Auth::getRecallerName() => $recallerString,
        ]);
        $this->app->instance('request', $request);

        new Google2FALoginSucceeded()->handle(new LoginSucceeded($this->getSuperUser()));

        $this->assertTrue($urt->fresh()->is_2fa_complete);
    }

}