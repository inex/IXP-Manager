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

use IXP\Console\Commands\Command;
use IXP\Models\User;

/**
 * Artisan command to reset the 2FA lockout state for a user
 */
class Reset2FALockout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-2fa-lockout
                        {search : Username or email fragment to search for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will reset the 2FA lockout state for a user.';

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

        $uid = $this->anticipate( 'Enter ID to reset 2FA lockout for', array_keys( $u ));

        if (!array_key_exists($uid, $u)) {
            $this->warn("ID {$uid} is not a valid option");
            return 3;
        }

        $user = User::find($uid);
        if (is_null($user)) {
            $this->warn("Could not find user with ID {$uid}");
            return 4;
        }

        if (is_null($user->user2FA)) {
            $this->warn("No 2FA configured for user with ID {$uid}");
            return 5;
        }

        $user->user2FA->resetLockoutState();
        $this->line("2FA lockout state reset for user with ID {$uid}");

        return 0;
    }
}
