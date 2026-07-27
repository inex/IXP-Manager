<?php
/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace IXP\Console\Commands\Customer;

use IXP\Console\Commands\Upgrade\MaxPrefixes_7_1_0;

/**
 * Tool to update customer global max prefixes based on values from
 * vlaninterfaces ipv4maxbgpprefix and ipv6maxbgpprefix settings.
 * The corresponding VLAN interfaces setting will then be cleared.
 *
 * Alias of `update:max-prefixes-7.1.0`
 */
class UpdateGlobalMaxPrefixes extends MaxPrefixes_7_1_0
{
    protected $signature = 'customer:update-global-max-prefixes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This tool guides an operator through updating a customers global max prefixes ' .
        'setting to reflect any per VLAN max prefixes overrides, and then clears the per VLAN max prefixes override.';
}