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

class NoIllegalChars implements ValidationRule
{
    #[\Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            $fail('The :attribute must be a string.');
            return;
        }

        // Ban ", \r, \n, \x00, and \
        if (preg_match('/["\r\n\x00\\\\]/', $value)) {
            $fail('The :attribute contains invalid characters (double quotes, black-slashes, and new lines are not allowed).');
            return;
        }
    }
}