<?php

namespace IXP\Console\Commands\User;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Support\Facades\Hash;
use IXP\Console\Commands\Command;
use IXP\Models\User;
use Illuminate\Support\Str;

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

    protected $rules = [
        'password'          => 'required|string|min:8|max:255',
        'confirm_password'  => 'required|same:password',
    ];
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $search     = $this->option('search' );
        $uid        = $this->option('uid' );
        $password   = $this->option('password' );

        if( ( !$search && !$uid ) || ( $search && $uid ) ){
            $this->error( "Search or UID must be set (and not both)." );
            return 0;
        }

        if( $search ){// Display result the --search parameter
            $this->table(
                ['ID', 'Name', 'Username', 'Email', 'Customers', 'Privs'],
                $u = $this->usersViaUsernameOrEmail( $search )
            );

            $uid = $this->anticipate( 'Enter ID to change password for', array_keys( $u ));
        }

        if( !$user = User::find( $uid ) ){
            $this->error( "UID does not exist !" );
            return 0;
        }

        $validate = $this->validateInput( [ 'password' => $this->rules[ 'password' ] ] , $password );
        if( $password && $validate !== true ){
            $this->error( $validate );
            return 0;
        }

        if( !$password ){// --password option not specified, ask for password
            $password = $this->secret( 'Password or (return to have one generated)' );
            if( $password ){// if the user type a password
                $validate = $this->validateInput( [ 'password' => $this->rules[ 'password' ] ] , $password );
                if( $validate !== true ){
                    $this->error( $validate );
                    return 0;
                }
                $confirmPassword    = $this->secret( 'Confirm password' );
                if( $password !== $confirmPassword ){// check if password match
                    $this->error( "The passwords does not match!" );
                    return 0;
                }
            } else {// if the user type return generate password
                $password = Str::random(16 );
                $this->info( "Generated password: " . $password );
            }
        }

        $user->password = Hash::make( $password );
        $user->save();

        $this->info( "Password set." );

        return 0;
    }
}