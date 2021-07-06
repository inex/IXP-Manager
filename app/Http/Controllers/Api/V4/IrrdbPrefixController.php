<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Http\{
    JsonResponse,
    Request
};

use IXP\Models\{
    Customer,
    IrrdbPrefix
};

/**
 * IrrdbPrefix API v4 Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IrrdbPrefixController extends Controller
{
    /**
     * Get IrrdbPrefixes depending on the customer and Protocol
     *
     * @param   Request $r instance of the current HTTP request
     *
     * @return  JsonResponse
     */
    public function byCustomerAndProtocol( Request $r ): JsonResponse
    {
        $cust = Customer::find( $r->custid );

        if( !in_array( $protocol = $r->protocol, [ 4, 6 ], false ) ) {
            abort( 404 );
        }

        $prefixes = false;

        if( $cust->maxprefixes < 2000 ){
            $prefixes = IrrdbPrefix::where( 'customer_id', $cust->id )->where( 'protocol', $protocol )->get()->toArray();
        }

        return response()->json( [ 'prefixes' => $prefixes ] );
    }
}