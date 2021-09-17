<?php

namespace IXP\Models\Aggregators;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
use Hash, Log;

use Illuminate\Support\Str;

use IXP\Events\User\{
    UserAddedToCustomer as UserAddedToCustomerEvent,
    UserCreated as UserCreatedEvent
};

use IXP\Models\{
    Customer,
    CustomerToUser,
    User,
    UserLoginHistory
};

/**
 * IXP\Models\Aggregators\UserAggregator
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $username
 * @property string|null $password
 * @property string|null $email
 * @property string|null $authorisedMobile
 * @property int|null $uid
 * @property int|null $privs
 * @property int|null $disabled
 * @property int|null $lastupdatedby
 * @property string|null $creator
 * @property string|null $name
 * @property int|null $peeringdb_id
 * @property array|null $extra_attributes
 * @property array|null $prefs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ApiKey[] $apiKeys
 * @property-read int|null $api_keys_count
 * @property-read Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|CustomerToUser[] $customerToUser
 * @property-read int|null $customer_to_user_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Customer[] $customers
 * @property-read int|null $customers_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \IXP\Models\User2FA|null $user2FA
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\UserRememberToken[] $userRememberTokens
 * @property-read int|null $user_remember_tokens_count
 * @method static Builder|User activeOnly()
 * @method static Builder|User byPrivs(?int $priv = null)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereAuthorisedMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereDisabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereExtraAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereLastupdatedby($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator wherePeeringdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator wherePrefs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator wherePrivs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAggregator whereUsername($value)
 * @mixin \Eloquent
 */
