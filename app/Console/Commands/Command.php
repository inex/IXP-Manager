<?php

namespace IXP\Console\Commands;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Database\Eloquent\Builder;
use IXP\Models\Customer;
use IXP\Models\User;

use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends \Illuminate\Console\Command
{
     /**
      * Returns true if verbosity is EXACTLY: VERBOSITY_QUIET
      *
      * @return bool
      */
     protected function isVerbosityQuiet(): bool
     {
         return $this->getOutput()->getVerbosity() === OutputInterface::VERBOSITY_QUIET;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_NORMAL
      *
      * @return bool
      */
     protected function isVerbosityNormal(): bool
     {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_VERBOSE
      *
      * @return bool
      */
     protected function isVerbosityVerbose(): bool
     {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_VERY_VERBOSE
      *
      * @return bool
      */
     protected function isVerbosityVeryVerbose(): bool
     {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_DEBUG
      *
      * @return bool
      */
     protected function isVerbosityDebug(): bool
     {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG;
     }

    /**
     * Returns the list of customers that the keyword match the ASN or the name
     *
     * @param  string|int  $search
     *
     * @return array
     */
     protected function customersViaNameOrASN( string|int $search ): array
     {
         return Customer::selectRaw( 'id,name,autsys' )
             ->when( is_numeric( $search ), function( Builder $q ) use( $search ) {
                 return $q->where( 'autsys',  $search  );
            })
             ->orWhere( 'name', 'LIKE', '%' . $search . '%' )
             ->orderBy( 'id' )->get()->toArray();
     }

    /**
     * Returns the list of users that the keyword match the username or the email
     *
     * @param  string  $search
     *
     * @return array
     */
    protected function usersViaUsernameOrEmail( string $search ): array
    {
        return User::selectRaw(
            'user.id as id, user.name as name, 
                      user.username as username, user.email as email,
                      GROUP_CONCAT( c.name SEPARATOR "\n" ) AS cname, 
                      GROUP_CONCAT( 
                      CASE 
                        WHEN c2u.privs = 3 THEN "SU"
                        WHEN c2u.privs = 2 THEN "CA"
                        WHEN c2u.privs = 1 THEN "CU"
                      END 
                      SEPARATOR "\n" ) 
                      AS privs' )
            ->leftJoin( 'customer_to_users AS c2u', 'c2u.user_id', 'user.id' )
            ->leftJoin( 'cust AS c', 'c.id', 'c2u.customer_id' )
            ->where( 'username',  'LIKE', '%' . $search . '%'  )
            ->orWhere( 'email', 'LIKE', '%' . $search . '%' )
            ->groupBy( 'id', 'name', 'username', 'email' )
            ->orderBy( 'id' )->get()->keyBy( 'id' )->toArray();
    }

    /**
     * Validate an input.
     *
     * @param  mixed   $method
     * @param  array   $rules
     *
     * @return mixed
     */
    protected function validate_cmd( mixed $method, array $rules ): mixed
    {
        $value = $method();
        $validate = $this->validateInput( $rules, $value );

        if( $validate !== true ) {
            $this->warn( $validate );
            $value = $this->validate_cmd( $method, $rules );
        }
        return $value;
    }

    /**
     * @param array     $rules
     * @param mixed     $value
     *
     * @return bool|string
     */
    protected function validateInput( array $rules, mixed $value ): bool|string
    {
        $validator = \Validator::make( [key( $rules ) => $value], $rules );

        if ($validator->fails()) {
            return $validator->errors()->first( key( $rules ) );
        }
        return true;
    }
}