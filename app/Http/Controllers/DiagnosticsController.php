<?php

namespace IXP\Http\Controllers;

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

use Illuminate\Http\Request;
use Illuminate\View\View;
use IXP\Models\Customer;
use IXP\Services\Diagnostics;


/**
 * .env file configurator Controller
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DiagnosticsController extends Controller
{

    /**
     */
    public function run( Customer $customer, Diagnostics $diagnostics ): View
    {
        $diagnosticsResult = $diagnostics->runCustomerDiagnostics($customer);
        $customerData = $customer->toArray();
        info("diagnostics result: " . var_export($diagnosticsResult, true));

        return view( 'diagnostics.customer-detail')->with([
            "customer" => $customerData,
            "results" => $diagnosticsResult,
        ]);
    }


}
