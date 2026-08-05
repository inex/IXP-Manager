<?php

namespace IXP\Http\Controllers\Api\V4\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use Illuminate\Http\JsonResponse;

use IXP\Http\Controllers\Api\V4\Controller;

use Auth;

/**
 * Provisioning API - Ping Controller
 *
 * A health / connectivity check for the provisioning API.
 *
 * It exists so that an operator can verify the whole authentication chain - API key,
 * superuser privilege and JSON content negotiation - without creating anything.
 *
 * @author     KleyReX
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PingController extends Controller
{
    /**
     * Confirm that the provisioning API is reachable and the API key is accepted.
     *
     * Note that the user is resolved from the API key by the `apiauth` middleware, which
     * logs the key's owner in via Auth::onceUsingId(). Auth::user() is therefore populated
     * exactly as it would be for a browser session.
     *
     * @return JsonResponse
     */
    public function ping(): JsonResponse
    {
        return response()->json( [
            'pong'      => true,
            'user'      => Auth::user()->username,
            'version'   => APPLICATION_VERSION,
        ] );
    }
}
