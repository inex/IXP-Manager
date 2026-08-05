<?php

namespace IXP\Support\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use Exception;

/**
 * Raised when an address cannot be allocated: the pool is exhausted, the address is not in
 * this VLAN's pool, or it is already assigned.
 *
 * Every case is the caller's problem to fix, so controllers translate this into a 422 rather
 * than letting it become a 500.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Support\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IpAllocationException extends Exception
{
}
