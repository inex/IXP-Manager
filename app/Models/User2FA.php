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

namespace IXP\Models;

use Illuminate\Database\Eloquent\{
    Model,
    Relations\BelongsTo
};

/**
 * IXP\Models\User2FA
 *
 * @property int $id
 * @property int $user_id
 * @property bool $enabled
 * @property string|null $secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User2FA newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User2FA newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User2FA query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User2FA whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User2FA whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User2FA whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User2FA whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User2FA whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User2FA whereUserId($value)
 * @mixin \Eloquent
 */
class User2FA extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_2fa';

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public static function setupForUser( User $user, string $secret ): User2FA
    {
        $user2fa = new self();
        $user2fa->user_id = $user->id;
        $user2fa->secret = $secret;
        $user2fa->enabled = false;
        $user2fa->save();
        return $user2fa;
    }

    /**
     * Return the user to which this 2FA record belongs
     *
     * @return BelongsTo<User, User2FA>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id' );
    }

    public function enable(): bool
    {
        $this->enabled = true;
        return $this->save();
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}