class UserAggregator extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Find or create a user from PeeringDB information.
     *
     *
     *  +pdbuser: array:8 [▼
     *    "family_name" => "Bloggs"
     *    "email" => "ixpmanager@example.com"
     *    "name" => "Joe Bloggs"
     *    "verified_user" => true
     *    "verified_email" => true
     *    "networks" => array:2 [▼
     *      0 => array:4 [▼
     *        "perms" => 1
     *        "id" => 888
     *        "name" => "INEX Route Collectors"
     *        "asn" => 65501
     *      ]
     *      1 => array:4 [▼
     *        "perms" => 1
     *        "id" => 777
     *        "name" => "INEX Route Servers"
     *        "asn" => 65500
     *      ]
     *    ]
     *    "id" => 666
     *    "given_name" => "Joe"
     *  ]
     * }
     *
     * @param array $pdbuser
     *
     * @return array
     */
    public static function findOrCreateFromPeeringDb( array $pdbuser ): array
    {
        // results to pass back:
        $result = [
            'user'         => null,
            'added_to'     => [],
            'removed_from' => [],
        ];

        // let's make sure we have a reason to do any work before we start:
        $asns = [];
        foreach( $pdbuser[ 'networks' ] as $nw ) {
            if( is_numeric( $nw[ 'asn' ] ) && (int)$nw[ 'asn' ] > 0 ) {
                $asns[] = (int)$nw[ 'asn' ];
            }
        }

        if( !count( $asns ) ) {
            Log::info( 'PeeringDB OAuth: no valid affiliated networks for ' . $pdbuser[ 'name' ] . '/' . $pdbuser[ 'email' ] );
            return $result;
        }


        if( Customer::whereIn( 'autsys', $asns )->count() === 0 ) {
            Log::info( 'PeeringDB OAuth: no customers for attempted login from ' . $pdbuser[ 'name' ] . '/' . $pdbuser[ 'email' ] . ' with networks: ' . implode( ',', $asns ) );
            return $result;
        }


        // what privilege do we use?
        $priv = isset( User::$PRIVILEGES_TEXT_NONSUPERUSER[ config( 'auth.peeringdb.privs' ) ] ) ? config( 'auth.peeringdb.privs' ) : User::AUTH_CUSTUSER;


        // if we don't have a user already, create one with unique username
        if( !( $user = User::where( 'peeringdb_id', $pdbuser['id'] )->first() ) ) {

            $un = strtolower( $pdbuser['name'] ?? 'unknownpdbuser' );
            $un = preg_replace( '/[^a-z0-9\._\-]/', '.', $un );
            $int = 0;

            do {
                $int++;
                $uname = $un . ( $int === 1 ? '' : "{$int}" );
            } while( User::where( 'username', $uname )->first() );
                $user = new User();
                $user->peeringdb_id = $pdbuser['id'];
                $user->username     = $uname;
                $user->password     = Hash::make( Str::random() );
                $user->privs        = $priv;
                $user->creator      = 'OAuth-PeeringDB';
                $user->save();

            $user_created = true;
            Log::info( 'PeeringDB OAuth: created new user ' . $user->id . '/' . $user->username . ' for PeeringDB user: ' . $pdbuser[ 'name' ] . '/' . $pdbuser[ 'email' ] );
        } else {
            $user_created = false;
            Log::info( 'PeeringDB OAuth: found existing user ' . $user->id . '/' . $user->username . ' for PeeringDB user: ' . $pdbuser[ 'name' ] . '/' . $pdbuser[ 'email' ] );
        }

        $user->name     = $pdbuser[ 'name' ];
        $user->email    = $pdbuser[ 'email' ];
        $user->save();

        $result[ 'user' ] = $user;

        // user updated or created now.
        // we still need to link to customers

        // let's start with removing any customers that are no longer in the peeringdb networks list
        foreach( $user->customerToUser as $c2u ) {
            $cust = $c2u->customer;
            $key = array_search( $cust->autsys, $asns );

            if( $key === false || ( $key && !$cust->peeringdb_oauth ) ) {
                // either user has a network that's not in the current peeringdb list of affiliated networks
                // or user has a network that (now) indicates PeeringDB OAuth should be disabled
                // then => if it came from peeringdb, remove it
                $ea = $c2u->extra_attributes;
                if( $ea && isset( $ea[ 'created_by' ][ 'type' ] ) && $ea[ 'created_by' ][ 'type' ] === 'PeeringDB' ) {
                    UserLoginHistory::where( 'customer_to_user_id', $c2u->id )->delete();
                    // if this is the user's default / last logged in as customer, reset it:
                    if( !$user->customer || $user->custid === $c2u->customer_id ) {
                        $user->custid = null;
                        $user->save();
                    }

                    $result['removed_from'][] = $c2u->customer;
                    Log::info( 'PeeringDB OAuth: removing user ' . $user->id . '/' . $user->username . ' from ' . $cust->getFormattedName() );
                    $c2u->delete();
                }
            } else {
                // we already have a link so take it out of the array
                Log::info( 'PeeringDB OAuth: user ' . $user->id . '/' . $user->username . ' already linked to AS' . $asns[ $key ] );
                unset( $asns[ $key ] );
            }
        }

        // what's left in $asns is potential new customers:
        foreach( $asns as $asn ) {
            if( $cust = Customer::where( 'autsys', $asn )->first() ) {
                Log::info( 'PeeringDB OAuth: user ' . $user->id . '/' . $user->username . ' has PeeringDB affiliation with ' . $cust->getFormattedName() );

                // is this a valid customer?
                if( !( $cust->typeFull() || $cust->typeProBono() ) || $cust->statusSuspended() || $cust->hasLeft() || !$cust->peeringdb_oauth ) {
                    Log::info( 'PeeringDB OAuth: ' . $cust->getFormattedName() . ' not a suitable IXP Manager customer for PeeringDB, skipping.' );
                    continue;
                }

                $c2u = CustomerToUser::create([
                    'customer_id'       => $cust->id,
                    'user_id'           => $user->id,
                    'privs'             => $priv,
                    'extra_attributes'  => [ "created_by" => [ "type" => "PeeringDB"  ] ],
                ]);

                $result['added_to'][] = $c2u->customer;
                Log::info( 'PeeringDB OAuth: user ' . $user->id . '/' . $user->username . ' linked with with ' . $cust->getFormattedName() );

                if( $user_created ) {
                    // should not emit any more UserCreatedEvent events
                    $user_created = false;
                    event( new UserCreatedEvent( $user ) );
                } else {
                    event( new UserAddedToCustomerEvent( $c2u ) );
                }
            }
        }

        // refresh from database
        $user->refresh();

        // do we actually have any customers after all this?
        if( !$user->customers()->count() ) {
            Log::info( 'PeeringDB OAuth: user ' . $user->id . '/' . $user->username . ' has no customers - deleting...' );

            // delete all the user's API keys
            $user->apiKeys()->delete();
            $user->delete();
            $result['user'] = null;

        } else if( $user->customer()->doesntExist() ) {
            // set a default customer if we do not have one
            Log::info( 'PeeringDB OAuth: user ' . $user->id . '/' . $user->username . ' given default customer: ' . $user->customers()->first()->getFormattedName() );
            $user->custid = $user->customers()->first()->id;
            $user->save();
        }

        return $result;
    }
}