<?php declare(strict_types=1);

namespace IXP\Utils;

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

use Laravel\Horizon\Contracts\MasterSupervisorRepository;

/**
 * Class Horizon
 *
 * @package App\Utils
 */
class Horizon
{
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_PAUSED   = 'paused';
    public const STATUS_RUNNING  = 'running';

    /**
     * Returns with Horizon's current status: 'inactive', 'paused' or 'running'
     *
     * @param void
     * @return string
     */
    public static function status()
    {
        if( !$masters = app(MasterSupervisorRepository::class )->all() ) {
            return self::STATUS_INACTIVE;
        }
        return collect( $masters )->contains( function( $master ) {
            return $master->status === self::STATUS_PAUSED;
        } ) ? self::STATUS_PAUSED : self::STATUS_RUNNING;
    }
}