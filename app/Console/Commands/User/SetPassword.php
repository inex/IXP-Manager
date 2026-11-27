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

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use IXP\Console\Commands\Command;
use IXP\Models\User;

/**
 * Artisan command to set the password of a user
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yanny@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Console\Commands\Customer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "user:set-password
                        {--search= : Username or email fragment to search for}
                        {--u|uid= : User's ID}
                        {--p|password= : User's password}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Set a user's password";

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws \Exception
     *
     * @psalm-return -1|0
     */
    public function handle(): int
    {
        $search     = $this->option('search' );
        $uid        = $this->option('uid' );
        $password   = $this->option('password' );

        if( ( !$search && !$uid ) || ( $search && $uid ) ){
            $this->error( "Search or UID must be set (and not both)." );
            return -1;
        }

        if( $search ){        // Display result the --search parameter
            $this->table(
                ['ID', 'Name', 'Username', 'Email', 'Customers', 'Privs'],
                $u = $this->usersViaUsernameOrEmail( $search )
            );

            $uid = $this->anticipate( 'Enter ID to change password for', array_keys( $u ));

            if (!array_key_exists($uid, $u)) {
                $this->error( "That UID was not in the list of returned options" );
                return -1;
            }
        }

        if( !$user = User::find( $uid ) ){
            $this->error( "UID does not exist !" );
            return -1;
        }

        if( !$password ){  // --password option not specified, ask for password
            $password = $this->secret( 'Password or (return to have one generated)' );
            if( $password ){ // if the user type a password
                $confirmPassword = $this->secret( 'Confirm password' );

                if( $password !== $confirmPassword ){// check if password match
                    $this->error( "The passwords does not match!" );
                    return -1;
                }
            } else {// if the user type return generate password
                $password = $this->buildPasswordRespectingPolicy();
                $this->info( "Generated password: " . $password );
            }
        }

        $pwValidator = $this->validator( $this->passwordRules(), 'password', $password );
        if ($pwValidator->fails()) {
            $this->error($pwValidator->errors()->first());
            return -1;
        }

        /** @var User $user */
        $user->password = Hash::make( $password );
        $user->resetLockoutState();
        $user->save();

        $this->info( "Password set." );

        return 0;
    }

    /**
     * Since the password policy may require a password of a certain length, we need to generate a password that respects that policy.
     */
    private function buildPasswordRespectingPolicy(): string
    {
        // How long should our new password be? Take 50% more than the minimum length using the max if its too large.
        $length = min( config( 'password_policy.maximum_length' ), (int) ceil( config( 'password_policy.minimum_length' ) * 1.5) );
        return Str::password($length);
    }

    /**
     * Return the validation rules for the command. Function instead of array property due to
     * function call - can't have expressions in property initializers
     */
    protected function passwordRules(): array
    {
        return [ 'required', 'string', Password::default() ];
    }

    protected function validator( string|array $rule, string $fieldName, mixed $value): Validator
    {
        return ValidatorFacade::make( [ $fieldName => $value ], [ $fieldName => $rule ] );
    }
}