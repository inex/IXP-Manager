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

namespace IXP\Utils\Validation\Validators;

use Illuminate\Http\Client\ConnectionException;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class SecurityValidator implements Validator
{

    #[\Override]
    public function getName(): string
    {
        return "Security settings";
    }

    #[\Override]
    public function getDescription(): string
    {
        return "Check security settings are properly configured.";
    }

    #[\Override]
    public function getPriority(): int
    {
        return 10;
    }

    #[\Override]
    public function run( ValidationBackend $backend ): void
    {
        if (config('ixp_api.unsecured_api_access')) {
            $backend->error("Unsecured API Access is enabled - this is strongly discouraged, and support will be removed in a future release. Update any dependent software and disable this ASAP.")
            ->withDocsPath( "install/security/" );
        }

        if (config('ixp_api.allow_apikeys_get_parameter')) {
            $backend->error("Passing API Keys as a GET parameter is enabled - this is strongly discouraged, and support for this will be removed in a future release. Update any dependent software and disable this ASAP.");
        }

        try {
            $url = url("/login");
            $loginResponse = \Http::get($url);
            $headers = $loginResponse->headers();
            $hsts = $headers['Strict-Transport-Security'][0] ?? null;
            if (is_null($hsts)) {
                $backend->error("Your server is missing the Strict-Transport-Security header.");
            }

            $frameDeny = $headers['X-Frame-Options'][0] ?? null;
            if (is_null($frameDeny)) {
                $backend->error("Your server is missing the X-Frame-Options header.");
            }

        } catch (ConnectionException $e) {
            $backend->warning("Failed to load IXP Manager login page for HTTP check. This is to be expected in some configurations.");
        }
    }
}