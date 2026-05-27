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

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class UsesCacheValidator implements Validator
//class UsesCacheValidator
{
    public function getName(): string
    {
        return "Cache setup validator";
    }

    public function getDescription(): string
    {
        return "Checks that cache configuration is working.";
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function run( ValidationBackend $backend ): void
    {
        $backend->software('memcached', '1.0');

        $key = 'validator:test-key';
        $randomInt = random_int(1, 1000);
        \Cache::set($key, $randomInt);
        if (!\Cache::has($key)) {
            $backend->error("Cache not working - recently set key was not found");
        } else if ($randomInt != \Cache::get($key)) {
            $backend->error("Cache not working - retrieved value didn't match expected value");
        } else {
            $backend->info("Tests passed");
        }
    }
}
