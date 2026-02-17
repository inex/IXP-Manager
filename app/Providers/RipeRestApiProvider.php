<?php

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Support\ServiceProvider;

use Illuminate\Contracts\Support\DeferrableProvider;

use IXP\Services\RipeRestApi;

/**
 * RIPE REST API Service Provider
 *
 * @see https://docs.ixpmanager.org/latest/features/rir-objects/
 * @author      Barry O'Donovan     <barry@opensolutions.ie>
 * @copyright   Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RipeRestApiProvider extends ServiceProvider implements DeferrableProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void {}

    /**
     * Register the application services.
     *
     * @return void
     */
    #[\Override]
    public function register(): void
    {
        $this->app->bind( RipeRestApi::class, function( $app ) {
            $ripe = new RipeRestApi();
            $ripe->checkConfiguration();
            return $ripe;
        });
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [RipeRestApi::class];
    }
}