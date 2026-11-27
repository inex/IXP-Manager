<?php

namespace IXP\Models;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Links an external system's reference to what it created here.
 *
 * @property int         $id
 * @property string      $reference
 * @property string      $model_type
 * @property int         $model_id
 * @property int|null    $created_by
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Models
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ProvisioningReference extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'provisioning_references';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reference',
        'model_type',
        'model_id',
        'created_by',
    ];

    /**
     * The thing this reference produced.
     *
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The customer a previous call with this reference produced, if any.
     *
     * @param  string|null  $reference
     *
     * @return Customer|null
     */
    public static function customerFor( ?string $reference ): ?Customer
    {
        if( $reference === null || $reference === '' ) {
            return null;
        }

        $ref = self::where( 'reference', $reference )
            ->where( 'model_type', Customer::class )
            ->first();

        return $ref ? Customer::find( $ref->model_id ) : null;
    }

    /**
     * The answer to a repeated onboarding request: what the first call with this reference
     * created, as far as it still exists.
     *
     * The member is always there - without it there is nothing to replay and null is returned.
     * `user` and `connection` are present when the first call created them and they still
     * exist. A caller retrying after a timeout never saw the first response, so the replay is
     * its only way to learn those ids: there is no endpoint which lists a member's users.
     *
     * References recorded before user and connection were recorded carry the member only.
     *
     * @param  string|null  $reference
     *
     * @return array|null
     */
    public static function replay( ?string $reference ): ?array
    {
        if( !( $cust = self::customerFor( $reference ) ) ) {
            return null;
        }

        $out = [
            'created'   => false,
            'reference' => $reference,
            'member'    => [ 'id' => $cust->id, 'shortname' => $cust->shortname ],
        ];

        $ids = self::where( 'reference', $reference )
            ->whereIn( 'model_type', [ User::class, VirtualInterface::class ] )
            ->pluck( 'model_id', 'model_type' );

        if( isset( $ids[ User::class ] ) && ( $user = User::find( $ids[ User::class ] ) ) ) {
            $out[ 'user' ] = [ 'id' => $user->id, 'username' => $user->username ];
        }

        if( isset( $ids[ VirtualInterface::class ] ) && ( $vi = VirtualInterface::find( $ids[ VirtualInterface::class ] ) ) ) {
            $out[ 'connection' ] = [ 'id' => $vi->id ];
        }

        return $out;
    }
}
