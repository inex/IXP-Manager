<?php

namespace IXP\Traits;

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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

use IXP\Models\{
    User,
    Log
};


/**
 * Observable trait
 *
 * @package App\Traits
 */
trait Observable
{
    public static function bootObservable(): void
    {
        if( !config( 'ixp_fe.frontend.disabled.logs' ) && !App::runningInConsole() ){
            static::saved(
                function( Model $model ) {
                    // create or update?
                    if( $model->wasRecentlyCreated ) {
                        static::logChange( $model, Log::ACTION_CREATED );
                    } elseif ( $model->getChanges() ) {
                        // Check if with have field with log exception
                        if( !( isset( $model->field_log_exception ) && empty( array_diff( array_values( $model->field_log_exception ), array_keys( $model->getChanges() ) ) ) ) ){
                            static::logChange( $model, Log::ACTION_UPDATED );
                        }
                    }
                }
            );

            static::deleted(
                function( Model $model ) {
                    static::logChange( $model, Log::ACTION_DELETED );
                }
            );
        }
    }

    /**
     * saves the changes into the log table
     *
     * @param Model     $model
     * @param string    $action
     *
     * @return void
     */
    public static function logChange( Model $model, string $action ): void
    {
        Log::create(
            [
                'user_id'   => Auth::check() ? Auth::id() : null,
                'model'     => self::getClass(),
                'model_id'  => $model->id,
                'action'    => $action,
                'message'   => static::logSubject( $model ),
                'models'    => [
                    'new'       => $action !== Log::ACTION_DELETED ? $model->getAttributes()  : null,
                    'old'       => $action !== Log::ACTION_CREATED ? $model->getOriginal()    : null,
                    'changed'   => $action === Log::ACTION_UPDATED ? $model->getChanges()     : null,
                ]
            ]
        );
    }

    /**
     * @return string
     */
    public static function getClass(): string
    {
        return class_basename(static::class );
    }

    /**
     * represents the model's attributes as a string
     *
     * @param  Model  $model
     *
     * @return string
     *
     * @throws \JsonException
     *
     * @see logImplodeAssoc
     */
    public static function logSubject( Model $model ): string
    {
        return static::logImplodeAssoc( $model->attributesToArray() );
    }

    /**
     * represents the array as a string
     *
     * @param  array  $attrs
     *
     * @return string
     *
     * @throws \JsonException
     */
    public static function logImplodeAssoc(array $attrs): string
    {
        $l = '';

        foreach ($attrs as $k => $v) {
            if( is_array( $v ) ){
                $v = json_encode($v, JSON_THROW_ON_ERROR);
            }
            $l .= "{ $k => $v } ";
        }

        return trim( $l );
    }

    /**
     * @param User|null $user limit the log entries to the actions of this user
     *
     * @return Builder
     */
    public function getLogEntries( ?User $user = null ): Builder
    {
        return Log::entries( self::getClass(), $this->id, $user );
    }
}