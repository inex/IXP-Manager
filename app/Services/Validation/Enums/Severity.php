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

namespace IXP\Services\Validation\Enums;

/**
 * Define possible statuses of validation results
 *
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
enum Severity: string
{
    case Debug = 'debug';
    case Info = 'info';
    case Suggestion = 'suggestion';
    case Warning = 'warning';
    case Error = 'error';

    /**
     * Establish a ranking for severity levels so they can be compared
     * @return int
     */
    public function rank(): int
    {
        return match( $this ) {
            self::Debug => 10,
            self::Info => 20,
            self::Suggestion => 30,
            self::Warning => 40,
            self::Error => 50,
        };
    }

    public function isAtLeast(self $other): bool
    {
        return $this->rank() >= $other->rank();
    }

    /**
     * Return an array of the values of the enum
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(
            fn( Severity $severity): string => $severity->value,
            Severity::cases()
        );
    }
}