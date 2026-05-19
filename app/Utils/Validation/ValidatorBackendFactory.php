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

namespace IXP\Utils\Validation;

use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Process;
use IXP\Contracts\Validation\ValidationRunner;
use IXP\Contracts\Validation\Validator;
use Ramsey\Uuid\Uuid;

/**
 * ValidatorBackendFactory - prepare a list of backends for Validators
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class ValidatorBackendFactory
{
    /**
     * Find all validators and build a list of backends to run them against.
     * @param string $testUUID
     * @return ValidationRunner[]
     * @throws \ReflectionException
     */
    public function buildBackends( string $testUUID ): array
    {
        $backends = [];
        foreach( glob( app_path( 'Utils/Validation/Validators/*Validator.php' ) ) as $filename) {
            $validatorClass = "\\IXP\\Utils\\Validation\\Validators\\" . basename( $filename, ".php" );

            // don't run anything that doesn't implement ValidatorInterface
            $reflectionClass = new \ReflectionClass( $validatorClass );
            if ( !( $reflectionClass->implementsInterface( Validator::class ) ) ) {
                continue;
            }

            $backends[] = new Backend( $testUUID, $validatorClass );
        }
        return $backends;
    }
}