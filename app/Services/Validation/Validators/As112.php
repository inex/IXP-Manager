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

namespace IXP\Services\Validation\Validators;

use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Models\Router;

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class As112 implements Validator
{

    #[\Override]
    public function getName(): string
    {
        return "AS112 validator";
    }

    #[\Override]
    public function getDescription(): string
    {
        return "Check AS112 feature settings";
    }

    #[\Override]
    public function getPriority(): int
    {
        return 50;
    }

    #[\Override]
    public function run( ValidationBackend $backend ): void
    {
        if (! config ( 'ixp.as112.ui_active' ) ) {
            return;
        }

        $numAs112Routers = Router::whereType(Router::TYPE_AS112)->count();
        if ($numAs112Routers === 0) {
            $backend->error("AS112 enabled but no AS112 routers setup")
                ->withDocsPath("features/as112/");
        } else {
            $backend->info("Found " . $numAs112Routers . " AS112 routers");
        }
    }
}