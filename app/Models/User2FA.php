<?php

namespace IXP\Models;

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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IXP\Models\User2FA
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User2FA newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User2FA newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User2FA query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property int $enabled
 * @property string|null $secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|User2FA whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User2FA whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User2FA whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User2FA whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User2FA whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User2FA whereUserId($value)
 * @property-read \IXP\Models\User $user
 */
class User2FA extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_2fa';

    /**
     * Get the physical interface associated with the core interface.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id' );
    }
}
