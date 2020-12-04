<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Jobs\FetchFilteredPrefixesForCustomer;

use Illuminate\Http\Request;

use Illuminate\View\View;

use IXP\Models\Customer;

/**
 * Filtered Prefixes Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class FilteredPrefixesController extends Controller
{
    /**
     * Get the list
     *
     * @param Request $r
     * @param Customer $customer
     *
     * @return View
     *
     * @throws
     */
    public function list( Request $r, Customer $customer ) : View
    {
        $this->authorize('view', $customer);

        // are we busting the cache?
        if( Auth::getUser()->isSuperUser() && $r->reset_cache === "1" ) {
            Cache::forget('filtered-prefixes-' . $customer->id );
        }

        // get the prefixes
        $filteredPrefixes = Cache::get( 'filtered-prefixes-' . $customer->id, false );

        if( $filteredPrefixes === false ) {
            // no cached result so schedule a job to gather them:
            //FetchFilteredPrefixesForCustomer::dispatch( $customer );

            // if we are using the sync queue runner, it will have completed
            $filteredPrefixes = Cache::get( 'filtered-prefixes-' . $customer->id, false );
        }

        return view( 'filtered-prefixes.view' )->with([
            'customer'         => $customer,
            'filteredPrefixes' => $filteredPrefixes,
        ]);
    }
}