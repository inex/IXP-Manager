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

namespace Tests\Trait;

use Illuminate\Support\Facades\Config;

trait SetsPasswordPolicy
{
    protected function setPasswordPolicy( int  $minLength = 8, int $maxLength = 72, bool $requireMixedCase = false,
                                          bool $requireNumbers = false, bool $requireSymbols = false, bool $notCompromised = false ): static
    {
        Config::set('password_policy', [
            'minimum_length'  => $minLength,
            'maximum_length'  => $maxLength,
            'require_mixed_case' => $requireMixedCase,
            'require_numbers' => $requireNumbers,
            'require_symbols' => $requireSymbols,
            'not_compromised' => $notCompromised,
        ]);
        return $this;
    }
}