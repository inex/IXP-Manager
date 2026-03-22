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
use IXP\Events\User\UserCreated as UserCreatedEvent;
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
                        {--email= : User's email}
                        {--name= : User's name}
                        {--username= : User's username}
                        {--mobile= : User's mobile number}
                        {--custid= : User's customer}
                        {--priv= : User's privilege}
                        {--password= : User's password}
                        {--send-welcome-email : Should we send the welcome email to the user?}";

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
            //${$option} = $value;

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
                        while (! $result = $this->ask('Search Customer by ASN or Name' ) ) {}

                        $this->table(
                            ['ID', 'Name', 'ASN'],
                            $this->customersViaNameOrASN( $result )
                        );
                    }

                    $options[$option] = $this->recurringAskWithValidation( function() use( $option ) {
                        if( $option === 'password' ){
                            return $this->secret('Enter '. $option);
                        }
                        return $this->ask('Enter ' . $option);
                    }, $option, $this->rules[ $option ] );
                }
            }
        }

        if( !$sendEmail && $this->confirm('Do you want to send the welcome email to the user?', false ) ) {
            $sendEmail = true;
        }

        // Creating the User object
        $user = new User;
        $user->creator          = 'artisan';
        $user->password         = Hash::make( $options['password'] );
        $user->name             = $options['name'];
        $user->authorisedMobile = $options['mobile'];
        $user->username         = strtolower( $options['username'] );
        $user->email            = strtolower( $options['email'] );
        $user->disabled         = false;
        $user->privs            = (int) $options['priv'];
        $user->custid           = (int) $options['custid'];
        $user->save();

        // Creating the CustomerToUser object
        $c2u = new CustomerToUser;
        $c2u->customer_id   = $user->custid;
        $c2u->user_id       = $user->id;
        $c2u->privs         = $user->privs;
        $c2u->extra_attributes = [ "created_by" => [ "type" => "artisan" , "user_id" => $user->id ] ];
        $c2u->save();

        if( $sendEmail ){
            // Send Email related to the event
            event( new UserCreatedEvent( $user ) );
        }

        $this->info( 'User created.' );
        return 0;
    }

    /**
     * This function assists the user in entering data until the data is valid.
     *
     * A closure $method is used to prompt the user for input.
     * $option is the name of the input variable.
     * $rules is the validation rules to apply to the input.
     *
     * If the user's input passes validation, it is returned to the caller.
     * If not, the function recurses to begin the cycle again.
     *
     * @param  \Closure  $method
     * @param  string    $option
     * @param  string    $rules
     *
     * @return mixed
     */
    private function recurringAskWithValidation( \Closure $method, string $option, string $rules ): mixed
    {
        $value = $method();
        $validate = $this->validateInput( $option, $rules, $value );

        if( $validate !== true ) {
            $this->warn( $validate );
            $value = $this->recurringAskWithValidation( $method, $option, $rules );
        }
        return $value;
    }

    /**
     * This validator function takes a value (called $name), and attempts to validate
     * it against the provided rules. If the validation fails, it returns the error message.
     * If successful, true is returned.
     * @param string     $name
     * @param string     $rules
     * @param mixed      $value
     *
     * @return bool|string
     */
    private function validateInput( string $name, string $rules, mixed $value ): bool|string
    {
        $validator = \Validator::make( [ $name => $value ], [ $name => $rules ] );

        if( $validator->fails() ) {
            return $validator->errors()->first( $name );
        }

        return true;
    }
}
