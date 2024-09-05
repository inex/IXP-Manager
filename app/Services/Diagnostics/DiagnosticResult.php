<?php

namespace IXP\Services\Diagnostics;

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

use IXP\Models\User;

/**
 * Diagnostics Service - Result Container
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Laszlo Kiss      <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class DiagnosticResult
{
    public const int TYPE_FATAL = 1000;
    public const int TYPE_ERROR = 900;
    public const int TYPE_WARN  = 800;
    public const int TYPE_GOOD  = 600;
    public const int TYPE_INFO  = 500;
    public const int TYPE_DEBUG = 200;
    public const int TYPE_TRACE = 100;

    public static array $RESULT_TYPES_TEXT = [
        self::TYPE_FATAL   => 'Fatal',
        self::TYPE_ERROR   => 'Error',
        self::TYPE_WARN    => 'Warning',
        self::TYPE_INFO    => 'Info',
        self::TYPE_GOOD    => 'Good',
        self::TYPE_DEBUG   => 'Debug',
        self::TYPE_TRACE   => 'Trace',
    ];

    public static array $RESULT_TYPES_ICON_STYLE = [
        self::TYPE_FATAL   => 'tw-bg-red-50 tw-text-red-700 tw-ring-red-600/10',
        self::TYPE_ERROR   => 'tw-bg-pink-50 tw-text-pink-700 tw-ring-pink-700/10',
        self::TYPE_WARN    => 'tw-bg-yellow-50 -text-yellow-800 tw-ring-yellow-600/20',
        self::TYPE_INFO    => 'tw-bg-blue-50 tw-text-blue-700 tw-ring-blue-700/10',
        self::TYPE_GOOD    => 'tw-bg-green-50 tw-text-green-700 tw-ring-green-600/20',
        self::TYPE_DEBUG   => 'tw-bg-gray-50 tw-text-gray-600 tw-ring-gray-500/10',
        self::TYPE_TRACE   => 'tw-bg-gray-100 tw-text-gray-800 tw-ring-gray-800/10',
    ];


    public function __construct(
        public string $name,
        public int $result,
        public string $narrative,
        public bool $auth = User::AUTH_SUPERUSER,    // whether the diagnostic result should be visible to the member
        public ?string $infoBadge = null,
    ) { }

    public function iconStyle(): string {
        return "tw-inline-flex tw-items-center tw-rounded-md tw-ml-2 tw-px-2 tw-py-1 tw-text-xs tw-font-medium "
            . self::$RESULT_TYPES_ICON_STYLE[$this->result]
            . " tw-ring-1 tw-ring-inset";
    }

    public function result(): string {
        return self::$RESULT_TYPES_TEXT[$this->result];
    }


    public function badge(): string {
        return "<span class=\"" . $this->iconStyle() . "\">" . $this->result() . "</span>";
    }


}