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

    public const int TYPE_OKAY    = 1;
    public const int TYPE_WARNING = 2;
    public const int TYPE_ERROR   = 3;
    public const int TYPE_INFO    = 4;
    public const int TYPE_UNKNOWN = 5;

    public static $RESULT_TYPES_TEXT = [
        self::TYPE_OKAY          => 'Okay',
        self::TYPE_WARNING       => 'Warning',
        self::TYPE_ERROR         => 'Error',
        self::TYPE_INFO          => 'Info',
        self::TYPE_UNKNOWN       => 'Unknown',
    ];

    public static $RESULT_TYPES_ICON = [
        self::TYPE_OKAY          => 'fa-check',
//        self::TYPE_WARNING       => 'Warning',
//        self::TYPE_ERROR         => 'Error',
//        self::TYPE_INFO          => 'Info',
//        self::TYPE_UNKNOWN       => 'Unknown',
    ];





    public function __construct(
        protected string $name,
        protected int $result,
        protected string $narrative,
    ) { }


}