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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace IXP\Http\Controllers\Api\V4;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use IXP\Utils\Validation\JobState;

class ValidationController
{
    /**
     * Load JobState from cache using it's ID and return the result as JSON
     */
    public function apiResults(string $id): JsonResponse
    {
        if ( !( $jobState = Cache::store('file')->get( JobState::getCacheKey( $id ) ) ) ) {
            return response()->json( [], 404 );
        }

        return response()->json( $jobState );
    }
}