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

namespace IXP\Console\Commands\User;

use Illuminate\Support\Facades\Log;
use IXP\Console\Commands\Command;
use IXP\Models\User;

/**
 * Artisan command to reset the password lockout state (failed attempt count and login throttle) for a user
 */
class ResetPasswordLockout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-password-lockout
                        {search : Username or email fragment to search for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will reset the password lockout state (failed attempts and login throttle) for a user.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $search = $this->argument('search');
        if (!$search) {
            $this->error("No search term provided");
            return 1;
        }

        $u = $this->usersViaUsernameOrEmail( $search );
        if (count($u) === 0) {
            $this->error("No matching users found");
            return 2;
        }

        $this->table(
            ['ID', 'Name', 'Username', 'Email', 'Customers', 'Privs'],
            $u
        );

        $uid = $this->anticipate( 'Enter ID to reset password lockout for', array_keys( $u ));

        if (!array_key_exists($uid, $u)) {
            $this->warn("ID {$uid} is not a valid option");
            return 3;
        }

        $user = User::find($uid);
        if (is_null($user)) {
            $this->warn("Could not find user with ID {$uid}");
            return 4;
        }

        $user->resetLockoutState();
        Log::notice( "User password lockout state reset via console command for " . $user->username );
        $this->line("Password lockout state reset for user with ID {$uid}");

        return 0;
    }
}
