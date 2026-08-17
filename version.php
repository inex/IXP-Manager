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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

#
# Basic version information
#

define( 'APPLICATION_VERSION', '7.4.0' );
define( 'APPLICATION_VERDATE', '2026081700' );
define( 'DOCUMENTATION_VERSION', '7.4' );
define( 'APPLICATION_MANIFEST', [
    'php_version' => [
        'min' => '8.4.0',
        'recommended' => '8.4.',
        'max' => null,
    ],
    'mysql_version' => [
        'min' => '8.0.0',
        'recommended' => '8.0.',
        'max' => null,
    ],
    'laravel_required_extensions' => [
        'filter', 'hash', 'mbstring', 'openssl', 'pcre', 'pdo', 'pdo_mysql', 'session', 'tokenizer', 'xml',
    ],
]);
