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
use Illuminate\Database\Eloquent\Builder;
use IXP\Events\User\UserCreated as UserCreatedEvent;
use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\User;

/**
 * Artisan command to create a new user
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yanny@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Console\Commands\Customer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class Create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "user:create
                        {--e|email= : User's email}
                        {--na|name= : User's name}
                        {--u|username= : User's username}
                        {--m|mobile= : User's mobile number}
                        {--c|custid= : User's customer}
                        {--pr|priv= : User's privilege}
                        {--pa|password= : User's password}
                        {--s|send-welcome-email : Should we send the welcome email to the user?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user.';

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = [
        'email'             => 'required|email|unique:user,email',
        'name'              => 'required|string|max:255',
        'username'          => 'required|string|min:3|max:255|regex:/^[a-z0-9\-_\.]{3,255}$/|unique:user,username',
        'mobile'            => 'nullable|string|max:50',
        'custid'            => 'required|integer|exists:cust,id',
        'priv'              => 'required|integer|in:' . User::AUTH_SUPERUSER . ',' . User::AUTH_CUSTADMIN  . ',' . User::AUTH_CUSTUSER ,
        'password'          => 'required|string|min:8|max:255',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // getting all the options in an array
        $options = [
            'email'     =>  $this->option('email'     ),
            'name'      =>  $this->option('name'      ),
            'username'  =>  $this->option('username'  ),
            'mobile'    =>  $this->option('mobile'    ),
            'custid'    =>  $this->option('custid'    ),
            'priv'      =>  $this->option('priv'      ),
            'password'  =>  $this->option('password'  ),
        ];

        $sendEmail =  $this->option('send-welcome-email');

        foreach( $options as $option => $value ){
            ${$option} = $value;

            if( $option !== 'send-welcome-email' ){
                $validator = \Validator::make( [ $option => $value], [$option => $this->rules[ $option ] ] );
                if( $value && $validator->fails() ) {
                    $this->error( $validator->errors()->first( $option ) );
                    return 0;
                }

                if( $option === 'priv' ) {
                    $this->info('[ 1 => member, read only; 2 => member, read/write; 3 => super admin (careful!!)');
                }

                if( !$value ) {
                    if( $option === 'custid' ){
                        $result = $this->ask('Search Customer by ASN or Name' );

                        $this->table(
                            ['ID', 'Name', 'ASN'],
                            $this->customersViaNameOrASN( $result )
                        );
                    }

                    ${$option} = $this->validate_cmd( function() use( $option ) {
                        if( $option === 'password' ){
                            return $this->secret('Enter '. $option);
                        }
                        return $this->ask('Enter ' . $option);
                    }, [ $option => $this->rules[ $option ] ]);
                }
            }
        }

        if( !$sendEmail && $this->confirm('Do you want to send the welcome email to the used?', false ) ) {
            $sendEmail = true;
        }

        // Creating the User object
        $user = new User;
        $user->creator          = 'artisan';
        $user->password         = Hash::make( $password );
        $user->name             = $name;
        $user->authorisedMobile = $mobile;
        $user->username         = strtolower( $username );
        $user->email            = strtolower( $email );
        $user->disabled         = false;
        $user->privs            = $priv;
        $user->custid           = $custid;
        $user->save();

        // Creating the CustomerToUser object
        $c2u = new CustomerToUser;
        $c2u->customer_id   = $user->custid;
        $c2u->user_id       = $user->id;
        $c2u->privs         = $priv;
        $c2u->extra_attributes = [ "created_by" => [ "type" => "artisan" , "user_id" => $user->id ] ];
        $c2u->save();

        if( $sendEmail ){
            // Send Email related to the event
            event( new UserCreatedEvent( $user ) );
        }

        $this->info( 'User created.' );
        return 0;
    }
}
