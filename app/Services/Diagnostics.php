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

use IXP\Models\{
    Customer,
    VirtualInterface,
};

use IXP\Exceptions\GeneralException;
use IXP\Services\Diagnostics\Suites\CustomerDiagnosticSuite;
use IXP\Services\Diagnostics\Suites\IrrdbDiagnosticSuite;
use IXP\Services\Diagnostics\Suites\PhysicalInterfaceDiagnosticSuite;
use IXP\Services\Diagnostics\Suites\VirtualInterfaceDiagnosticSuite;

/**
 * Diagnostics Service
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Laszlo Kiss      <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Diagnostics
{

    /**
     * @param Customer $customer
     * @return array
     * @throws GeneralException
     */
    public function getCustomerDiagnostics(Customer $customer): array
    {
        $d = new CustomerDiagnosticSuite( $customer );
        return [ 'suite' => $d, 'results' => $d->run()->results() ];
    }

    /**
     * @param Customer $customer
     * @return array
     */
    public function getCustomerIrrdbDiagnostics(Customer $customer): array
    {
        $d = new IrrdbDiagnosticSuite( $customer );
        return [ 'suite' => $d, 'results' => $d->run()->results() ];
    }


    public function getVirtualInterfaceDiagnostics( VirtualInterface $vi ): array
    {
        $d = new VirtualInterfaceDiagnosticSuite( $vi );
        return [ 'suite' => $d, 'results' => $d->run()->results() ];
    }

    public function getPhysicalInterfaceDiagnostics(VirtualInterface $virtualInterface): array
    {
        return ( new PhysicalInterfaceDiagnosticSuite( $virtualInterface ) )->run()->results();
    }

}