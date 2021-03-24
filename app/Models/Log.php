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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

/**
 * IXP\Models\Log
 *
 * @method static Builder|Log newModelQuery()
 * @method static Builder|Log newQuery()
 * @method static Builder|Log query()
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $user_id
 * @property string $model
 * @property int|null $model_id
 * @property string $action
 * @property string $message
 * @property mixed $models
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Log whereAction($value)
 * @method static Builder|Log whereCreatedAt($value)
 * @method static Builder|Log whereId($value)
 * @method static Builder|Log whereMessage($value)
 * @method static Builder|Log whereModel($value)
 * @method static Builder|Log whereModelId($value)
 * @method static Builder|Log whereModels($value)
 * @method static Builder|Log whereUpdatedAt($value)
 * @method static Builder|Log whereUserId($value)
 */
class Log extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'model',
        'model_id',
        'action',
        'message',
        'models',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'models' => 'array',
    ];

    /**
     * Get the user record associated with this log
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @param string $model
     * @param int $id
     * @param User|null $user limit the log entries to the actions of this user
     *
     * @return Builder
     */
    public static function entries(string $model, int $id, ?User $user = null): Builder
    {
        $query = self::with('user' )
            ->where('model', $model )
            ->where('model_id', $id );

        if ($user) {
            $query->where('user_id', $user->id );
        }

        return $query->orderByDesc('id' );
    }

    /**
     * @param  $query
     * @param array $filters
     *
     * @throws \Exception
     */
    public function scopeFilter( Builder $query, array $filters ): void
    {
        $query->when(
            $filters['search'] ?? null,
            function (Builder $query, $search) {
                $query->whereRaw("( message like ? or models like ? )", ["%{$search}%", "%{$search}%"]);
            }
        )->when(
            $filters['model'] ?? null,
            function (Builder $query, $search) {
                $query->where('model', $search);
            }
        )->when(
            $filters['model_id'] ?? null,
            function (Builder $query, $search) {
                $query->where('model_id', (int)$search);
            }
        )->when(
            $filters['user_id'] ?? null,
            function (Builder $query, $search) {
                $query->where('user_id', (int)$search);
            }
        );
    }
}