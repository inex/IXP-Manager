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

use IXP\Traits\Observable;
use Illuminate\Database\Eloquent\{
    Model,
    Relations\BelongsTo
};

/**
 * IXP\Models\AppPassword
 *
 * @property int $id
 * @property int $user_id
 * @property string $password
 * @property string|null $expires
 * @property string|null $lastseenAt
 * @property string|null $lastseenFrom
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\User $user
 * @property string|null $salt
 * @property string|null $last_seen_at
 * @property string|null $last_seen_from
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword whereLastSeenFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword whereSalt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppPassword whereUserId($value)
 * @mixin \Eloquent
 */
class AppPassword extends Model
{
    
    use Observable;

    /**
     * Get the user
     *
     * @return BelongsTo<User, AppPassword>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id' );
    }
    
    
    
    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Model $model
     *
     * @return string
     */
    public static function logSubject( Model $model ): string
    {
        return sprintf(
            "Application-Specific Password [id:%d] belonging to user [id:%d] '%s'",
            $model->id,
            $model->user->id,
            $model->user->username
        );
    }
}
