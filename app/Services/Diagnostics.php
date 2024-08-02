<?php

namespace IXP\Services;

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

use Cache, Config;

use IXP\Exceptions\Services\Grapher\{
        BadBackendException,
        ConfigurationException,
        GraphCannotBeProcessedException
};

use Closure;
use Illuminate\Contracts\Cache\Repository;
use IXP\Contracts\Grapher\Backend as BackendContract;

use IXP\Models\{Customer,
    CoreBundle,
    Infrastructure,
    Location,
    PhysicalInterface,
    VirtualInterface,
    VlanInterface,
    Switcher,
    Vlan};

use IXP\Services\Diagnostics\Suites\CustomerDiagnosticSuite;
use IXP\Services\Diagnostics\Suites\VirtualInterfaceDiagnosticSuite;
use IXP\Services\Grapher\Graph;

use IXP\Services\Grapher\Graph\{
    IXP               as IXPGraph,
    Infrastructure    as InfrastructureGraph,
    Vlan              as VlanGraph,
    Location          as LocationGraph,
    Switcher          as SwitchGraph,
    Trunk             as TrunkGraph,
    CoreBundle        as CoreBundleGraph,
    PhysicalInterface as PhysIntGraph,  // member physical port
    VirtualInterface  as VirtIntGraph,  // member LAG
    Customer          as CustomerGraph, // member agg over all physical ports
    VlanInterface     as VlanIntGraph,  // member VLAN interface
    P2p               as P2pGraph,
    Latency           as LatencyGraph
};

/**
 * Diagnostics Service
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Laszlo Kiss      <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Diagnostics
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    public function getStatusDiagnostics(Customer $customer): array
    {
        return ( new CustomerDiagnosticSuite( $customer ) )->run()->results();
    }

    public function getVirtualInterfaceDiagnostics(Customer $customer): array
    {
        return ( new VirtualInterfaceDiagnosticSuite( $customer ) )->run()->results();
    }

}