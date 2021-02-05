<?php

namespace IXP\Http\Controllers;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth, Cache;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use IXP\Jobs\UpdateIrrdb;

use IXP\Http\Requests\Irrdb as IrrdbRequest;

use IXP\Models\{
    Aggregators\IrrdbAggregator,
    Customer
};

/**
 * Irrdb Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IrrdbController extends Controller
{
    /**
     * Display the list of IRRDB (ASN/Prefix), (IPv4/IPv6) for a customer
     *
     * @param IrrdbRequest      $r
     * @param Customer          $cust
     * @param String            $type
     * @param int               $protocol
     *
     * @return View
     */
    public function list( IrrdbRequest $r, Customer $cust, string $type, int $protocol ) : View
    {
        $irrdbList = IrrdbAggregator::forCustomerAndProtocol( $cust->id, $protocol, $type );

        // are we busting the cache?
        if( $r->reset_cache === "1"  && Auth::getUser()->isSuperUser() ) {
            Cache::forget('updated-irrdb-' . $type . '-' . $cust->id );
        }

        return view( 'irrdb/list' )->with([
            'irrdbList'         => $irrdbList,
            'type'              => $type,
            'customer'          => $cust,
            'protocol'          => $protocol,
            'updatingIrrdb'     => Cache::get( 'updating-irrdb-' . $type . '-' . $protocol . '-' . $cust->id, false ),
            'updatedIrrdb'      => Cache::get( 'updated-irrdb-'  . $type . '-' . $protocol . '-' . $cust->id, false ),
        ]);
    }

    /**
     * Update the list of IRRDB (ASN/Prefix) for a customer
     *
     * @param IrrdbRequest      $r
     * @param Customer          $cust
     * @param String            $type
     * @param int               $protocol
     *
     * @return RedirectResponse
     */
    public function update( IrrdbRequest $r, Customer $cust, string $type, int $protocol ) : RedirectResponse
    {
        // are we busting the cache?
        if( $r->reset_cache === "1" && Auth::getUser()->isSuperUser() ) {
            Cache::forget('updated-irrdb-' . $type . '-' . $protocol . '-' . $cust->id );
        }

        // get the status of the irrdb update function
        $updatedIrrdb = Cache::get( 'updated-irrdb-' . $type . '-' . $protocol . '-' . $cust->id, false );

        if( $updatedIrrdb === false ) {
            // no cached result so schedule a job to gather them:
            Cache::put( 'updating-irrdb-' . $type . '-' . $protocol . '-' . $cust->id, true, 3600 );
            UpdateIrrdb::dispatch( $cust, $type, $protocol );
        }

        return redirect( route( "irrdb@list", [ "cust" => $cust->id, "type" => $type , "protocol" => $protocol ] )  );
    }
}