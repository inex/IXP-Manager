<?php

namespace IXP\Models;

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

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo
};
use Eloquent;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $token_identifier
 * @property string|null $token_hash
 * @property string|null $api_key
 * @property Carbon $expires
 * @property string|null $allowed_ips
 * @property Carbon|null $last_seen_at
 * @property string|null $last_seen_from
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \IXP\Models\User $user
 * @method static Builder<static>|ApiKey newModelQuery()
 * @method static Builder<static>|ApiKey newQuery()
 * @method static Builder<static>|ApiKey query()
 * @method static Builder<static>|ApiKey whereAllowedIps($value)
 * @method static Builder<static>|ApiKey whereApiKey($value)
 * @method static Builder<static>|ApiKey whereCreatedAt($value)
 * @method static Builder<static>|ApiKey whereDescription($value)
 * @method static Builder<static>|ApiKey whereExpires($value)
 * @method static Builder<static>|ApiKey whereId($value)
 * @method static Builder<static>|ApiKey whereLastSeenAt($value)
 * @method static Builder<static>|ApiKey whereLastSeenFrom($value)
 * @method static Builder<static>|ApiKey whereTokenHash($value)
 * @method static Builder<static>|ApiKey whereTokenIdentifier($value)
 * @method static Builder<static>|ApiKey whereUpdatedAt($value)
 * @method static Builder<static>|ApiKey whereUserId($value)
 * @property string $apiKey
 * @property string|null $allowedIPs
 * @property string|null $lastseenAt
 * @property string|null $lastseenFrom
 * @method static Builder<static>|ApiKey whereAllowedIPs($value)
 * @method static Builder<static>|ApiKey whereLastseenAt($value)
 * @method static Builder<static>|ApiKey whereLastseenFrom($value)
 * @mixin Eloquent
 */
class ApiKey extends Model
{
    const PREFIX = 'ixpm_';


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'expires'      => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }


    /**
     * Get the user
     *
     * @return BelongsTo<User, ApiKey>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id' );
    }

    /**
     * Record last_seen_at and last_seen_from when API key is used for authentication
     */
    public function updateLastSeen(): bool
    {
        $this->last_seen_at   = now();
        $this->last_seen_from = ixp_get_client_ip();
        return $this->save();
    }
}