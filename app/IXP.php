<?php

namespace IXP;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

/**
 * IXP - top level class for constants, etc.
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class IXP
{
    public const int IPv4 = 4;
    public const int IPv6 = 6;

    public static array $PROTOCOLS = [
        self::IPv4 => 'IPv4',
        self::IPv6 => 'IPv6',
    ];

    public static function protocol( int $p ): string {
        return self::$PROTOCOLS[$p] ?? '';
    }

}