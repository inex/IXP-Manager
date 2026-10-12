<?php

declare(strict_types=1);

namespace IXP\Providers;

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

use Illuminate\Contracts\Foundation\Application;
use IXP\Contracts\IrrQuerier;
use IXP\Exceptions\ConfigurationException;
use IXP\Utils\Bgpq3;
use IXP\Utils\Bgpq4;

use Illuminate\Support\ServiceProvider;

/**
 * IrrQuerier Service Provider
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IrrQuerierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    #[\Override]
    public function register(): void
    {
        $this->app->bind( IrrQuerier::class, function( Application $app ) {
            return match ( config( 'ixp.irrdb.utility' ) ) {
                'bgpq3' => $app->make( Bgpq3::class ),
                'bgpq4' => $app->make( Bgpq4::class ),
                default => throw new ConfigurationException( 'Unknown IrrQuerier utility - check IXP_IRRDB_UTILITY' ),
            };
        });
    }
}