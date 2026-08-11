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

namespace IXP\Services\Validation;

use IXP\Contracts\Validation\Validator;

/**
 * ValidationRunnerFactory - prepare a list of backends for Validators
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class ValidationRunnerFactory
{
    /**
     * Find all validators and build a list of ValidationRunners to run them against.
     * @return Backend[]
     * @throws \ReflectionException
     */
    public function getRunners(): array
    {
        $runners = [];
        foreach( glob( app_path( 'Services/Validation/Validators/*.php' ) ) as $filename) {
            $validatorClass = "\\IXP\\Services\\Validation\\Validators\\" . basename( $filename, ".php" );
            // don't run anything that doesn't implement ValidatorInterface
            $reflectionClass = new \ReflectionClass( $validatorClass );
            if ( !( $reflectionClass->implementsInterface( Validator::class ) ) ) {
                continue;
            }

            $runners[] = new Backend( $validatorClass );
        }

        return $runners;
    }
}