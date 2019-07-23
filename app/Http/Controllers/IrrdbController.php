<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth;
use Cache, D2EM;

use Entities\{
    IrrdbAsn    as IrrdbAsnEntity,
    IrrdbPrefix as IrrdbPrefixEntity
};

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use IXP\Jobs\UpdateIrrdb;

use IXP\Http\Requests\Irrdb as IrrdbRequest;

use IXP\Models\Customer as CustomerModel;


/**
 * PatchPanel Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 *
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IrrdbController extends Controller
{
    /**
     * Display the list of IRRDB (ASN/Prefix), (IPv4/IPv6) for a customer
     *
     * @param IrrdbRequest      $r
     * @param CustomerModel     $customer
     * @param String            $type
     * @param Integer           $protocol
     *
     * @return View
     */
    public function list( IrrdbRequest $r, CustomerModel $customer, $type, $protocol ) : View {

        $entity = $type == "asn" ? IrrdbAsnEntity::class : IrrdbPrefixEntity::class;

        /** @var  $entity IrrdbAsnEntity::class|IrrdbPrefixEntity::class */
        $irrdbList = D2EM::getRepository( $entity )->getForCustomerAndProtocol( $customer->getDoctrineObject(), $protocol );

        // are we busting the cache?
        if( Auth::user()->isSuperUser() && $r->input( "reset_cache" ) === "1" ) {
            Cache::forget('updated-irrdb-' . $type . '-' . $customer->id );
        }

        return view( 'irrdb/list' )->with([
            'irrdbList'         => $irrdbList,
            'type'              => $type,
            'customer'          => $customer,
            'protocol'          => $protocol,
            'updatingIrrdb'     => Cache::get( 'updating-irrdb-' . $type . '-' . $protocol . '-' . $customer->id, false ),
            'updatedIrrdb'      => Cache::get( 'updated-irrdb-'  . $type . '-' . $protocol . '-' . $customer->id, false ),
        ]);

    }

    /**
     * Update the list of IRRDB (ASN/Prefix) for a customer
     *
     * @param IrrdbRequest $r
     * @param CustomerModel $customer
     * @param String $type
     * @param Integer $protocol
     *
     * @return RedirectResponse
     */
    public function update( IrrdbRequest $r, CustomerModel $customer, $type, $protocol ) : RedirectResponse
    {
        // are we busting the cache?
        if( Auth::user()->isSuperUser() && $r->reset_cache === "1" ) {
            Cache::forget('updated-irrdb-' . $type . '-' . $protocol . '-' . $customer->id );
        }

        // get the status of the irrdb update function
        $updatedIrrdb = Cache::get( 'updated-irrdb-' . $type . '-' . $protocol . '-' . $customer->id, false );

        if( $updatedIrrdb === false ) {
            // no cached result so schedule a job to gather them:
            Cache::put( 'updating-irrdb-' . $type . '-' . $protocol . '-' . $customer->id, true, 3600 );
            UpdateIrrdb::dispatch( $customer, $type, $protocol );
        }

        return redirect( route( "irrdb@list", [ "customer" => $customer->id, "type" => $type , "protocol" => $protocol ] )  );
    }

}