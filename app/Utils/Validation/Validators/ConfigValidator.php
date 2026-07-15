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

use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use Ramsey\Uuid\Uuid;

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class ConfigValidator implements Validator
{
    public function getName(): string
    {
        return "Configuration validation";
    }

    public function getDescription(): string
    {
        return "Perform checks of the IXP Manager configuration";
    }

    public function getPriority(): int
    {
        return 20;
    }

    public function run( ValidationBackend $backend ): void
    {
        $backend->info("Default cache driver is " . config('cache.default') );

        try {
            $key = Uuid::uuid4()->toString();
            $value = Uuid::uuid4()->toString();
            \Cache::put("validation:random:$key", $value, 60);
            $retrieved = \Cache::get("validation:random:$key");
            if ($value !== $retrieved) {
                $backend->error("Cache didn't return the same value that was written");
            }
        } catch ( \Exception $e ) {
            $backend->error("The cache write test encountered an error: " . $e->getMessage());
        }
    }
}