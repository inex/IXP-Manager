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

namespace IXP\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Process;
use Illuminate\Translation\PotentiallyTranslatedString;

class ExistsAndExecutable implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    #[\Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ( file_exists($value) ) {
            if ( !( is_file( $value ) && is_executable( $value ) ) ) {
                $fail('The :attribute path must refer to an executable file');
            }
            // ok!
        } else if ( preg_match('/^[A-Za-z0-9._-]+$/', $value)) {
            // see if we can find a location in the command line $PATH
            $paths = explode( PATH_SEPARATOR, getenv( "PATH" ) );

            $located = false;
            foreach ( $paths as $pathElem ) {
                if ( is_executable( rtrim( $pathElem, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $value ) ) {
                    $located = true;
                    break;
                }
            }

            if ( !$located ) {
                $fail('The :attribute program must refer to an executable present in $PATH, or using it\'s absolute path on the filesystem.');
            }
            // ok
        } else {
            $fail('The :attribute program must refer to an executable present in $PATH, or using it\'s absolute path on the filesystem.');
        }
    }
}
