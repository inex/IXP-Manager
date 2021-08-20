<?php

namespace IXP\Models;

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

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo
};

/**
 * IXP\Models\ApiKey
 *
 * @property int $id
 * @property int $user_id
 * @property string $apiKey
 * @property string|null $expires
 * @property string|null $allowedIPs
 * @property string|null $lastseenAt
 * @property string|null $lastseenFrom
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\User $user
 * @method static Builder|ApiKey newModelQuery()
 * @method static Builder|ApiKey newQuery()
 * @method static Builder|ApiKey query()
 * @method static Builder|ApiKey whereAllowedIPs($value)
 * @method static Builder|ApiKey whereApiKey($value)
 * @method static Builder|ApiKey whereCreatedAt($value)
 * @method static Builder|ApiKey whereDescription($value)
 * @method static Builder|ApiKey whereExpires($value)
 * @method static Builder|ApiKey whereId($value)
 * @method static Builder|ApiKey whereLastseenAt($value)
 * @method static Builder|ApiKey whereLastseenFrom($value)
 * @method static Builder|ApiKey whereUpdatedAt($value)
 * @method static Builder|ApiKey whereUserId($value)
 * @mixin \Eloquent
 */
class ApiKey extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'apiKey',
        'expires',
        'allowedIPs',
        'created',
        'lastseenAt',
        'lastseenFrom',
        'description'
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id' );
    }
}